<?php
namespace APF\modules\usermanagement\biz\model;

//<*UmgtVisibilityDefinitionTypeBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtVisibilityDefinitionType. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtVisibilityDefinitionType" which extends this base-class.
 */
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * @package modules::usermanagement::biz::model
 * @class UmgtVisibilityDefinitionTypeBase
 * 
 * This class provides the descriptive getter and setter methods for the "UmgtVisibilityDefinitionType" domain object.
 */
abstract class UmgtVisibilityDefinitionTypeBase extends GenericDomainObject {

   public function __construct($objectName = null){
      parent::__construct('AppProxyType');
   }

   /**
    * @return string The value for property "AppObjectName".
    */
   public function getAppObjectName() {
      return $this->getProperty('AppObjectName');
   }

   /**
    * @param string $value The value to set for property "AppObjectName".
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

// DO NOT CHANGE THIS COMMENT! <*UmgtVisibilityDefinitionTypeBase:end*>

/**
 * @package modules::usermanagement::biz::model
 * @class UmgtVisibilityDefinitionType
 * 
 * This class represents the "UmgtVisibilityDefinitionType" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class UmgtVisibilityDefinitionType extends UmgtVisibilityDefinitionTypeBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * $object = new UmgtVisibilityDefinitionType();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null){
      parent::__construct();
   }

}
