<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * http://adventure-php-framework.org.
 *
 * The APF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The APF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 * -->
 */
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
