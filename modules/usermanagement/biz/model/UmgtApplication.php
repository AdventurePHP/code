<?php
namespace APF\modules\usermanagement\biz\model;

/**
 * @package APF\modules\usermanagement\biz\model
 * @class UmgtApplication
 *
 * This class represents the "APF\modules\usermanagement\biz\model\UmgtApplication" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class UmgtApplication extends UmgtApplicationBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * use APF\modules\usermanagement\biz\model\UmgtApplication;
    * $object = new UmgtApplication();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null) {
      parent::__construct();
   }

}
