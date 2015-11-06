<?php
namespace APF\tools\mail;

use APF\core\registry\Registry;
use Exception;

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

      $this->contentType = 'plaint/text; charset=' . Registry::retrieve('APF\core', 'Charset');
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
    * @throws Exception In case something went wrong during e-mail delivery.
    */
   public function send() {

      // check on recipients
      if (empty($this->recipients) && empty($this->carbonCopy) && empty($this->blindCarbonCopy)) {
         throw new Exception('Cannot send e-mail w/o any recipients');
      }

      $header = $this->getAdditionalHeaders();
      if (!@mail($this->getRecipients(), $this->getSubject(), $this->getContent(), $header)) {
         throw new Exception('Sending e-mail went wrong!');
      }

      return $this;
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

      if ($this->returnPath !== null) {
         $headers[] = 'Return-Path: ' . $this->returnPath->getEmail() . '';
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
    * Returns the string representation of recipients.
    *
    * @return string The list of recipients.
    */
   public function getRecipients() {
      return implode(', ', $this->recipients);
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
    * @param Recipient $returnPath The return path/e-mail this message.
    *
    * @return $this This instance for further usage.
    */
   public function setReturnPath(Recipient $returnPath) {
      $this->returnPath = $returnPath;

      return $this;
   }

}
