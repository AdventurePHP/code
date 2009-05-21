<?php
   /**
    * Represents the Guestbook domain object of the guestbook.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.05.2009<br />
    */
   final class Guestbook
   {

      /**
       * @private
       * The title of the guestbook.
       */
      private $title;

      /**
       * @private
       * The description of the guestbook.
       */
      private $description;

      /**
       * @private
       * The entries of the guestbook.
       */
      private $entries = array();

      public function getTitle(){
         return $this->title;
      }

      public function getDescription(){
         return $this->description;
      }

      public function getEntries(){
         return $this->entries;
      }

      public function setTitle($title){
         $this->title = $title;
      }

      public function setDescription($description){
         $this->description = $description;
      }

      public function setEntries($entries){
         $this->entries = $entries;
      }

      public function addEntry($entry){
         $this->entries[] = $entry;
      }

    // end class
   }
?>