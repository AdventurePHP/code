<?php
namespace APF\tools\mail;

use APF\tools\validation\EMailValidator;
use InvalidArgumentException;

/**
 * Represents an e-mail recipient that can be added to a Message as TO, CC, or BCC entry.
 */
class Recipient {

   /**
    * @var string The recipient's name
    */
   protected $name;

   /**
    * @var string The recipient's e-mail.
    */
   protected $email;

   /**
    * Creates a recipient.
    *
    * @param string $name The recipient's name.
    * @param string $email The recipient's e-mail.
    *
    * @throws InvalidArgumentException In case of invalid/empty name and/or e-mail.
    */
   public function __construct($name, $email) {

      if (empty($email)) {
         throw new InvalidArgumentException('Name and/or e-mail must not be empty!');
      }

      if (!(new EMailValidator())->isValid($email)) {
         throw new InvalidArgumentException('E-mail "' . $email . '" is invalid!');
      }

      $this->email = $email;

      // reset name to null in case an empty name is applied
      $this->name = empty($name) ? null : $name;
   }

   public function getName() {
      return $this->name;
   }

   public function getEmail() {
      return $this->email;
   }

   /**
    * Creates the string representation of a recipient. This is used
    * by the Message class to generate the e-mail body.
    */
   public function __toString() {

      // in case we only ...
      if ($this->name === null) {
         return $this->email;
      }

      return '"' . $this->name . '" <' . $this->email . '>';
   }

}
