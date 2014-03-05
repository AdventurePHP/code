<?php
namespace APF\tools\mail;

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
use APF\core\logging\LogEntry;
use APF\core\pagecontroller\APFObject;
use APF\core\logging\Logger;
use APF\core\singleton\Singleton;
use APF\tools\form\taglib\ButtonTag;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\taglib\TextFieldTag;
use APF\tools\form\validator\EMailValidator;

/**
 * @package APF\tools\mail
 * @class mailSender
 *
 * Provides a mail() wrapper.
 *
 * @deprecated This service implementation is deprecated. Please use any available PHP solution instead (e.g. PHPMailer).
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 09.06.2004<br />
 * Version 0.2, 04.01.2005<br />
 * Version 0.3, 27.04.2005<br />
 * Version 0.4, 14.01.2006 (Redesign / introduced new technology)<br />
 * Version 0.5, 21.06.2006 (Added CC recipients capability)<br />
 * Version 0.5, 30.03.2007 (Switched to the ConfigurationManager)<br />
 * Version 0.5, 03.09.2007 (Added BCC recipients capability and further header)<br />
 */
class mailSender extends APFObject {

   /**
    * @protected
    * Indicates the sender.
    * <pre>$this->sender['Name']  = '...';
    * $this->sender['EMail'] = '...';</pre>
    */
   protected $sender = array();

   /**
    * @protected
    * Indicates the recipients.
    * <pre>$this->recipients[0]['Name']  = '...';
    * $this->recipients[0]['EMail'] = '...';
    * $this->recipients[1]['Name']  = '...';
    * $this->recipients[1]['EMail'] = '...';</pre>
    */
   protected $recipients = array();

   /**
    * @protected
    * Indicates the CC recipients.
    * <pre>$this->ccRecipients[0]['Name']  = '...';
    * $this->ccRecipients[0]['EMail'] = '...';
    * $this->ccRecipients[1]['Name']  = '...';
    * $this->ccRecipients[1]['EMail'] = '...';</pre>
    */
   protected $ccRecipients = array();

   /**
    * @protected
    * Indicates the BCC recipients.
    * <pre>$this->bccRecipients[0]['Name']  = '...';
    * $this->bccRecipients[0]['EMail'] = '...';
    * $this->bccRecipients[1]['Name']  = '...';
    * $this->bccRecipients[1]['EMail'] = '...';</pre>
    */
   protected $bccRecipients = array();

   /**
    * @protected
    * Header of the mail.
    */
   protected $mailHeader = null;

   /**
    * @protected
    * The mail's subject.
    */
   protected $subject;

   /**
    * @protected
    * Content of the mail.
    */
   protected $content = '';

   /**
    * @protected
    * Content type of the mail.
    */
   protected $contentType;

   /**
    * @protected
    * Return path.
    */
   protected $returnPath;

   /**
    * @protected
    * EOL sign.
    */
   protected static $EOL = "\n";

   /**
    * @protected
    * CRLF sign.
    */
   protected static $CRLF = "\r\n";

   /**
    * @public
    *
    * Initializes the component. Loads the configuration file for the current
    * instance of the mailSender.
    *
    * @param string $initParam The name of the config section to initialize the component with.
    * @throws \InvalidArgumentException In case the init param is referring to non-existent config section.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 14.01.2006<br />
    * Version 0.2, 15.01.2006<br />
    * Version 0.3, 30.03.2007 (Renamed to init() to be able to use it with the ServiceManager)<br />
    * Version 0.4, 31.03.2007 (Text and recipients are now cleared to allow multiple usage)<br />
    * Version 0.5, 04.08.2009 (Added workaround for PHP bug with method signature in PHP 5.2.10 and 5.3.X)<br />
    */
   public function init($initParam) {

      // safely initialize the default config section
      if (empty($initParam)) {
         $initParam = 'Standard';
      }

      // load config
      $config = $this->getConfiguration('APF\tools\mail', 'mailsender.ini');
      $section = $config->getSection($initParam);

      if ($section === null) {
         throw new \InvalidArgumentException('[mailSender::init()] Section "' . $initParam
               . '" is not present within the mail sender\'s configuration!');
      }

      // set sender
      $this->sender['Name'] = $section->getValue('Mail.SenderName');
      $this->sender['EMail'] = $section->getValue('Mail.SenderEMail');

      $this->contentType = $section->getValue('Mail.ContentType');

      $this->returnPath = $section->getValue('Mail.ReturnPath');

      // reset text and recipients to avoid interference during multiple usage
      $this->clearRecipients();
      $this->clearCCRecipients();
      $this->clearContent();
   }

   /**
    * @protected
    *
    * Generates the mail's header.
    *
    * @return string $header the final header of the mail.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 09.06.2004<br />
    * Version 0.2, 03.01.2005<br />
    * Version 0.3, 17.01.2005<br />
    * Version 0.4, 14.01.2006<br />
    * Version 0.5, 21.06.2005 (Added the CC recipients)<br />
    * Version 0.6, 03.09.2007 (Added some more headers and BCC recipients)<br />
    */
   protected function generateHeader() {

      $mailHeader = (string)'';
      $mailHeader .= 'From: "' . ($this->sender['Name']) . '" <' . ($this->sender['EMail']) . '>' . self::$EOL;

      // add cc recipients
      if (count($this->ccRecipients) > 0) {

         $ccRecipients = array();

         for ($i = 0; $i < count($this->ccRecipients); $i++) {
            $ccRecipients[] = '"' . ($this->ccRecipients[$i]['Name']) . '" <' . ($this->ccRecipients[$i]['EMail']) . '>';
         }

         $mailHeader .= 'CC: ' . implode(', ', $ccRecipients) . '' . self::$EOL;

      }

      // add bcc recipients
      if (count($this->bccRecipients) > 0) {

         $bccRecipients = array();

         for ($i = 0; $i < count($this->bccRecipients); $i++) {
            $bccRecipients[] = '"' . ($this->bccRecipients[$i]['Name']) . '" <' . ($this->bccRecipients[$i]['EMail']) . '>';
         }

         $mailHeader .= 'BCC: ' . implode(', ', $bccRecipients) . '' . self::$EOL;

      }

      // add default header
      $mailHeader .= 'X-Sender: APF-E-Mail-Client' . self::$EOL;
      $mailHeader .= 'X-Mailer: PHP/' . phpversion() . '' . self::$EOL;
      $mailHeader .= 'X-Priority: 3' . self::$EOL; // 1: urgent, 3: normal
      $mailHeader .= 'MIME-Version: 1.0' . self::$EOL;
      $mailHeader .= 'Return-Path: ' . ($this->returnPath) . '' . self::$EOL;
      $mailHeader .= 'Content-Type: ' . ($this->contentType) . '' . self::$EOL;

      // add additional header if applicable
      if ($this->mailHeader != null) {
         $mailHeader .= $this->mailHeader;
      }

      return $mailHeader;
   }

   /**
    * @public
    *
    * Allows to add headers to the mail.
    *
    * @param string $header the header to add.
    *
    * @author Christian W. Schäfer
    * @version
    * Version 0.1, 03.09.2007<br />
    */
   public function addHeader($header = '') {
      if (strpos($header, ':') !== false) {
         $this->mailHeader .= $header . '' . self::$EOL;
      }
   }

   /**
    * @public
    *
    * Allows you to add recipients.
    *
    * @param string $recipientEMail the email of the BCC recipient.
    * @param string $recipientName the name of the BCC recipient.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 09.06.2004<br />
    * Version 0.2, 14.01.2006<br />
    */
   public function setRecipient($recipientEMail, $recipientName) {
      if ($this->validateEMail($recipientEMail)) {
         $this->recipients[count($this->recipients)] = array('Name' => $recipientName,
            'EMail' => $recipientEMail
         );
      }
   }

   /**
    * @public
    *
    * Allows you to clear the recipients.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.03.2007<br />
    */
   public function clearRecipients() {
      $this->recipients = array();
   }

   /**
    * @public
    *
    * Allows you to add CC recipients.
    *
    * @param string $recipientEMail the email of the CC recipient.
    * @param string $recipientName the name of the CC recipient.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 21.06.2006<br />
    */
   public function setCCRecipient($recipientEMail, $recipientName) {
      if ($this->validateEMail($recipientEMail)) {
         $this->ccRecipients[count($this->ccRecipients)] = array('Name' => $recipientName,
            'EMail' => $recipientEMail
         );
      }
   }

   /**
    * @public
    *
    * Allows you to clear CC recipients.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.03.2007<br />
    */
   public function clearCCRecipients() {
      $this->ccRecipients = array();
   }

   /**
    * @public
    *
    * Allows you to add BCC recipients.
    *
    * @param string $recipientEMail the email of the BCC recipient.
    * @param string $recipientName the name of the BCC recipient.
    *
    * @author Christian W. Schäfer
    * @version
    * Version 0.1, 03.09.2007<br />
    */
   public function setBCCRecipient($recipientEMail, $recipientName) {
      if ($this->validateEMail($recipientEMail)) {
         $this->bccRecipients[count($this->bccRecipients)] = array('Name' => $recipientName,
            'EMail' => $recipientEMail
         );
      }
   }

   /**
    * @public
    *
    * Allows you to clear BCC recipients.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.03.2007<br />
    */
   public function clearBCCRecipients() {
      $this->bccRecipients = array();
   }

   /**
    * @public
    *
    * Allows you to manipulate the sender.
    *
    * @param string $senderEMail the email of the sender.
    * @param string $senderName the name of the sender.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 17.12.2006<br />
    */
   public function setSender($senderEMail, $senderName) {
      if ($this->validateEMail($senderEMail)) {
         $this->sender['Name'] = $senderName;
         $this->sender['EMail'] = $senderEMail;
      }
   }

   /**
    * @param string $email The email to validate.
    * @return bool True in case the email is valid, false otherwise.
    */
   private function validateEMail($email) {
      $validator = new EMailValidator(new TextFieldTag(), new ButtonTag());
      return $validator->validate($email);
   }

   /**
    * @public
    *
    * Allows you to manipulate return path
    *
    * @param string $returnPath the return path
    *
    * @author Ralf Schubert
    */
   public function setReturnPath($returnPath) {
      $this->returnPath = $returnPath;
   }

   /**
    * @public
    *
    * Allows you to add content to the mail.
    *
    * @param string $content the content to add to the mail.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 09.06.2004<br />
    * Version 0.2, 14.01.2006<br />
    */
   public function setContent($content) {
      $this->content .= $content . '' . self::$EOL;
   }

   /**
    * @public
    *
    * Resets the content of the mail.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.03.2007<br />
    */
   public function clearContent() {
      $this->content = (string)'';
   }

   /**
    * @public
    *
    * Sets the subject of the mail.
    *
    * @param string $subject the mail's subject.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 09.06.2004<br />
    * Version 0.2, 14.01.2006<br />
    */
   public function setSubject($subject) {
      $this->subject = $subject;
   }

   /**
    * @public
    *
    * Send an email to the recipients configured. The return array contains two associative
    * offsets including the following information:
    * <ul>
    *   <li>recipientcount (new!): the number of recipients.</li>
    *   <li>successcount (new!): the number of emails sent successfully.</li>
    * </ul>
    *
    * @return string[] The sending status.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 09.06.2004<br />
    * Version 0.2, 03.01.2005<br />
    * Version 0.3, 17.01.2005<br />
    * Version 0.4, 14.01.2006<br />
    */
   public function sendMail() {

      $header = $this->generateHeader();
      /* @var $log Logger */
      $log = & Singleton::getInstance('APF\core\logging\Logger');
      $sentEmails = array();

      for ($i = 0; $i < count($this->recipients); $i++) {

         $result = @mail($this->recipients[$i]['EMail'], $this->subject, $this->content, $header);

         if ($result == 1 || $result == true) {
            $log->logEntry('mail', 'Sending mail to ' . $this->recipients[$i]['EMail'] . '.', LogEntry::SEVERITY_INFO);
            $sentEmails[] = '1';
         } else {
            $log->logEntry('mail', 'Sending mail to ' . $this->recipients[$i]['EMail'] . '.', LogEntry::SEVERITY_ERROR);
         }

      }

      $status['recipientcount'] = count($this->recipients);
      $status['successcount'] = count($sentEmails);
      return $status;
   }

}
