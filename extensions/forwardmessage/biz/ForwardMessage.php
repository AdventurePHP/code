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

/**
 * @package extensions::forwardmessage::biz
 * @class ForwardMessageManager
 *
 * Represents a single message within the forward message extension.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.02.2012<br />
 */
class ForwardMessage {

   /**
    * @var string The message content.
    */
   private $message;

   /**
    * @var bool True, in case this message is visible, false otherwise.
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
