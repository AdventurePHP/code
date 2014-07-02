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
namespace APF\extensions\postbox\biz;

//<*MessageBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for Message. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "Message" which extends this base-class.
 */

/**
 * @package APF\extensions\postbox\biz
 * @class MessageBase
 *
 * This class provides the descriptive getter and setter methods for the "APF\extensions\postbox\biz\Message" domain object.
 */
abstract class MessageBase extends AbstractMessage {

   public function __construct($objectName = null) {
      parent::__construct('Message');
   }

   /**
    * @return string The value for property "Text".
    */
   public function getText() {
      return $this->getProperty('Text');
   }

   /**
    * @param string $value The value to set for property "Text".
    *
    * @return Message The domain object for further usage.
    */
   public function setText($value) {
      $this->setProperty('Text', $value);

      return $this;
   }

   /**
    * @return Message The domain object for further usage.
    */
   public function deleteText() {
      $this->deleteProperty('Text');

      return $this;
   }

   /**
    * @return string The value for property "AuthorNameFallback".
    */
   public function getAuthorNameFallback() {
      return $this->getProperty('AuthorNameFallback');
   }

   /**
    * @param string $value The value to set for property "AuthorNameFallback".
    *
    * @return Message The domain object for further usage.
    */
   public function setAuthorNameFallback($value) {
      $this->setProperty('AuthorNameFallback', $value);

      return $this;
   }

   /**
    * @return Message The domain object for further usage.
    */
   public function deleteAuthorNameFallback() {
      $this->deleteProperty('AuthorNameFallback');

      return $this;
   }

   /**
    * @return string The value for property "CreationTimestamp".
    */
   public function getCreationTimestamp() {
      return $this->getProperty('CreationTimestamp');
   }

   /**
    * @return string The value for property "ModificationTimestamp".
    */
   public function getModificationTimestamp() {
      return $this->getProperty('ModificationTimestamp');
   }

}

// DO NOT CHANGE THIS COMMENT! <*MessageBase:end*>
