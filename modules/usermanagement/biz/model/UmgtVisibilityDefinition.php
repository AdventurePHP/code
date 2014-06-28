<?php
namespace APF\modules\usermanagement\biz\model;

/**
 * This class represents the "APF\modules\usermanagement\biz\model\UmgtVisibilityDefinition" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class UmgtVisibilityDefinition extends UmgtVisibilityDefinitionBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * use APF\modules\usermanagement\biz\model\UmgtVisibilityDefinition;
    * $object = new UmgtVisibilityDefinition();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null) {
      parent::__construct();
   }

}
