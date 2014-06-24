<?php
namespace APF\extensions\postbox\biz;

//<*MessageBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for Message. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "Message" which extends this base-class.
 */
use APF\extensions\postbox\biz\AbstractMessage;

/**
 * @package APF\extensions\postbox\biz
 * @class MessageBase
 *
 * This class provides the descriptive getter and setter methods for the "APF\extensions\postbox\biz\Message" domain object.
 */
abstract class MessageBase extends AbstractMessage {

   public function __construct($objectName = null) {
      parent::__construct('Message');
   }

   /**
    * @return string The value for property "Text".
    */
   public function getText() {
      return $this->getProperty('Text');
   }

   /**
    * @param string $value The value to set for property "Text".
    *
    * @return Message The domain object for further usage.
    */
   public function setText($value) {
      $this->setProperty('Text', $value);

      return $this;
   }

   /**
    * @return Message The domain object for further usage.
    */
   public function deleteText() {
      $this->deleteProperty('Text');

      return $this;
   }

   /**
    * @return string The value for property "AuthorNameFallback".
    */
   public function getAuthorNameFallback() {
      return $this->getProperty('AuthorNameFallback');
   }

   /**
    * @param string $value The value to set for property "AuthorNameFallback".
    *
    * @return Message The domain object for further usage.
    */
   public function setAuthorNameFallback($value) {
      $this->setProperty('AuthorNameFallback', $value);

      return $this;
   }

   /**
    * @return Message The domain object for further usage.
    */
   public function deleteAuthorNameFallback() {
      $this->deleteProperty('AuthorNameFallback');

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

// DO NOT CHANGE THIS COMMENT! <*MessageBase:end*>
