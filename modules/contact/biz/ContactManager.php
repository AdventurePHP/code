<?php
namespace APF\modules\kontakt4\biz;

/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * http://adventure-php-framework.org.
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
use APF\core\configuration\ConfigurationException;
use APF\core\loader\RootClassLoader;
use APF\core\pagecontroller\APFObject;
use APF\core\pagecontroller\IncludeException;
use APF\modules\kontakt4\biz\ContactFormData;
use APF\modules\kontakt4\biz\ContactFormRecipient;
use APF\modules\kontakt4\data\ContactMapper;
use APF\tools\link\LinkGenerator;
use APF\tools\http\HeaderManager;
use APF\tools\link\Url;
use APF\tools\mail\mailSender;

/**
 * @package APF\modules\contact\biz
 * @class ContactManager
 *
 * Implements the business component for the contact form.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 03.06.2006<br />
 */
class ContactManager extends APFObject {

   /**
    * @public
    *
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

      // set up the mail sender
      $mail = & $this->getAndInitServiceObject('APF\tools\mail\mailSender', 'ContactForm');
      /* @var $mail mailSender */

      $recipient = $this->getMapper()->loadRecipientPerId($formData->getRecipientId());
      /* @var $recipient ContactFormRecipient */

      $mail->setRecipient($recipient->getEmailAddress(), $recipient->getName());
      $mail->setContent(
         $this->getNotificationText(
            array(
               'sender-name' => $formData->getSenderName(),
               'sender-email' => $formData->getSenderEmail(),
               'sender-subject' => $formData->getSubject(),
               'sender-message' => $formData->getMessage(),
               'recipient-name' => $recipient->getName(),
               'recipient-email' => $recipient->getEmailAddress()
            )
         )
      );

      $mail->setSubject($formData->getSubject());

      // send mail to notify the recipient
      $mail->sendMail();

      $mail->clearRecipients();
      $mail->clearCCRecipients();
      $mail->clearContent();

      $mail->setRecipient($formData->getSenderEmail(), $formData->getSenderName());

      $mail->setContent(
         $this->getConfirmationText(
            array(
               'sender-name' => $formData->getSenderName(),
               'sender-email' => $formData->getSenderEmail(),
               'sender-subject' => $formData->getSubject(),
               'sender-message' => $formData->getMessage(),
               'recipient-name' => $recipient->getName(),
               'recipient-email' => $recipient->getEmailAddress()
            )
         )
      );

      $mail->setSubject($formData->getSubject());

      // send mail to notify the sender
      $mail->sendMail();

      // redirect to the thanks page to avoid F5 bugs!
      $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array('contactview' => 'thanks')));
      HeaderManager::forward($link);
   }

   /**
    * @public
    *
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

   /**
    * @return ContactMapper
    */
   private function &getMapper() {
      return $this->getServiceObject('APF\modules\contact\data\ContactMapper');
   }

   /**
    * @private
    *
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
    * @return string The notification text sent to the contact person to inform about the complaint.
    * @throws ConfigurationException In case the language configuration section is missing.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.10.2011<br />
    */
   private function getNotificationText(array $values = array()) {

      $config = $this->getConfiguration('APF\modules\contact', 'mail_templates.ini');
      $section = $config->getSection($this->getLanguage());
      if ($section === null) {
         throw new ConfigurationException('Configuration section "' . $this->getLanguage() . '" is not present within '
               . 'the contact form module configuration loading the email templates. Please '
               . 'review your configuration!');
      }

      return $this->fillPlaceHolders(
         $this->getEmailTemplateContent(
            $section->getValue('notification.namespace'),
            $section->getValue('notification.template')
         ),
         $values
      );
   }

   /**
    * @private
    *
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
    * @return string The notification text sent to the originator to confirm the submission.
    * @throws ConfigurationException In case the language configuration section is missing.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.10.2011<br />
    */
   private function getConfirmationText(array $values = array()) {
      $config = $this->getConfiguration('APF\modules\contact', 'mail_templates.ini');
      $section = $config->getSection($this->getLanguage());
      if ($section === null) {
         throw new ConfigurationException('Configuration section "' . $this->getLanguage() . '" is not present within '
               . 'the contact form module configuration loading the email templates. Please '
               . 'review your configuration!');
      }

      return $this->fillPlaceHolders(
         $this->getEmailTemplateContent(
            $section->getValue('confirmation.namespace'),
            $section->getValue('confirmation.template')
         ),
         $values
      );
   }

   /**
    * @private
    *
    * Fills the applied place holders within the given text.
    *
    * @param string $text The text to fill the place holders in.
    * @param array $values An associative array of place holders and their value to be included within the text.
    * @return string The text with filled place holders.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.10.2011<br />
    */
   private function fillPlaceHolders($text, array $values = array()) {
      foreach ($values as $key => $value) {
         $text = str_replace('{' . $key . '}', $value, $text);
      }
      return $text;
   }

   /**
    * @private
    *
    * Loads the email template regarding the configuration.
    *
    * @param string $namespace The namespace of the template.
    * @param string $template The name of the template.
    * @return string The mail template content.
    * @throws IncludeException In case the template file cannot be loaded.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.10.2011<br />
    */
   private function getEmailTemplateContent($namespace, $template) {
      $file = $this->getRootPath() . '/' . str_replace('\\', '/', $namespace) . '/' . $template . '.html';
      if (file_exists($file)) {
         return file_get_contents($file);
      }
      throw new IncludeException('Email template file "' . $file . '" cannot be loaded. '
            . 'Please review your contact module configuration!');
   }

   /**
    * @return string The root path of the APF code base.
    */
   private function getRootPath() {
      return RootClassLoader::getLoaderByVendor('APF')->getRootPath();
   }

}
