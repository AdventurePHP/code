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
namespace APF\extensions\postbox\biz;

//<*MessageBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for Message. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "Message" which extends this base-class.
 */
use APF\extensions\postbox\biz\AbstractMessage;

/**
 * This class provides the descriptive getter and setter methods for the "APF\extensions\postbox\biz\Message" domain object.
 */
abstract class MessageBase extends AbstractMessage {

   /**
    * @var string The value for property "Text".
    */
   protected $Text;

   /**
    * @var string The value for property "AuthorNameFallback".
    */
   protected $AuthorNameFallback;

   /**
    * @var int The value for the object's ID.
    */
   protected $MessageID;

   /**
    * @var string The creation timestamp.
    */
   protected $CreationTimestamp;

   /**
    * @var string The modification timestamp.
    */
   protected $ModificationTimestamp;

   protected $propertyNames = [
         'MessageID',
         'CreationTimestamp',
         'ModificationTimestamp',
         'Text',
         'AuthorNameFallback'
   ];

   public function __construct(string $objectName = null) {
      parent::__construct('Message');
   }

   public function getProperty(string $name) {
      if (in_array($name, $this->propertyNames)) {
         return $this->$name;
      }

      return null;
   }

   public function setProperty(string $name, $value) {
      if (in_array($name, $this->propertyNames)) {
         $this->$name = $value;
      }
   }

   public function getProperties() {
      $properties = [];
      foreach ($this->propertyNames as $name) {
         if ($this->$name !== null) {
            $properties[$name] = $this->$name;
         }
      }
      return $properties;
   }

   public function setProperties(array $properties = []) {
      foreach ($properties as $key => $value) {
         if (in_array($key, $this->propertyNames)) {
            $this->$key = $value;
         }
      }
   }

   public function deleteProperty(string $name) {
      if (in_array($name, $this->propertyNames)) {
         $this->$name = null;
      }
   }

   public function setObjectId(int $id) {
      $this->MessageID = $id;

      return $this;
   }

   public function getObjectId() {
      return $this->MessageID;
   }

   public function __sleep() {
      return [
            'objectName',
            'MessageID',
            'CreationTimestamp',
            'ModificationTimestamp',
            'Text',
            'AuthorNameFallback',
            'relatedObjects'
      ];
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

}

// DO NOT CHANGE THIS COMMENT! <*MessageBase:end*>
