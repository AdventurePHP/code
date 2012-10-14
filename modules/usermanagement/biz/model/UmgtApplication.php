<?php

//<*UmgtApplicationBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtApplication. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtApplication" which extends this base-class.
 */
import('modules::genericormapper::data', 'GenericDomainObject');

/**
 * @package modules::usermanagement::biz::model
 * @class UmgtApplicationBase
 * 
 * This class provides the descriptive getter and setter methods for the "UmgtApplication" domain object.
 */
abstract class UmgtApplicationBase extends GenericDomainObject {

   public function __construct($objectName = null){
      parent::__construct('Application');
   }

   /**
    * @return string The value for property "DisplayName".
    */
   public function getDisplayName() {
      return $this->getProperty('DisplayName');
   }

   /**
    * @param string $value The value to set for property "DisplayName".
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

// DO NOT CHANGE THIS COMMENT! <*UmgtApplicationBase:end*>

/**
 * @package modules::usermanagement::biz::model
 * @class UmgtApplication
 * 
 * This class represents the "UmgtApplication" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class UmgtApplication extends UmgtApplicationBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * $object = new UmgtApplication();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null){
      parent::__construct();
   }

}
