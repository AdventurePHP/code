<?php
namespace APF\extensions\news\biz;

//<*NewsBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for News. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "News" which extends this base-class.
 */
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * This class provides the descriptive getter and setter methods for the "APF\extensions\news\biz\News" domain object.
 */
abstract class NewsBase extends GenericDomainObject {

   public function __construct($objectName = null) {
      parent::__construct('News');
   }

   /**
    * @return string The value for property "AppKey".
    */
   public function getAppKey() {
      return $this->getProperty('AppKey');
   }

   /**
    * @param string $value The value to set for property "AppKey".
    *
    * @return News The domain object for further usage.
    */
   public function setAppKey($value) {
      $this->setProperty('AppKey', $value);

      return $this;
   }

   /**
    * @return News The domain object for further usage.
    */
   public function deleteAppKey() {
      $this->deleteProperty('AppKey');

      return $this;
   }

   /**
    * @return string The value for property "Author".
    */
   public function getAuthor() {
      return $this->getProperty('Author');
   }

   /**
    * @param string $value The value to set for property "Author".
    *
    * @return News The domain object for further usage.
    */
   public function setAuthor($value) {
      $this->setProperty('Author', $value);

      return $this;
   }

   /**
    * @return News The domain object for further usage.
    */
   public function deleteAuthor() {
      $this->deleteProperty('Author');

      return $this;
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
    * @return News The domain object for further usage.
    */
   public function setTitle($value) {
      $this->setProperty('Title', $value);

      return $this;
   }

   /**
    * @return News The domain object for further usage.
    */
   public function deleteTitle() {
      $this->deleteProperty('Title');

      return $this;
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
    * @return News The domain object for further usage.
    */
   public function setText($value) {
      $this->setProperty('Text', $value);

      return $this;
   }

   /**
    * @return News The domain object for further usage.
    */
   public function deleteText() {
      $this->deleteProperty('Text');

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

// DO NOT CHANGE THIS COMMENT! <*NewsBase:end*>
