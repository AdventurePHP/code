<?php
namespace APF\modules\usermanagement\biz\model;

//<*UmgtAuthTokenBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtAuthToken. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtAuthToken" which extends this base-class.
 */
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * @package APF\APF\modules\usermanagement\biz\model
 * @class UmgtAuthTokenBase
 *
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

/**
 * @package APF\APF\modules\usermanagement\biz\model
 * @class UmgtAuthToken
 *
 * This class represents the "APF\modules\usermanagement\biz\model\UmgtAuthToken" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class UmgtAuthToken extends UmgtAuthTokenBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * use APF\modules\usermanagement\biz\model\UmgtAuthToken;
    * $object = new UmgtAuthToken();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null) {
      parent::__construct();
   }

}
