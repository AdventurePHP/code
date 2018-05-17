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

//<*UmgtVisibilityDefinitionTypeBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtVisibilityDefinitionType. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtVisibilityDefinitionType" which extends this base-class.
 */
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * This class provides the descriptive getter and setter methods for the "APF\modules\usermanagement\biz\model\UmgtVisibilityDefinitionType" domain object.
 */
abstract class UmgtVisibilityDefinitionTypeBase extends GenericDomainObject {

   /**
    * @var string The value for property "AppObjectName".
    */
   protected $AppObjectName;

   /**
    * @var int The value for the object's ID.
    */
   protected $AppProxyTypeID;

   /**
    * @var string The creation timestamp.
    */
   protected $CreationTimestamp;

   /**
    * @var string The modification timestamp.
    */
   protected $ModificationTimestamp;

   protected $propertyNames = [
         'AppProxyTypeID',
         'CreationTimestamp',
         'ModificationTimestamp',
         'AppObjectName'
   ];

   public function __construct(string $objectName = null) {
      parent::__construct('AppProxyType');
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
      $this->AppProxyTypeID = $id;

      return $this;
   }

   public function getObjectId() {
      return $this->AppProxyTypeID;
   }

   public function __sleep() {
      return [
            'objectName',
            'AppProxyTypeID',
            'CreationTimestamp',
            'ModificationTimestamp',
            'AppObjectName',
            'relatedObjects'
      ];
   }

   /**
    * @return string The value for property "AppObjectName".
    */
   public function getAppObjectName() {
      return $this->getProperty('AppObjectName');
   }

   /**
    * @param string $value The value to set for property "AppObjectName".
    *
    * @return UmgtVisibilityDefinitionType The domain object for further usage.
    */
   public function setAppObjectName($value) {
      $this->setProperty('AppObjectName', $value);

      return $this;
   }

   /**
    * @return UmgtVisibilityDefinitionType The domain object for further usage.
    */
   public function deleteAppObjectName() {
      $this->deleteProperty('AppObjectName');

      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*UmgtVisibilityDefinitionTypeBase:end*>
