<?php
namespace APF\modules\guestbook2009\biz;

/**
 * @package APF\APF\modules\guestbook2009\biz
 * @class Guestbook
 *
 * Represents the Guestbook domain object of the guestbook.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.05.2009<br />
 */
final class Guestbook {

   /**
    * @var string The title of the guestbook.
    */
   private $title;

   /**
    * @var string The description of the guestbook.
    */
   private $description;

   /**
    * @var Entry[] The entries of the guestbook.
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
