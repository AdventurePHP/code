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

   public function __construct($objectName = null) {
      parent::__construct('AuthToken');
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

   /**
    * @return string The value for property "CreationTimestamp".
    */
   public function getCreationTimestamp() {
      return $this->getProperty('CreationTimestamp');
   }

   /**
    * @return string The value for property "ModificationTimestamp".
    */
   public function getModificationTimestamp() {
      return $this->getProperty('ModificationTimestamp');
   }

}

// DO NOT CHANGE THIS COMMENT! <*UmgtAuthTokenBase:end*>
