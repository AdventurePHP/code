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
namespace APF\tools\mail;

use APF\tools\validation\EMailValidator;
use InvalidArgumentException;

/**
 * Represents an e-mail sender or recipient that can be added to a Message as TO, CC, or BCC entry.
 */
class MailAddress {

   /**
    * @var string The recipient's name
    */
   protected $name;

   /**
    * @var string The recipient's e-mail.
    */
   protected $email;

   /**
    * Creates an e-mail address.
    *
    * @param string $name The sender's/recipient's (display) name.
    * @param string $email The sender's/recipient's e-mail.
    *
    * @throws InvalidArgumentException In case of invalid/empty name and/or e-mail.
    */
   public function __construct(string $name = null, string $email = null) {

      if (empty($email)) {
         throw new InvalidArgumentException('(Display) Name and/or e-mail must not be empty!');
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
    * Creates the string representation of a sender/recipient. This is used
    * by the Message class to generate the e-mail body.
    */
   public function __toString() {

      // in case we only have an e-mail...
      if ($this->name === null) {
         return $this->email;
      }

      return '"' . $this->name . '" <' . $this->email . '>';
   }

}
