<?php
namespace APF\extensions\postbox\biz;

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

//<*MessageBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for Message. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "Message" which will extend this base-class.
 */
use APF\extensions\postbox\biz\AbstractMessage;
class MessageBase extends AbstractMessage {

   public function __construct($objectName = null) {
      parent::__construct('Message');
   }

   public function getText() {
      return $this->getProperty('Text');
   }

   public function setText($value) {
      $this->setProperty('Text', $value);
      return $this;
   }

   public function getAuthorNameFallback() {
      return $this->getProperty('AuthorNameFallback');
   }

   public function setAuthorNameFallback($value) {
      $this->setProperty('AuthorNameFallback', $value);
      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*MessageBase:end*>

/**
 * @package APF\extensions\postbox\biz
 * @class Message
 *
 * Domain object for "Message"
 * Use this class to add your own functions.
 */
class Message extends MessageBase {
   /**
    * Call parent's function because the objectName needs to be set.
    */
   public function __construct($objectName = null) {
      parent::__construct();
   }

}
