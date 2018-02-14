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

//<*PostboxFolderBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for PostboxFolder. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "PostboxFolder" which extends this base-class.
 */
use APF\extensions\postbox\biz\AbstractPostboxFolder;

/**
 * This class provides the descriptive getter and setter methods for the "APF\extensions\postbox\biz\PostboxFolder" domain object.
 */
abstract class PostboxFolderBase extends AbstractPostboxFolder {

   /**
    * @var string The value for property "Name".
    */
   protected $Name;

   /**
    * @var int The value for the object's ID.
    */
   protected $PostboxFolderID;

   /**
    * @var string The creation timestamp.
    */
   protected $CreationTimestamp;

   /**
    * @var string The modification timestamp.
    */
   protected $ModificationTimestamp;

   protected $propertyNames = [
         'PostboxFolderID',
         'CreationTimestamp',
         'ModificationTimestamp',
         'Name'
   ];

   public function __construct($objectName = null) {
      parent::__construct('PostboxFolder');
   }

   public function getProperty($name) {
      if (in_array($name, $this->propertyNames)) {
         return $this->$name;
      }

      return null;
   }

   public function setProperty($name, $value) {
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

   public function setProperties($properties = []) {
      foreach ($properties as $key => $value) {
         if (in_array($key, $this->propertyNames)) {
            $this->$key = $value;
         }
      }
   }

   public function deleteProperty($name) {
      if (in_array($name, $this->propertyNames)) {
         $this->$name = null;
      }
   }

   public function setObjectId($id) {
      $this->PostboxFolderID = $id;
   }

   public function getObjectId() {
      return $this->PostboxFolderID;
   }

   public function __sleep() {
      return [
            'objectName',
            'PostboxFolderID',
            'CreationTimestamp',
            'ModificationTimestamp',
            'Name',
            'relatedObjects'
      ];
   }

   /**
    * @return string The value for property "Name".
    */
   public function getName() {
      return $this->getProperty('Name');
   }

   /**
    * @param string $value The value to set for property "Name".
    *
    * @return PostboxFolder The domain object for further usage.
    */
   public function setName($value) {
      $this->setProperty('Name', $value);

      return $this;
   }

   /**
    * @return PostboxFolder The domain object for further usage.
    */
   public function deleteName() {
      $this->deleteProperty('Name');

      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*PostboxFolderBase:end*>
