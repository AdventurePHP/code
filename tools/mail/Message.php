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
namespace APF\tools\mail;

use APF\core\registry\Registry;
use Exception;

/**
 * Defines a simple interface to send e-mail messages.
 */
class Message {

   const PRIORITY_NORMAL = 1;
   const PRIORITY_HIGH = 3;

   /**
    * @var Recipient Sender of the message.
    */
   protected $sender;

   /**
    * @var string The subject of the e-mail.
    */
   protected $subject;

   /**
    * @var Recipient[] List of direct recipients.
    */
   protected $recipients = [];

   /**
    * @var Recipient[] List of copy.
    */
   protected $carbonCopy = [];

   /**
    * @var Recipient[] List of blind copy recipients.
    */
   protected $blindCarbonCopy = [];

   /**
    * @var Recipient Return path for this e-mail.
    */
   protected $returnPath;

   /**
    * @var string The content of the e-mail.
    */
   protected $content;

   /**
    * @var int The e-mail priority.
    */
   protected $priority = self::PRIORITY_NORMAL;

   /**
    * @var string The content format contained in the e-mail.
    */
   protected $contentType;

   /**
    * @param Recipient $sender The sender of this message.
    * @param string $subject The message's subject.
    * @param string $content The message content.
    */
   public function __construct(Recipient $sender, $subject, $content) {

      $this->sender = $sender;
      $this->subject = $subject;
      $this->content = $content;

      $this->contentType = 'plain/text; charset=' . Registry::retrieve('APF\core', 'Charset');
   }

   /**
    * @return string The content type of the current message.
    */
   public function getContentType() {
      return $this->contentType;
   }

   /**
    * Allows to specify the e-mails content type.
    *
    * @param string $contentType The content type of the current message.
    *
    * @return $this This instance for further usage.
    */
   public function setContentType($contentType) {
      $this->contentType = $contentType;

      return $this;
   }

   /**
    * @return $this This instance for further usage.
    * @throws Exception In case something went wrong during e-mail delivery.
    */
   public function send() {

      // check on recipients
      if (empty($this->recipients) && empty($this->carbonCopy) && empty($this->blindCarbonCopy)) {
         throw new MessageException('Cannot send e-mail w/o any recipients!');
      }

      if (!$this->mail(implode(', ', $this->getRecipients()), $this->getSubject(), $this->getContent(), $this->getAdditionalHeaders())) {
         throw new MessageException('Sending e-mail failed!');
      }

      return $this;
   }

   /**
    * Internal mail() method wrapper for testing purposes.
    *
    * @param string $recipients List of recipients.
    * @param string $subject The e-mail's subject.
    * @param string $content The email's content.
    * @param string $headers The additional headers of the e-mail.
    *
    * @return bool
    */
   protected function mail($recipients, $subject, $content, $headers) {
      return @mail($recipients, $subject, $content, $headers);
   }

   /**
    * Returns the string representation of recipients.
    *
    * @return Recipient[] The list of recipients.
    */
   public function getRecipients() {
      return $this->recipients;
   }

   /**
    * @param Recipient[] $recipients List of recipients to send the e-mail to.
    *
    * @return $this This instance for further usage.
    */
   public function setRecipients(array $recipients) {
      $this->recipients = $recipients;

      return $this;
   }

   /**
    * @return string The message subject.
    */
   public function getSubject() {
      return $this->subject;
   }

   /**
    * @return string The message content.
    */
   public function getContent() {
      return $this->content;
   }

   /**
    * @return string Assembles the e-mails headers.
    */
   protected function getAdditionalHeaders() {

      $headers[] = 'From: ' . $this->getSender();

      // add cc recipients
      if (count($this->carbonCopy) > 0) {
         $headers[] = 'CC: ' . implode(', ', $this->carbonCopy);
      }

      // add bcc recipients
      if (count($this->blindCarbonCopy) > 0) {
         $headers[] = 'BCC: ' . implode(', ', $this->blindCarbonCopy);
      }

      $headers[] = 'Content-Type: ' . $this->contentType . '';

      if ($this->getReturnPath() !== null) {
         $headers[] = 'Return-Path: ' . $this->getReturnPath()->getEmail() . '';
      }

      $headers[] = 'X-Priority: ' . $this->priority;
      $headers[] = 'MIME-Version: 1.0';

      $headers[] = 'X-Sender: APF-E-Mail-Client';
      $headers[] = 'X-Mailer: PHP/' . phpversion() . '';

      return implode(PHP_EOL, $headers);
   }

   /**
    * @return Recipient The sender of this e-mail.
    */
   public function getSender() {
      return $this->sender;
   }

   /**
    * @return Recipient|null The return e-mail of the message.
    */
   public function getReturnPath() {
      return $this->returnPath;
   }

   /**
    * @param Recipient $returnPath The return path/e-mail this message.
    *
    * @return $this This instance for further usage.
    */
   public function setReturnPath(Recipient $returnPath) {
      $this->returnPath = $returnPath;

      return $this;
   }

   /**
    * @param Recipient $recipient A recipient to add to this e-mail.
    *
    * @return $this This instance for further usage.
    */
   public function addRecipient(Recipient $recipient) {
      $this->recipients[] = $recipient;

      return $this;
   }

   /**
    * @return $this This instance for further usage.
    */
   public function clearRecipients() {
      $this->recipients = [];

      return $this;
   }

   /**
    * @return Recipient[] The list of copy recipients.
    */
   public function getCopyRecipients() {
      return $this->carbonCopy;
   }

   /**
    * @param Recipient[] $recipients The list of copy recipients.
    *
    * @return $this This instance for further usage.
    */
   public function setCopyRecipients(array $recipients) {
      $this->carbonCopy = $recipients;

      return $this;
   }

   /**
    * @param Recipient $recipient The copy recipient to add.
    *
    * @return $this This instance for further usage.
    */
   public function addCopyRecipient(Recipient $recipient) {
      $this->carbonCopy[] = $recipient;

      return $this;
   }

   /**
    * @return $this This instance for further usage.
    */
   public function clearCopyRecipients() {
      $this->carbonCopy = [];

      return $this;
   }

   /**
    * @return Recipient[] The list of blind copy recipients.
    */
   public function getBlindCopyRecipients() {
      return $this->blindCarbonCopy;
   }

   /**
    * @param Recipient[] $recipients The list of blind copy recipients.
    *
    * @return $this This instance for further usage.
    */
   public function setBlindCopyRecipients(array $recipients) {
      $this->blindCarbonCopy = $recipients;

      return $this;
   }

   /**
    * @param Recipient $recipient The blind copy recipient to add.
    *
    * @return $this This instance for further usage.
    */
   public function addBlindCopyRecipient(Recipient $recipient) {
      $this->blindCarbonCopy[] = $recipient;

      return $this;
   }

   /**
    * @return $this This instance for further usage.
    */
   public function clearBlindCopyRecipients() {
      $this->blindCarbonCopy = [];

      return $this;
   }

   /**
    * @return int Priority of the current message.
    */
   public function getPriority() {
      return $this->priority;
   }

   /**
    * @param int $priority Priority of the current message.
    *
    * @return $this This instance for further usage.
    */
   public function setPriority($priority) {
      $this->priority = $priority;

      return $this;
   }

}
