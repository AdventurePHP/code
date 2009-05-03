<?php
   /**
    * Represents the User domain object of the guestbook.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.05.2009<br />
    */
   final class User
   {

      /**
       * @private
       * The user name.
       */
      private $username;

      /**
       * @private
       * The user's password.
       */
      private $password;

      /**
       * @private
       * The user's name.
       */
      private $name;

      /**
       * @private
       * The user's email address.
       */
      private $email;

      public function getUsername(){
         return $this->username;
      }

      public function getPassword(){
         return $this->password;
      }

      public function getName(){
         return $this->name;
      }

      public function getEmail(){
         return $this->email;
      }

      public function setUsername($username){
         $this->username = $username;
      }

      public function setPassword($password){
         $this->password = $password;
      }

      public function setName($name){
         $this->name = $name;
      }

      public function setEmail($email){
         $this->email = $email;
      }

    // end class
   }
?>