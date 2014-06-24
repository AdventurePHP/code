<?php
namespace APF\extensions\postbox\biz;

/**
 * @package APF\extensions\postbox\biz
 * @class MessageChannel
 *
 * This class represents the "APF\extensions\postbox\biz\MessageChannel" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class MessageChannel extends MessageChannelBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * use APF\extensions\postbox\biz\MessageChannel;
    * $object = new MessageChannel();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null) {
      parent::__construct();
   }

}
