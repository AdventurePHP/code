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

//<*UmgtAuthTokenBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtAuthToken. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtAuthToken" which extends this base-class.
 */
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * This class provides the descriptive getter and setter methods for the "APF\modules\usermanagement\biz\model\UmgtAuthToken" domain object.
 */
abstract class UmgtAuthTokenBase extends GenericDomainObject {

   /**
    * @var string The value for property "Token".
    */
   protected $Token;

   /**
    * @var int The value for the object's ID.
    */
   protected $AuthTokenID;

   /**
    * @var string The creation timestamp.
    */
   protected $CreationTimestamp;

   /**
    * @var string The modification timestamp.
    */
   protected $ModificationTimestamp;

   protected $propertyNames = [
         'AuthTokenID',
         'CreationTimestamp',
         'ModificationTimestamp',
         'Token'
   ];

   public function __construct($objectName = null) {
      parent::__construct('AuthToken');
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
      $this->AuthTokenID = $id;
   }

   public function getObjectId() {
      return $this->AuthTokenID;
   }

   public function __sleep() {
      return [
            'objectName',
            'AuthTokenID',
            'CreationTimestamp',
            'ModificationTimestamp',
            'Token',
            'relatedObjects'
      ];
   }

   /**
    * @return string The value for property "Token".
    */
   public function getToken() {
      return $this->getProperty('Token');
   }

   /**
    * @param string $value The value to set for property "Token".
    *
    * @return UmgtAuthToken The domain object for further usage.
    */
   public function setToken($value) {
      $this->setProperty('Token', $value);

      return $this;
   }

   /**
    * @return UmgtAuthToken The domain object for further usage.
    */
   public function deleteToken() {
      $this->deleteProperty('Token');

      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*UmgtAuthTokenBase:end*>
