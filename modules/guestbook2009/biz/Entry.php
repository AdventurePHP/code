<?php
   /**
    * Represents the Entry domain object of the guestbook.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.05.2009<br />
    */
   final class Entry
   {

      /**
       * @private
       * The title of the entry.
       */
      private $title;

      /**
       * @private
       * The text of the entry. 
       */
      private $text;

      /**
       * @private
       * The creation timestamp of the entry. 
       */
      private $creationTimestamp;
      
      /**
       * @private
       * The modification timestamp of the entry. 
       */      
      private $modificationTimestamp;

      public function getTitle(){
         return $this->title;
      }

      public function getText(){
         return $this->text;
      }

      public function getModificationTimestamp(){
         return $this->modificationTimestamp;
      }

      public function getCreationTimestamp(){
         return $this->creationTimestamp;
      }

      public function setTitle($title){
         $this->title = $title;
      }

      public function setText($text){
         $this->text = $text;
      }

      public function setModificationTimestamp($modificationTimestamp){
         $this->modificationTimestamp = $modificationTimestamp;
      }

      public function setCreationTimestamp($creationTimestamp){
         $this->creationTimestamp = $creationTimestamp;
      }

    // end class
   }
?>