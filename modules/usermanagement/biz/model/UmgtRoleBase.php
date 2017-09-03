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
namespace APF\modules\usermanagement\biz\model;

//<*UmgtRoleBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtRole. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtRole" which extends this base-class.
 */
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * This class provides the descriptive getter and setter methods for the "APF\modules\usermanagement\biz\model\UmgtRole" domain object.
 */
abstract class UmgtRoleBase extends GenericDomainObject {

   /**
    * @var string The value for property "DisplayName".
    */
   protected $DisplayName;

   /**
    * @var string The value for property "Description".
    */
   protected $Description;

   /**
    * @var int The value for the object's ID.
    */
   protected $RoleID;

   /**
    * @var string The creation timestamp.
    */
   protected $CreationTimestamp;

   /**
    * @var string The modification timestamp.
    */
   protected $ModificationTimestamp;

   protected $propertyNames = [
         'RoleID',
         'CreationTimestamp',
         'ModificationTimestamp',
         'DisplayName',
         'Description'
   ];

   public function __construct($objectName = null) {
      parent::__construct('Role');
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
      $this->RoleID = $id;
   }

   public function getObjectId() {
      return $this->RoleID;
   }

   public function __sleep() {
      return [
            'objectName',
            'RoleID',
            'CreationTimestamp',
            'ModificationTimestamp',
            'DisplayName',
            'Description',
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
    * @return UmgtRole The domain object for further usage.
    */
   public function setDisplayName($value) {
      $this->setProperty('DisplayName', $value);

      return $this;
   }

   /**
    * @return UmgtRole The domain object for further usage.
    */
   public function deleteDisplayName() {
      $this->deleteProperty('DisplayName');

      return $this;
   }

   /**
    * @return string The value for property "Description".
    */
   public function getDescription() {
      return $this->getProperty('Description');
   }

   /**
    * @param string $value The value to set for property "Description".
    *
    * @return UmgtRole The domain object for further usage.
    */
   public function setDescription($value) {
      $this->setProperty('Description', $value);

      return $this;
   }

   /**
    * @return UmgtRole The domain object for further usage.
    */
   public function deleteDescription() {
      $this->deleteProperty('Description');

      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*UmgtRoleBase:end*>
