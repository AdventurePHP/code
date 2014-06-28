<?php
namespace APF\extensions\postbox\biz;

//<*PostboxFolderBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for PostboxFolder. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "PostboxFolder" which extends this base-class.
 */
use APF\extensions\postbox\biz\AbstractPostboxFolder;

/**
 * This class provides the descriptive getter and setter methods for the "APF\extensions\postbox\biz\PostboxFolder" domain object.
 */
abstract class PostboxFolderBase extends AbstractPostboxFolder {

   public function __construct($objectName = null) {
      parent::__construct('PostboxFolder');
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
    * @return PostboxFolder The domain object for further usage.
    */
   public function setName($value) {
      $this->setProperty('Name', $value);

      return $this;
   }

   /**
    * @return PostboxFolder The domain object for further usage.
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

// DO NOT CHANGE THIS COMMENT! <*PostboxFolderBase:end*>
