<?php
namespace APF\tools\mail;

use APF\core\pagecontroller\APFObject;
use InvalidArgumentException;

/**
 * Creates and initializes an e-mail Message from an ini configuration file. The configuration files is
 * expected to reside under <em>APF\tools\mail</em> namespace with name <em>mailsender.ini</em>. The file
 * is supposed to containing the following configuration directives:
 * <code>
 * [Section-Name]
 * Mail.SenderName = "Sender name"
 * Mail.SenderEMail = "sender@e-mail.com"
 * Mail.ReturnPath = "return@e-mail.com"
 * Mail.ContentType = "text/plain; charset=utf-8"
 * </code>
 * The builder can be used to mime the initialization of the <em>mailSender</em> component removed in version 3.3.
 * <p/>
 * Usage:
 * <code>
 * $subject = ...
 * $content = ...
 *
 * $builder = $this->getServiceObject(MessageBuilder::class);
 * $message = $builder->createMessage('<Section-Name>', $subject, $content);
 * ...
 * $message->send();
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.01.2017 (ID#251: added builder to allow backward-compatible configuration of e-mail message)<br />
 */
class MessageBuilder extends APFObject {

   /**
    * Creates a Message from a given configuration file <em>mailsender.ini</em> located under namespace
    * APF\tools\mail.
    *
    * @param string $section The name of the configuration section of the <em>mailsender.ini</em> file to construct the message.
    * @param string $subject The subject line of the message.
    * @param string $content The content (i.e. body) of the message.
    * @return Message The message to create and initialize.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.01.2017 (ID#251: added builder to allow backward-compatible configuration of e-mail message)<br />
    */
   public function createMessage($section, $subject, $content) {

      $config = $this->getConfiguration('APF\tools\mail', 'mailsender.ini');

      if (!$config->hasSection($section)) {
         throw new InvalidArgumentException('[MessageBuilder::createMessage()] Section "' . $section
               . '" is not present within the "mailsender.ini" configuration!');
      }

      $section = $config->getSection($section . '.Mail');

      $sender = new Recipient($section->getValue('SenderName'), $section->getValue('SenderEMail'));

      $message = new Message($sender, $subject, $content);

      $message->setContentType($section->getValue('ContentType'));
      $message->setReturnPath(new Recipient(null, $section->getValue('ReturnPath')));

      return $message;
   }

}
