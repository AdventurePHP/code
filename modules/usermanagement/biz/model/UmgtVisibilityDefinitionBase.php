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

//<*UmgtVisibilityDefinitionBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtVisibilityDefinition. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtVisibilityDefinition" which extends this base-class.
 */
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * This class provides the descriptive getter and setter methods for the "APF\modules\usermanagement\biz\model\UmgtVisibilityDefinition" domain object.
 */
abstract class UmgtVisibilityDefinitionBase extends GenericDomainObject {

   public function __construct($objectName = null) {
      parent::__construct('AppProxy');
   }

   /**
    * @return string The value for property "AppObjectId".
    */
   public function getAppObjectId() {
      return $this->getProperty('AppObjectId');
   }

   /**
    * @param string $value The value to set for property "AppObjectId".
    *
    * @return UmgtVisibilityDefinition The domain object for further usage.
    */
   public function setAppObjectId($value) {
      $this->setProperty('AppObjectId', $value);

      return $this;
   }

   /**
    * @return UmgtVisibilityDefinition The domain object for further usage.
    */
   public function deleteAppObjectId() {
      $this->deleteProperty('AppObjectId');

      return $this;
   }

   /**
    * @return string The value for property "ReadPermission".
    */
   public function getReadPermission() {
      return $this->getProperty('ReadPermission');
   }

   /**
    * @param string $value The value to set for property "ReadPermission".
    *
    * @return UmgtVisibilityDefinition The domain object for further usage.
    */
   public function setReadPermission($value) {
      $this->setProperty('ReadPermission', $value);

      return $this;
   }

   /**
    * @return UmgtVisibilityDefinition The domain object for further usage.
    */
   public function deleteReadPermission() {
      $this->deleteProperty('ReadPermission');

      return $this;
   }

   /**
    * @return string The value for property "WritePermission".
    */
   public function getWritePermission() {
      return $this->getProperty('WritePermission');
   }

   /**
    * @param string $value The value to set for property "WritePermission".
    *
    * @return UmgtVisibilityDefinition The domain object for further usage.
    */
   public function setWritePermission($value) {
      $this->setProperty('WritePermission', $value);

      return $this;
   }

   /**
    * @return UmgtVisibilityDefinition The domain object for further usage.
    */
   public function deleteWritePermission() {
      $this->deleteProperty('WritePermission');

      return $this;
   }

   /**
    * @return string The value for property "LinkPermission".
    */
   public function getLinkPermission() {
      return $this->getProperty('LinkPermission');
   }

   /**
    * @param string $value The value to set for property "LinkPermission".
    *
    * @return UmgtVisibilityDefinition The domain object for further usage.
    */
   public function setLinkPermission($value) {
      $this->setProperty('LinkPermission', $value);

      return $this;
   }

   /**
    * @return UmgtVisibilityDefinition The domain object for further usage.
    */
   public function deleteLinkPermission() {
      $this->deleteProperty('LinkPermission');

      return $this;
   }

   /**
    * @return string The value for property "DeletePermission".
    */
   public function getDeletePermission() {
      return $this->getProperty('DeletePermission');
   }

   /**
    * @param string $value The value to set for property "DeletePermission".
    *
    * @return UmgtVisibilityDefinition The domain object for further usage.
    */
   public function setDeletePermission($value) {
      $this->setProperty('DeletePermission', $value);

      return $this;
   }

   /**
    * @return UmgtVisibilityDefinition The domain object for further usage.
    */
   public function deleteDeletePermission() {
      $this->deleteProperty('DeletePermission');

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

// DO NOT CHANGE THIS COMMENT! <*UmgtVisibilityDefinitionBase:end*>
