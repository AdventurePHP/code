<?php

//<*UmgtVisibilityDefinitionBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtVisibilityDefinition. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtVisibilityDefinition" which extends this base-class.
 */
import('modules::genericormapper::data', 'GenericDomainObject');

/**
 * @package modules::usermanagement::biz::model
 * @class UmgtVisibilityDefinitionBase
 * 
 * This class provides the descriptive getter and setter methods for the "UmgtVisibilityDefinition" domain object.
 */
abstract class UmgtVisibilityDefinitionBase extends GenericDomainObject {

   public function __construct($objectName = null){
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

}

// DO NOT CHANGE THIS COMMENT! <*UmgtVisibilityDefinitionBase:end*>

/**
 * @package modules::usermanagement::biz::model
 * @class UmgtVisibilityDefinition
 * 
 * This class represents the "UmgtVisibilityDefinition" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class UmgtVisibilityDefinition extends UmgtVisibilityDefinitionBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * $object = new UmgtVisibilityDefinition();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null){
      parent::__construct();
   }

}

?>