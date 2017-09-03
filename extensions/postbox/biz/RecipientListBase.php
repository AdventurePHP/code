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

//<*RecipientListBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for RecipientList. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "RecipientList" which extends this base-class.
 */
use APF\extensions\postbox\biz\AbstractRecipientList;

/**
 * This class provides the descriptive getter and setter methods for the "APF\extensions\postbox\biz\RecipientList" domain object.
 */
abstract class RecipientListBase extends AbstractRecipientList {

   /**
    * @var string The value for property "Name".
    */
   protected $Name;

   /**
    * @var int The value for the object's ID.
    */
   protected $RecipientListID;

   /**
    * @var string The creation timestamp.
    */
   protected $CreationTimestamp;

   /**
    * @var string The modification timestamp.
    */
   protected $ModificationTimestamp;

   protected $propertyNames = [
         'RecipientListID',
         'CreationTimestamp',
         'ModificationTimestamp',
         'Name'
   ];

   public function __construct($objectName = null) {
      parent::__construct('RecipientList');
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
      $this->RecipientListID = $id;
   }

   public function getObjectId() {
      return $this->RecipientListID;
   }

   public function __sleep() {
      return [
            'objectName',
            'RecipientListID',
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
    * @return RecipientList The domain object for further usage.
    */
   public function setName($value) {
      $this->setProperty('Name', $value);

      return $this;
   }

   /**
    * @return RecipientList The domain object for further usage.
    */
   public function deleteName() {
      $this->deleteProperty('Name');

      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*RecipientListBase:end*>
