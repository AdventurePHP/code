<?php
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

   import('tools::validator', 'Validator');

   /**
    *  @package tools::mail
    *  @class mailSender
    *
    *  Provides a mail() wrapper.
    *
    *  @author Christian Schäfer
    *  @version
    *  Version 0.1, 09.06.2004<br />
    *  Version 0.2, 04.01.2005<br />
    *  Version 0.3, 27.04.2005<br />
    *  Version 0.4, 14.01.2006 (Redesign / introduced new technology)<br />
    *  Version 0.5, 21.06.2006 (Added CC recipients capability)<br />
    *  Version 0.5, 30.03.2007 (Switched to the ConfigurationManager)<br />
    *  Version 0.5, 03.09.2007 (Added BCC recipients capability and futher header)<br />
    */
   class mailSender extends APFObject {

      /**
       * @protected
       * Indicates the sender.
       * <pre>$this->__Sender['Name']  = '...';
       * $this->__Sender['EMail'] = '...';</pre>
       */
      protected $__Sender = array();
      /**
       * @protected
       * Indicates the recipients.
       * <pre>$this->__Recipients[0]['Name']  = '...';
       * $this->__Recipients[0]['EMail'] = '...';
       * $this->__Recipients[1]['Name']  = '...';
       * $this->__Recipients[1]['EMail'] = '...';</pre>
       */
      protected $__Recipients = array();
      /**
       * @protected
       * Indicates the CC recipients.
       * <pre>$this->__CCRecipients[0]['Name']  = '...';
       * $this->__CCRecipients[0]['EMail'] = '...';
       * $this->__CCRecipients[1]['Name']  = '...';
       * $this->__CCRecipients[1]['EMail'] = '...';</pre>
       */
      protected $__CCRecipients = array();
      /**
       * @protected
       * Indicates the BCC recipients.
       * <pre>$this->__BCCRecipients[0]['Name']  = '...';
       * $this->__BCCRecipients[0]['EMail'] = '...';
       * $this->__BCCRecipients[1]['Name']  = '...';
       * $this->__BCCRecipients[1]['EMail'] = '...';</pre>
       */
      protected $__BCCRecipients = array();
      /**
       * @protected
       * Header of the mail.
       */
      protected $__MailHeader = null;
      /**
       * @protected
       * The mail's subject.
       */
      protected $__Subject;
      /**
       * @protected
       * Content of the mail.
       */
      protected $__Content = '';
      /**
       * @protected
       * Content type of the mail.
       */
      protected $__ContentType;
      /**
       * @protected
       * Return path.
       */
      protected $__ReturnPath;
      /**
       * @protected
       * EOL sign.
       */
      protected $__EOL = "\n";
      /**
       * @protected
       * CRLF sign.
       */
      protected $__CRLF = "\r\n";

      /**
       * @public
       *
       * Initializes the conmponent. Loads the configuration file for the current
       * instance of the mailSender.
       *
       * @param string $initParam The name of the config section to initialize the component with.
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
            echo $initParam = 'Standard';
         }

         // load config
         $config = $this->getConfiguration('tools::mail', 'mailsender.ini');
         $section = $config->getSection($initParam);

         // set sender
         $this->__Sender['Name'] = $section->getValue('Mail.SenderName');
         $this->__Sender['EMail'] = $section->getValue('Mail.SenderEMail');

         $this->__ContentType = $section->getValue('Mail.ContentType');

         $this->__ReturnPath = $section->getValue('Mail.ReturnPath');

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

         $mailHeader = (string) '';
         $mailHeader .= 'From: "' . ($this->__Sender['Name']) . '" <' . ($this->__Sender['EMail']) . '>' . $this->__EOL;

         // add cc recipients
         if (count($this->__CCRecipients) > 0) {

            $ccRecipients = array();

            for ($i = 0; $i < count($this->__CCRecipients); $i++) {
               $ccRecipients[] = '"' . ($this->__CCRecipients[$i]['Name']) . '" <' . ($this->__CCRecipients[$i]['EMail']) . '>';
               // end for
            }

            $mailHeader .= 'CC: ' . implode(', ', $ccRecipients) . '' . $this->__EOL;

            // end if
         }

         // add bcc recipients
         if (count($this->__BCCRecipients) > 0) {

            $bccRecipients = array();

            for ($i = 0; $i < count($this->__BCCRecipients); $i++) {
               $bccRecipients[] = '"' . ($this->__BCCRecipients[$i]['Name']) . '" <' . ($this->__BCCRecipients[$i]['EMail']) . '>';
            }

            $mailHeader .= 'BCC: ' . implode(', ', $bccRecipients) . '' . $this->__EOL;

         }

         // add default header
         $mailHeader .= 'X-Sender: APF-E-Mail-Client' . $this->__EOL;
         $mailHeader .= 'X-Mailer: PHP/' . phpversion() . '' . $this->__EOL;
         $mailHeader .= 'X-Priority: 3' . $this->__EOL; //1 Dringende E-Mail, 3: Priorit�t Normal
         $mailHeader .= 'MIME-Version: 1.0' . $this->__EOL;
         $mailHeader .= 'Return-Path: ' . ($this->__ReturnPath) . '' . $this->__EOL;
         $mailHeader .= 'Content-Type: ' . ($this->__ContentType) . '' . $this->__EOL;

         // add additional header if applicable
         if ($this->__MailHeader != null) {
            $mailHeader .= $this->__MailHeader;
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
            $this->__MailHeader .= $header . '' . $this->__EOL;
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

         if (Validator::validateEMail($recipientEMail)) {
            $this->__Recipients[count($this->__Recipients)] = array('Name' => $recipientName,
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
         $this->__Recipients = array();
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

         if (Validator::validateEMail($recipientEMail)) {
            $this->__CCRecipients[count($this->__CCRecipients)] = array('Name' => $recipientName,
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
         $this->__CCRecipients = array();
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

         if (Validator::validateEMail($recipientEMail)) {
            $this->__BCCRecipients[count($this->__BCCRecipients)] = array('Name' => $recipientName,
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
         $this->__BCCRecipients = array();
      }

      /**
       * @public
       *
       * Allows you to maipulate the sender.
       *
       * @param string $senderEMail the email of the sender.
       * @param string $senderName the name of the sender.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 17.12.2006<br />
       */
      public function setSender($senderEMail, $senderName) {

         if (Validator::validateEMail($senderEMail)) {
            $this->__Sender['Name'] = $senderName;
            $this->__Sender['EMail'] = $senderEMail;
         }

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
         $this->__Content .= $content . '' . $this->__EOL;
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
         $this->__Content = (string) '';
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
         $this->__Subject = $subject;
      }

      /**
       * @public
       *
       * Send an email to the recipients configured. The return array contains two associative
       * offsets including the following information:
       * <ul>
       *   <li>AnzEMail (deprecated): the number of recipients.</li>
       *   <li>Versandt (deprecated): the number of emails sent successfully.</li>
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
         $log = &Singleton::getInstance('Logger');
         $sentEmails = array();

         for ($i = 0; $i < count($this->__Recipients); $i++) {

            $result = @mail($this->__Recipients[$i]['EMail'], $this->__Subject, $this->__Content, $header);

            if ($result == 1 || $result == true) {
               $log->logEntry('mail', 'Sending mail to ' . $this->__Recipients[$i]['EMail'] . '.', 'INFO');
               $sentEmails[] = '1';
            } else {
               $log->logEntry('mail', 'Sending mail to ' . $this->__Recipients[$i]['EMail'] . '.', 'ERROR');
            }

         }

         $status['recipientcount'] = count($this->__Recipients);
         $status['AnzEMail'] = $status['recipientcount']; // for back compatibility!
         $status['successcount'] = count($sentEmails);
         $status['Versandt'] = $status['successcount']; // for back compatibility!
         return $status;

      }

   }
?>