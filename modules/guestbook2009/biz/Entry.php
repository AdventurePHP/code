<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
 * Represents the Entry domain object of the guestbook.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.05.2009<br />
 */
final class Entry {

   /**
    * The title of the entry.
    *
    * @var string $title
    */
   private $title;

   /**
    * The text of the entry.
    *
    * @var string $text
    */
   private $text;

   /**
    * The creation timestamp of the entry.
    *
    * @var string $creationTimestamp
    */
   private $creationTimestamp;

   /**
    * The modification timestamp of the entry.
    *
    * @var string $modificationTimestamp
    */
   private $modificationTimestamp;

   /**
    * The creator of the entry.
    *
    * @var User $user
    */
   private $user;

   /**
    * Contains the id of the entry used to identify on update/delete.
    *
    * @var string $id
    */
   private $id;

   /**
    * @return User The editor of this entry.
    */
   public function getEditor() {
      return $this->user;
   }

   public function getTitle() {
      return $this->title;
   }

   public function getText() {
      return $this->text;
   }

   public function getModificationTimestamp() {
      return $this->modificationTimestamp;
   }

   public function getCreationTimestamp() {
      return $this->creationTimestamp;
   }

   public function getId() {
      return $this->id;
   }

   public function setEditor(User $user) {
      $this->user = $user;
   }

   public function setTitle($title) {
      $this->title = $title;
   }

   public function setText($text) {
      $this->text = $text;
   }

   public function setModificationTimestamp($modificationTimestamp) {
      $this->modificationTimestamp = $modificationTimestamp;
   }

   public function setCreationTimestamp($creationTimestamp) {
      $this->creationTimestamp = $creationTimestamp;
   }

   public function setId($id) {
      $this->id = $id;
   }

}
