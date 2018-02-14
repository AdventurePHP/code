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
 * Represents the User domain object of the guestbook.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.05.2009<br />
 */
final class User {

   /**
    * The user name.
    *
    * @var string $username
    */
   private $username;

   /**
    * The user's password.
    *
    * @var string $password
    */
   private $password;

   /**
    * The user's name.
    *
    * @var string $name
    */
   private $name;

   /**
    * The user's email address.
    *
    * @var string $email
    */
   private $email;

   /**
    * The user's website.
    *
    * @var string $website
    */
   private $website;

   /**
    * Contains the id of the entry used to identify on update/delete.
    *
    * @var string $id
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
