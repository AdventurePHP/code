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

//<*UmgtApplicationBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtApplication. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtApplication" which extends this base-class.
 */
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * This class provides the descriptive getter and setter methods for the "APF\modules\usermanagement\biz\model\UmgtApplication" domain object.
 */
abstract class UmgtApplicationBase extends GenericDomainObject {

   /**
    * @var string The value for property "DisplayName".
    */
   protected $DisplayName;

   /**
    * @var int The value for the object's ID.
    */
   protected $ApplicationID;

   /**
    * @var string The creation timestamp.
    */
   protected $CreationTimestamp;

   /**
    * @var string The modification timestamp.
    */
   protected $ModificationTimestamp;

   protected $propertyNames = [
         'ApplicationID',
         'CreationTimestamp',
         'ModificationTimestamp',
         'DisplayName'
   ];

   public function __construct($objectName = null) {
      parent::__construct('Application');
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
      $this->ApplicationID = $id;
   }

   public function getObjectId() {
      return $this->ApplicationID;
   }

   public function __sleep() {
      return [
            'objectName',
            'ApplicationID',
            'CreationTimestamp',
            'ModificationTimestamp',
            'DisplayName',
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
    * @return UmgtApplication The domain object for further usage.
    */
   public function setDisplayName($value) {
      $this->setProperty('DisplayName', $value);

      return $this;
   }

   /**
    * @return UmgtApplication The domain object for further usage.
    */
   public function deleteDisplayName() {
      $this->deleteProperty('DisplayName');

      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*UmgtApplicationBase:end*>
