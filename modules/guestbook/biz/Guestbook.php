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

/**
 * @package modules::guestbook::biz
 * @class Guestbook
 *
 *  Guestbook domain object.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 12.04.2007<br />
 */
final class Guestbook {

   /**
    * @private
    *  Id of the guestbook.
    */
   private $id = null;

   /**
    * @private
    *  Name of the guestbook.
    */
   private $name;

   /**
    * @private
    *  Description of the guestbook.
    */
   private $description;

   /**
    * @private
    *  Entries of the giestbook.
    */
   private $entries = array();

   /**
    * @private
    *  Admin username.
    */
   private $adminUsername;

   /**
    * @private
    *  Admin password.
    */
   private $adminPassword;

   /**
    * @public
    *
    * Returns the list of entries of the guestbook.
    *
    * @return Entry[] The entries list
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 12.04.2007<br />
    */
   public function getEntries() {
      return $this->entries;
   }

   /**
    * @public
    *
    *  Fills the entry list.
    *
    * @param Entry[] $entries A list of entries
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.04.2007<br />
    */
   public function setEntries(array $entries) {
      $this->entries = $entries;
   }

   /**
    * @public
    *
    * Adds an entry to the list.
    *
    * @param Entry $entry An entry object
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.04.2007<br />
    */
   public function addEntry(Entry $entry) {
      $this->entries[] = $entry;
   }

   public function getId() {
      return $this->id;
   }

   public function setId($id) {
      $this->id = $id;
   }

   public function getName() {
      return $this->name;
   }

   public function setName($name) {
      $this->name = $name;
   }

   public function getDescription() {
      return $this->description;
   }

   public function setDescription($description) {
      $this->description = $description;
   }

   public function getAdminUsername() {
      return $this->adminUsername;
   }

   public function setAdminUsername($adminUsername) {
      $this->adminUsername = $adminUsername;
   }

   public function getAdminPassword() {
      return $this->adminPassword;
   }

   public function setAdminPassword($adminPassword) {
      $this->adminPassword = $adminPassword;
   }

}
