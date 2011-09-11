<?php

//<*UmgtGroupBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtGroup. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtGroup" which extends this base-class.
 */
import('modules::genericormapper::data', 'GenericDomainObject');

/**
 * @package modules::usermanagement::biz::model
 * @class UmgtGroupBase
 * 
 * This class provides the descriptive getter and setter methods for the "UmgtGroup" domain object.
 */
abstract class UmgtGroupBase extends GenericDomainObject {

   public function __construct($objectName = null){
      parent::__construct('Group');
   }

   /**
    * @return string The value for property "DisplayName".
    */
   public function getDisplayName() {
      return $this->getProperty('DisplayName');
   }

   /**
    * @param string $value The value to set for property "DisplayName".
    * @return UmgtGroup The domain object for further usage.
    */
   public function setDisplayName($value) {
      $this->setProperty('DisplayName', $value);
      return $this;
   }

   /**
    * @return UmgtGroup The domain object for further usage.
    */
   public function deleteDisplayName() {
      $this->deleteProperty('DisplayName');
      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*UmgtGroupBase:end*>

/**
 * @package modules::usermanagement::biz::model
 * @class UmgtGroup
 * 
 * This class represents the "UmgtGroup" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class UmgtGroup extends UmgtGroupBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * $object = new UmgtGroup();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null){
      parent::__construct();
   }

}

?>