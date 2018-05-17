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
namespace APF\extensions\news\biz;

//<*NewsBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for News. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "News" which extends this base-class.
 */
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * This class provides the descriptive getter and setter methods for the "APF\extensions\news\biz\News" domain object.
 */
abstract class NewsBase extends GenericDomainObject {

   /**
    * @var string The value for property "AppKey".
    */
   protected $AppKey;

   /**
    * @var string The value for property "Author".
    */
   protected $Author;

   /**
    * @var string The value for property "Title".
    */
   protected $Title;

   /**
    * @var string The value for property "Text".
    */
   protected $Text;

   /**
    * @var int The value for the object's ID.
    */
   protected $NewsID;

   /**
    * @var string The creation timestamp.
    */
   protected $CreationTimestamp;

   /**
    * @var string The modification timestamp.
    */
   protected $ModificationTimestamp;

   protected $propertyNames = [
         'NewsID',
         'CreationTimestamp',
         'ModificationTimestamp',
         'AppKey',
         'Author',
         'Title',
         'Text'
   ];

   public function __construct(string $objectName = null) {
      parent::__construct('News');
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
      $this->NewsID = $id;

      return $this;
   }

   public function getObjectId() {
      return $this->NewsID;
   }

   public function __sleep() {
      return [
            'objectName',
            'NewsID',
            'CreationTimestamp',
            'ModificationTimestamp',
            'AppKey',
            'Author',
            'Title',
            'Text',
            'relatedObjects'
      ];
   }

   /**
    * @return string The value for property "AppKey".
    */
   public function getAppKey() {
      return $this->getProperty('AppKey');
   }

   /**
    * @param string $value The value to set for property "AppKey".
    *
    * @return News The domain object for further usage.
    */
   public function setAppKey($value) {
      $this->setProperty('AppKey', $value);

      return $this;
   }

   /**
    * @return News The domain object for further usage.
    */
   public function deleteAppKey() {
      $this->deleteProperty('AppKey');

      return $this;
   }

   /**
    * @return string The value for property "Author".
    */
   public function getAuthor() {
      return $this->getProperty('Author');
   }

   /**
    * @param string $value The value to set for property "Author".
    *
    * @return News The domain object for further usage.
    */
   public function setAuthor($value) {
      $this->setProperty('Author', $value);

      return $this;
   }

   /**
    * @return News The domain object for further usage.
    */
   public function deleteAuthor() {
      $this->deleteProperty('Author');

      return $this;
   }

   /**
    * @return string The value for property "Title".
    */
   public function getTitle() {
      return $this->getProperty('Title');
   }

   /**
    * @param string $value The value to set for property "Title".
    *
    * @return News The domain object for further usage.
    */
   public function setTitle($value) {
      $this->setProperty('Title', $value);

      return $this;
   }

   /**
    * @return News The domain object for further usage.
    */
   public function deleteTitle() {
      $this->deleteProperty('Title');

      return $this;
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
    * @return News The domain object for further usage.
    */
   public function setText($value) {
      $this->setProperty('Text', $value);

      return $this;
   }

   /**
    * @return News The domain object for further usage.
    */
   public function deleteText() {
      $this->deleteProperty('Text');

      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*NewsBase:end*>
