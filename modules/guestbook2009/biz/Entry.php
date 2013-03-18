<?php
namespace APF\modules\guestbook2009\biz;

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

/**
 * Represents the Entry domain object of the guestbook.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.05.2009<br />
 */
final class Entry {

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

   /**
    * @private
    * The creator of the entry.
    */
   private $user;

   /**
    * @private
    * Contains the id of the entry used to identify on update/delete.
    */
   private $id;

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

   public function setEditor($user) {
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
