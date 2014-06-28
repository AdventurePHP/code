<?php
namespace APF\extensions\postbox\biz;

//<*RecipientListBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for RecipientList. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "RecipientList" which extends this base-class.
 */
use APF\extensions\postbox\biz\AbstractRecipientList;

/**
 * This class provides the descriptive getter and setter methods for the "APF\extensions\postbox\biz\RecipientList" domain object.
 */
abstract class RecipientListBase extends AbstractRecipientList {

   public function __construct($objectName = null) {
      parent::__construct('RecipientList');
   }

   /**
    * @return string The value for property "Name".
    */
   public function getName() {
      return $this->getProperty('Name');
   }

   /**
    * @param string $value The value to set for property "Name".
    *
    * @return RecipientList The domain object for further usage.
    */
   public function setName($value) {
      $this->setProperty('Name', $value);

      return $this;
   }

   /**
    * @return RecipientList The domain object for further usage.
    */
   public function deleteName() {
      $this->deleteProperty('Name');

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

// DO NOT CHANGE THIS COMMENT! <*RecipientListBase:end*>
