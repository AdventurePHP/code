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
namespace APF\modules\usermanagement\biz\model;

//<*UmgtPermissionBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtPermission. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtPermission" which extends this base-class.
 */
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * This class provides the descriptive getter and setter methods for the "APF\modules\usermanagement\biz\model\UmgtPermission" domain object.
 */
abstract class UmgtPermissionBase extends GenericDomainObject {

   /**
    * @var string The value for property "DisplayName".
    */
   protected $DisplayName;

   /**
    * @var string The value for property "Name".
    */
   protected $Name;

   /**
    * @var string The value for property "Value".
    */
   protected $Value;

   /**
    * @var int The value for the object's ID.
    */
   protected $PermissionID;

   /**
    * @var string The creation timestamp.
    */
   protected $CreationTimestamp;

   /**
    * @var string The modification timestamp.
    */
   protected $ModificationTimestamp;

   protected $propertyNames = [
         'PermissionID',
         'CreationTimestamp',
         'ModificationTimestamp',
         'DisplayName',
         'Name',
         'Value'
   ];

   public function __construct(string $objectName = null) {
      parent::__construct('Permission');
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

      return $this;
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

      return $this;
   }

   public function deleteProperty(string $name) {
      if (in_array($name, $this->propertyNames)) {
         $this->$name = null;
      }

      return $this;
   }

   public function setObjectId(int $id) {
      $this->PermissionID = $id;

      return $this;
   }

   public function getObjectId() {
      return $this->PermissionID;
   }

   public function __sleep() {
      return [
            'objectName',
            'PermissionID',
            'CreationTimestamp',
            'ModificationTimestamp',
            'DisplayName',
            'Name',
            'Value',
            'relatedObjects'
      ];
   }

   /**
    * @return string The value for property "DisplayName".
    */
   public function getDisplayName() {
      return $this->getProperty('DisplayName');
   }

   /**
    * @param string $value The value to set for property "DisplayName".
    *
    * @return UmgtPermission The domain object for further usage.
    */
   public function setDisplayName($value) {
      $this->setProperty('DisplayName', $value);

      return $this;
   }

   /**
    * @return UmgtPermission The domain object for further usage.
    */
   public function deleteDisplayName() {
      $this->deleteProperty('DisplayName');

      return $this;
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
    * @return UmgtPermission The domain object for further usage.
    */
   public function setName($value) {
      $this->setProperty('Name', $value);

      return $this;
   }

   /**
    * @return UmgtPermission The domain object for further usage.
    */
   public function deleteName() {
      $this->deleteProperty('Name');

      return $this;
   }

   /**
    * @return string The value for property "Value".
    */
   public function getValue() {
      return $this->getProperty('Value');
   }

   /**
    * @param string $value The value to set for property "Value".
    *
    * @return UmgtPermission The domain object for further usage.
    */
   public function setValue($value) {
      $this->setProperty('Value', $value);

      return $this;
   }

   /**
    * @return UmgtPermission The domain object for further usage.
    */
   public function deleteValue() {
      $this->deleteProperty('Value');

      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*UmgtPermissionBase:end*>
