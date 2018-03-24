<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
 *
 * The APF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The APF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 * -->
 */
namespace APF\modules\contact\biz;

use APF\core\configuration\ConfigurationException;
use APF\core\http\mixins\GetRequestResponse;
use APF\core\loader\RootClassLoader;
use APF\core\pagecontroller\APFObject;
use APF\core\pagecontroller\IncludeException;
use APF\modules\contact\data\ContactMapper;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;
use APF\tools\mail\MessageBuilder;
use APF\tools\mail\MailAddress;

/**
 * Implements the business component for the contact form.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 03.06.2006<br />
 */
class ContactManager extends APFObject {

   use GetRequestResponse;

   /**
    * Sends the contact form and displays the thanks page.
    *
    * @param ContactFormData $formData The form's content.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 03.06.2006<br />
    * Version 0.2, 04.06.2006<br />
    * Version 0.3, 21.06.2006 (Now an additional mail is sent to the sender)<br />
    * Version 0.4, 09.03.2007<br />
    * Version 0.5, 31.03.2007<br />
    * Version 0.6, 04.01.2008 (Corrected url generating for non-rewrite urls)<br />
    */
   public function sendContactForm(ContactFormData $formData) {

      $recipient = $this->getMapper()->loadRecipientById($formData->getRecipientId());

      // send mail to notify the recipient
      $content = $this->getNotificationText(
            [
                  'sender-name' => $formData->getSenderName(),
                  'sender-email' => $formData->getSenderEmail(),
                  'sender-subject' => $formData->getSubject(),
                  'sender-message' => $formData->getMessage(),
                  'recipient-name' => $recipient->getName(),
                  'recipient-email' => $recipient->getEmailAddress()
            ]
      );

      /* @var $builder MessageBuilder */
      $builder = $this->getServiceObject(MessageBuilder::class);
      $message = $builder->createMessage('ContactForm', $formData->getSubject(), $content);

      $message->addRecipient(new MailAddress($recipient->getName(), $recipient->getEmailAddress()));

      $message->send();

      // ---------------------------------------------------------------------------------------------------------------

      // send mail to notify the sender
      $content = $this->getConfirmationText(
            [
                  'sender-name' => $formData->getSenderName(),
                  'sender-email' => $formData->getSenderEmail(),
                  'sender-subject' => $formData->getSubject(),
                  'sender-message' => $formData->getMessage(),
                  'recipient-name' => $recipient->getName(),
                  'recipient-email' => $recipient->getEmailAddress()
            ]
      );

      $message = $builder->createMessage('ContactForm', $formData->getSubject(), $content);

      $message->addRecipient(new MailAddress($formData->getSenderName(), $formData->getSenderEmail()));

      $message->send();

      // redirect to the thanks page to avoid F5 bugs!
      $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(['contactview' => 'thanks']));
      $this->getResponse()->forward($link);
   }

   /**
    * @return ContactMapper
    */
   private function getMapper() {
      /** @noinspection PhpIncompatibleReturnTypeInspection */
      // PHP does not allow generic interface definition, so returning APFService would result in an IDE warning.
      return $this->getServiceObject(ContactMapper::class);
   }

   /**
    * Allows you to set these place holders (including the brackets!) within your text:
    * <ul>
    * <li>{sender-name}</li>
    * <li>{sender-email}</li>
    * <li>{sender-subject}</li>
    * <li>{sender-message}</li>
    * <li>{recipient-name}</li>
    * <li>{recipient-email}</li>
    * </ul>
    *
    * @param array $values An associative array of place holders and their value to be included within the text.
    *
    * @return string The notification text sent to the contact person to inform about the complaint.
    * @throws ConfigurationException In case the language configuration section is missing.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.10.2011<br />
    */
   private function getNotificationText(array $values = []) {

      $config = $this->getConfiguration('APF\modules\contact', 'mail_templates.ini');

      if (!$config->hasSection($this->getLanguage())) {
         throw new ConfigurationException('Configuration section "' . $this->getLanguage() . '" is not present within '
               . 'the contact form module configuration loading the email templates. Please '
               . 'review your configuration!');
      }

      $section = $config->getSection($this->getLanguage())->getSection('notification');

      return $this->fillPlaceHolders(
            $this->getEmailTemplateContent(
                  $section->getValue('namespace'),
                  $section->getValue('template')
            ),
            $values
      );
   }

   /**
    * Fills the applied place holders within the given text.
    *
    * @param string $text The text to fill the place holders in.
    * @param array $values An associative array of place holders and their value to be included within the text.
    *
    * @return string The text with filled place holders.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.10.2011<br />
    */
   private function fillPlaceHolders($text, array $values = []) {
      foreach ($values as $key => $value) {
         $text = str_replace('{' . $key . '}', $value, $text);
      }

      return $text;
   }

   /**
    * Loads the email template regarding the configuration.
    *
    * @param string $namespace The namespace of the template.
    * @param string $template The name of the template.
    *
    * @return string The mail template content.
    * @throws IncludeException In case the template file cannot be loaded.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.10.2011<br />
    */
   private function getEmailTemplateContent($namespace, $template) {
      $loader = RootClassLoader::getLoaderByNamespace($namespace);
      $rootPath = $loader->getRootPath();
      $vendor = $loader->getVendorName();

      $fqNamespace = str_replace('\\', '/', str_replace($vendor . '\\', '', $namespace));

      $file = $rootPath . '/' . $fqNamespace . '/' . $template . '.html';

      if (file_exists($file)) {
         return file_get_contents($file);
      }
      throw new IncludeException('Email template file "' . $file . '" cannot be loaded. '
            . 'Please review your contact module configuration!');
   }

   /**
    * Allows you to set these place holders (including the brackets!) within your text:
    * <ul>
    * <li>{sender-name}</li>
    * <li>{sender-email}</li>
    * <li>{sender-subject}</li>
    * <li>{sender-message}</li>
    * <li>{recipient-name}</li>
    * <li>{recipient-email}</li>
    * </ul>
    *
    * @param array $values An associative array of place holders and their value to be included within the text.
    *
    * @return string The notification text sent to the originator to confirm the submission.
    * @throws ConfigurationException In case the language configuration section is missing.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.10.2011<br />
    */
   private function getConfirmationText(array $values = []) {
      $config = $this->getConfiguration('APF\modules\contact', 'mail_templates.ini');

      if (!$config->hasSection($this->getLanguage())) {
         throw new ConfigurationException('Configuration section "' . $this->getLanguage() . '" is not present within '
               . 'the contact form module configuration loading the email templates. Please '
               . 'review your configuration!');
      }

      $section = $config->getSection($this->getLanguage())->getSection('confirmation');

      return $this->fillPlaceHolders(
            $this->getEmailTemplateContent(
                  $section->getValue('namespace'),
                  $section->getValue('template')
            ),
            $values
      );
   }

   /**
    * Loads the configuration of the recipients.
    *
    * @return ContactFormRecipient[] The contact reasons.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 03.06.2006<br />
    * Version 0.2, 04.06.2006<br />
    */
   public function loadRecipients() {
      return $this->getMapper()->loadRecipients();
   }

}
