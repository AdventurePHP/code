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
namespace APF\extensions\forwardmessage\biz;

/**
 * Represents a single message within the forward message extension.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.02.2012<br />
 */
class ForwardMessage {

   /**
    * The message content.
    *
    * @var string $message
    */
   private $message;

   /**
    * True, in case this message is visible, false otherwise.
    *
    * @var bool $visible
    */
   private $visible;

   /**
    * @param string $message The content of the message.
    * @param bool $visible True in case the message is visible by default, false otherwise.
    */
   public function __construct($message, $visible = true) {
      $this->message = $message;
      $this->visible = $visible;
   }

   public function setMessage($message) {
      $this->message = $message;
   }

   public function getMessage() {
      return $this->message;
   }

   public function hide() {
      $this->visible = false;
   }

   public function show() {
      $this->visible = true;
   }

   public function isVisible() {
      return $this->visible;
   }

}
