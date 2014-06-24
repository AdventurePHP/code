<?php
namespace APF\extensions\postbox\biz;

//<*MessageChannelBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for MessageChannel. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "MessageChannel" which extends this base-class.
 */
use APF\extensions\postbox\biz\AbstractMessageChannel;

/**
 * @package APF\extensions\postbox\biz
 * @class MessageChannelBase
 *
 * This class provides the descriptive getter and setter methods for the "APF\extensions\postbox\biz\MessageChannel" domain object.
 */
abstract class MessageChannelBase extends AbstractMessageChannel {

   public function __construct($objectName = null) {
      parent::__construct('MessageChannel');
   }

   /**
    * @return string The value for property "Title".
    */
   public function getTitle() {
      return $this->getProperty('Title');
   }

   /**
    * @param string $value The value to set for property "Title".
    *
    * @return MessageChannel The domain object for further usage.
    */
   public function setTitle($value) {
      $this->setProperty('Title', $value);

      return $this;
   }

   /**
    * @return MessageChannel The domain object for further usage.
    */
   public function deleteTitle() {
      $this->deleteProperty('Title');

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

// DO NOT CHANGE THIS COMMENT! <*MessageChannelBase:end*>
