<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::guestbook2009::biz','Entry');
   import('modules::guestbook2009::biz','Guestbook');
   import('modules::guestbook2009::biz','User');
   import('tools::http','HeaderManager');
   import('modules::guestbook2009::biz','GuestbookModel');
   import('core::session','sessionManager');
   
   /**
    * Description of GuestbookService
    *
    * @author Administrator
    */
   final class GuestbookService extends coreObject
   {
   
      public function loadPagedEntryList(){
         $mapper = &$this->__getMapper();
         return $mapper->loadEntryList();
       // end function
      }

      public function loadGuestbook(){
      }


      /**
       * @public
       * 
       * Implements the login helper method called by the document controller. Returns false, in
       * case of login errors or logs the user in and redirects to the admin page. 
       * 
       * @param string $user The user object containing the username and password typed by the user.
       * @return boolean False in case, the credential check failed, true otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 16.05.2009<br />
       */
      public function validateCredentials($user){

         $mapper = &$this->__getMapper();
         if($mapper->validateCredentials($user)){

            // log user in
            $model = &$this->__getServiceObject('modules::guestbook2009::biz','GuestbookModel');
            $guestbookId = $model->get('GuestbookId');
            $session = new sessionManager('modules::guestbook2009::biz::'.$guestbookId);
            $session->saveSessionData('LoggedIn','true');

            // redirect to admin page
            HeaderManager::forward('./?gbview=admin');

          // end if
         }
         return false;
      
       // end function
      }

      /**
       * @public
       *
       * Saves the entry and forwards to the list view.
       *
       * @param Entry $entry The guestbook entry to save.
       * 
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.05.2009<br />
       */
      public function saveEntry($entry){

         $mapper = &$this->__getMapper();
         $mapper->saveEntry($entry);

         // Forward to the desired view to prevent F5-bugs.
         HeaderManager::forward('./?pagepart=list');

       // end function
      }

      /**
       * @private
       *
       * Returns the configured instance of the guestbook's data component.
       *
       * @return GuestbookMapper The mapper instance.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.05.2009<br />
       */
      private function &__getMapper(){
         return $this->__getDIServiceObject('modules::guestbook2009::data','GuestbookMapper');
      }
      
      // Mehrfache Widerverwendbarkeit: durch unterschiedliche Datenbanken!
      // Ansonsten über mehrere DAOs, die per z.B. Registry in der index.php
      // konfigurierbar sind!

    // end class
   }
?>