<?php
namespace APF\modules\usermanagement\biz\model;

/**
 * This class represents the "APF\modules\usermanagement\biz\model\UmgtRole" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class UmgtRole extends UmgtRoleBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * use APF\modules\usermanagement\biz\model\UmgtRole;
    * $object = new UmgtRole();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null) {
      parent::__construct();
   }

}
