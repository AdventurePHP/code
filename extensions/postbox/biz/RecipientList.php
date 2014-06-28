<?php
namespace APF\extensions\postbox\biz;

/**
 * This class represents the "APF\extensions\postbox\biz\RecipientList" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class RecipientList extends RecipientListBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * use APF\extensions\postbox\biz\RecipientList;
    * $object = new RecipientList();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null) {
      parent::__construct();
   }

}
