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

//<*UmgtUserBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtUser. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtUser" which extends this base-class.
 */
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * This class provides the descriptive getter and setter methods for the "APF\modules\usermanagement\biz\model\UmgtUser" domain object.
 */
abstract class UmgtUserBase extends GenericDomainObject {

   /**
    * @var string The value for property "DisplayName".
    */
   protected $DisplayName;

   /**
    * @var string The value for property "FirstName".
    */
   protected $FirstName;

   /**
    * @var string The value for property "LastName".
    */
   protected $LastName;

   /**
    * @var string The value for property "StreetName".
    */
   protected $StreetName;

   /**
    * @var string The value for property "StreetNumber".
    */
   protected $StreetNumber;

   /**
    * @var string The value for property "ZIPCode".
    */
   protected $ZIPCode;

   /**
    * @var string The value for property "City".
    */
   protected $City;

   /**
    * @var string The value for property "EMail".
    */
   protected $EMail;

   /**
    * @var string The value for property "Phone".
    */
   protected $Phone;

   /**
    * @var string The value for property "Mobile".
    */
   protected $Mobile;

   /**
    * @var string The value for property "Username".
    */
   protected $Username;

   /**
    * @var string The value for property "Password".
    */
   protected $Password;

   /**
    * @var string The value for property "DynamicSalt".
    */
   protected $DynamicSalt;

   /**
    * @var string The value for property "DateOfBirth".
    */
   protected $DateOfBirth;

   /**
    * @var string The value for property "ForgotPasswordHash".
    */
   protected $ForgotPasswordHash;

   /**
    * @var int The value for the object's ID.
    */
   protected $UserID;

   /**
    * @var string The creation timestamp.
    */
   protected $CreationTimestamp;

   /**
    * @var string The modification timestamp.
    */
   protected $ModificationTimestamp;

   protected $propertyNames = [
         'UserID',
         'CreationTimestamp',
         'ModificationTimestamp',
         'DisplayName',
         'FirstName',
         'LastName',
         'StreetName',
         'StreetNumber',
         'ZIPCode',
         'City',
         'EMail',
         'Phone',
         'Mobile',
         'Username',
         'Password',
         'DynamicSalt',
         'DateOfBirth',
         'ForgotPasswordHash'
   ];

   public function __construct(string $objectName = null) {
      parent::__construct('User');
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
      $this->UserID = $id;

      return $this;
   }

   public function getObjectId() {
      return $this->UserID;
   }

   public function __sleep() {
      return [
            'objectName',
            'UserID',
            'CreationTimestamp',
            'ModificationTimestamp',
            'DisplayName',
            'FirstName',
            'LastName',
            'StreetName',
            'StreetNumber',
            'ZIPCode',
            'City',
            'EMail',
            'Phone',
            'Mobile',
            'Username',
            'Password',
            'DynamicSalt',
            'DateOfBirth',
            'ForgotPasswordHash',
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
    * @return UmgtUser The domain object for further usage.
    */
   public function setDisplayName($value) {
      $this->setProperty('DisplayName', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteDisplayName() {
      $this->deleteProperty('DisplayName');

      return $this;
   }

   /**
    * @return string The value for property "FirstName".
    */
   public function getFirstName() {
      return $this->getProperty('FirstName');
   }

   /**
    * @param string $value The value to set for property "FirstName".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setFirstName($value) {
      $this->setProperty('FirstName', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteFirstName() {
      $this->deleteProperty('FirstName');

      return $this;
   }

   /**
    * @return string The value for property "LastName".
    */
   public function getLastName() {
      return $this->getProperty('LastName');
   }

   /**
    * @param string $value The value to set for property "LastName".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setLastName($value) {
      $this->setProperty('LastName', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteLastName() {
      $this->deleteProperty('LastName');

      return $this;
   }

   /**
    * @return string The value for property "StreetName".
    */
   public function getStreetName() {
      return $this->getProperty('StreetName');
   }

   /**
    * @param string $value The value to set for property "StreetName".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setStreetName($value) {
      $this->setProperty('StreetName', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteStreetName() {
      $this->deleteProperty('StreetName');

      return $this;
   }

   /**
    * @return string The value for property "StreetNumber".
    */
   public function getStreetNumber() {
      return $this->getProperty('StreetNumber');
   }

   /**
    * @param string $value The value to set for property "StreetNumber".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setStreetNumber($value) {
      $this->setProperty('StreetNumber', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteStreetNumber() {
      $this->deleteProperty('StreetNumber');

      return $this;
   }

   /**
    * @return string The value for property "ZIPCode".
    */
   public function getZIPCode() {
      return $this->getProperty('ZIPCode');
   }

   /**
    * @param string $value The value to set for property "ZIPCode".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setZIPCode($value) {
      $this->setProperty('ZIPCode', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteZIPCode() {
      $this->deleteProperty('ZIPCode');

      return $this;
   }

   /**
    * @return string The value for property "City".
    */
   public function getCity() {
      return $this->getProperty('City');
   }

   /**
    * @param string $value The value to set for property "City".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setCity($value) {
      $this->setProperty('City', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteCity() {
      $this->deleteProperty('City');

      return $this;
   }

   /**
    * @return string The value for property "EMail".
    */
   public function getEMail() {
      return $this->getProperty('EMail');
   }

   /**
    * @param string $value The value to set for property "EMail".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setEMail($value) {
      $this->setProperty('EMail', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteEMail() {
      $this->deleteProperty('EMail');

      return $this;
   }

   /**
    * @return string The value for property "Phone".
    */
   public function getPhone() {
      return $this->getProperty('Phone');
   }

   /**
    * @param string $value The value to set for property "Phone".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setPhone($value) {
      $this->setProperty('Phone', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deletePhone() {
      $this->deleteProperty('Phone');

      return $this;
   }

   /**
    * @return string The value for property "Mobile".
    */
   public function getMobile() {
      return $this->getProperty('Mobile');
   }

   /**
    * @param string $value The value to set for property "Mobile".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setMobile($value) {
      $this->setProperty('Mobile', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteMobile() {
      $this->deleteProperty('Mobile');

      return $this;
   }

   /**
    * @return string The value for property "Username".
    */
   public function getUsername() {
      return $this->getProperty('Username');
   }

   /**
    * @param string $value The value to set for property "Username".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setUsername($value) {
      $this->setProperty('Username', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteUsername() {
      $this->deleteProperty('Username');

      return $this;
   }

   /**
    * @return string The value for property "Password".
    */
   public function getPassword() {
      return $this->getProperty('Password');
   }

   /**
    * @param string $value The value to set for property "Password".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setPassword($value) {
      $this->setProperty('Password', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deletePassword() {
      $this->deleteProperty('Password');

      return $this;
   }

   /**
    * @return string The value for property "DynamicSalt".
    */
   public function getDynamicSalt() {
      return $this->getProperty('DynamicSalt');
   }

   /**
    * @param string $value The value to set for property "DynamicSalt".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setDynamicSalt($value) {
      $this->setProperty('DynamicSalt', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteDynamicSalt() {
      $this->deleteProperty('DynamicSalt');

      return $this;
   }

   /**
    * @return string The value for property "DateOfBirth".
    */
   public function getDateOfBirth() {
      return $this->getProperty('DateOfBirth');
   }

   /**
    * @param string $value The value to set for property "DateOfBirth".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setDateOfBirth($value) {
      $this->setProperty('DateOfBirth', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteDateOfBirth() {
      $this->deleteProperty('DateOfBirth');

      return $this;
   }

   /**
    * @return string The value for property "ForgotPasswordHash".
    */
   public function getForgotPasswordHash() {
      return $this->getProperty('ForgotPasswordHash');
   }

   /**
    * @param string $value The value to set for property "ForgotPasswordHash".
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setForgotPasswordHash($value) {
      $this->setProperty('ForgotPasswordHash', $value);

      return $this;
   }

   /**
    * @return UmgtUser The domain object for further usage.
    */
   public function deleteForgotPasswordHash() {
      $this->deleteProperty('ForgotPasswordHash');

      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*UmgtUserBase:end*>
