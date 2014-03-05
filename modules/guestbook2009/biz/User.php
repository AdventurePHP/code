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
 * @package APF\modules\guestbook2009\biz
 * @class User
 *
 * Represents the User domain object of the guestbook.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.05.2009<br />
 */
final class User {

   /**
    * @var string The user name.
    */
   private $username;

   /**
    * @var string The user's password.
    */
   private $password;

   /**
    * @var string The user's name.
    */
   private $name;

   /**
    * @var string The user's email address.
    */
   private $email;

   /**
    * @var string The user's website.
    */
   private $website;

   /**
    * @var string Contains the id of the entry used to identify on update/delete.
    */
   private $id;

   public function getUsername() {
      return $this->username;
   }

   public function getPassword() {
      return $this->password;
   }

   public function getName() {
      return $this->name;
   }

   public function getEmail() {
      return $this->email;
   }

   public function getWebsite() {
      return $this->website;
   }

   public function getId() {
      return $this->id;
   }

   public function setUsername($username) {
      $this->username = $username;
   }

   public function setPassword($password) {
      $this->password = $password;
   }

   public function setName($name) {
      $this->name = $name;
   }

   public function setEmail($email) {
      $this->email = $email;
   }

   public function setWebsite($website) {
      $this->website = $website;
   }

   public function setId($id) {
      $this->id = $id;
   }

}
