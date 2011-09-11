<?php

//<*UmgtRoleBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtRole. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtRole" which extends this base-class.
 */
import('modules::genericormapper::data', 'GenericDomainObject');

/**
 * @package modules::usermanagement::biz::model
 * @class UmgtRoleBase
 * 
 * This class provides the descriptive getter and setter methods for the "UmgtRole" domain object.
 */
abstract class UmgtRoleBase extends GenericDomainObject {

   public function __construct($objectName = null){
      parent::__construct('Role');
   }

   /**
    * @return string The value for property "DisplayName".
    */
   public function getDisplayName() {
      return $this->getProperty('DisplayName');
   }

   /**
    * @param string $value The value to set for property "DisplayName".
    * @return UmgtRole The domain object for further usage.
    */
   public function setDisplayName($value) {
      $this->setProperty('DisplayName', $value);
      return $this;
   }

   /**
    * @return UmgtRole The domain object for further usage.
    */
   public function deleteDisplayName() {
      $this->deleteProperty('DisplayName');
      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*UmgtRoleBase:end*>

/**
 * @package modules::usermanagement::biz::model
 * @class UmgtRole
 * 
 * This class represents the "UmgtRole" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class UmgtRole extends UmgtRoleBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * $object = new UmgtRole();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null){
      parent::__construct();
   }

}

?>