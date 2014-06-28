<?php
namespace APF\modules\guestbook2009\biz;

/**
 * Represents the Guestbook domain object of the guestbook.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.05.2009<br />
 */
final class Guestbook {

   /**
    * The title of the guestbook.
    *
    * @var string $title
    */
   private $title;

   /**
    * The description of the guestbook.
    *
    * @var string $description
    */
   private $description;

   /**
    * The entries of the guestbook.
    *
    * @var Entry[] $entries
    */
   private $entries = array();

   public function getTitle() {
      return $this->title;
   }

   public function getDescription() {
      return $this->description;
   }

   /**
    * @return Entry[]
    */
   public function getEntries() {
      return $this->entries;
   }

   public function setTitle($title) {
      $this->title = $title;
   }

   public function setDescription($description) {
      $this->description = $description;
   }

   public function setEntries(array $entries) {
      $this->entries = $entries;
   }

   public function addEntry(Entry $entry) {
      $this->entries[] = $entry;
   }

}
