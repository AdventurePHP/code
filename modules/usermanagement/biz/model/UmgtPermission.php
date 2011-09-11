<?php

//<*UmgtPermissionBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtPermission. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtPermission" which extends this base-class.
 */
import('modules::genericormapper::data', 'GenericDomainObject');

/**
 * @package modules::usermanagement::biz::model
 * @class UmgtPermissionBase
 * 
 * This class provides the descriptive getter and setter methods for the "UmgtPermission" domain object.
 */
abstract class UmgtPermissionBase extends GenericDomainObject {

   public function __construct($objectName = null){
      parent::__construct('Permission');
   }

   /**
    * @return string The value for property "DisplayName".
    */
   public function getDisplayName() {
      return $this->getProperty('DisplayName');
   }

   /**
    * @param string $value The value to set for property "DisplayName".
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

/**
 * @package modules::usermanagement::biz::model
 * @class UmgtPermission
 * 
 * This class represents the "UmgtPermission" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class UmgtPermission extends UmgtPermissionBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * $object = new UmgtPermission();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null){
      parent::__construct();
   }

}

?>