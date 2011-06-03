<?php

class UmgtUserSessionStore extends APFObject {

   /**
    * @var GenericDomainObject The current user;
    */
   private $user;

   /**
    * @return GenericDomainObject The currently logged-in user.
    */
   public function getUser() {
      return $this->user;
   }

   public function setUser(GenericDomainObject $user) {
      $this->user = $user;
   }

   public function isLoggedIn() {
      return $this->user !== null;
   }

   public function isLoggedOut() {
      return!$this->isLoggedIn();
   }

   public function logout() {
      $this->user = null;
   }

}

?>