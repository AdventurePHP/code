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
   import('tools::link','frontcontrollerLinkHandler');
   
   /**
    * Description of GuestbookService
    *
    * @author Administrator
    */
   final class GuestbookService extends coreObject {


      /**
       * @public
       *
       * Loads a paged entry list.
       *
       * @return Entry[] The paged entry list.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.05.2009<br />
       */
      public function loadPagedEntryList(){
         $mapper = &$this->__getMapper();
         return $mapper->loadEntryList();
       // end function
      }


      /**
       * @public
       *
       * Loads a complete entry list for selection (backend!).
       *
       * @return Entry[] The entry list.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.05.2009<br />
       */
      public function loadEntryListForSelection(){
         $mapper = &$this->__getMapper();
         return $mapper->loadEntryListForSelection();
       // end function
      }


      /**
       * @public
       *
       * Loads a dedicated entry.
       *
       * @return Entry An entry.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.05.2009<br />
       */
      public function loadEntry($id){
         $mapper = &$this->__getMapper();
         return $mapper->loadEntry($id);
       // end function
      }


      /**
       * @public
       *
       * Loads the guestbook.
       *
       * @return Guestbook The current guestbook domain object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.05.2009<br />
       */
      public function loadGuestbook(){
         $mapper = &$this->__getMapper();
         return $mapper->loadGuestbook();
       // end function
      }


      /**
       * @public
       * 
       * Deletes the given entry.
       *
       * @param Entry $entry The entry domain object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.05.2009<br />
       */
      public function deleteEntry($entry){
         
         if($entry !== null){
            $mapper = &$this->__getMapper();
            $mapper->deleteEntry($entry);
          // end if
         }

         // display the admin start page
         $link = frontcontrollerLinkHandler::generateLink(
            $_SERVER['REQUEST_URI'],
            array(
               'gbview' => 'admin',
               'adminview' => null
            )
         );
         HeaderManager::forward($link);
         
       // end function
      }


      /**
       * @public
       *
       * Logs the user out and displays the list view.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.05.2009<br />
       */
      public function logout(){

         // logout by cleaning the session
         $model = &$this->__getServiceObject('modules::guestbook2009::biz','GuestbookModel');
         $guestbookId = $model->get('GuestbookId');
         $session = new sessionManager('modules::guestbook2009::biz::'.$guestbookId);
         $session->deleteSessionData('LoggedIn');
         
         // display the list view
         $link = frontcontrollerLinkHandler::generateLink(
            $_SERVER['REQUEST_URI'],
            array(
               'gbview' => 'list',
               'adminview' => null
            )
         );
         HeaderManager::forward($link);

       // end function
      }


      /**
       * @public
       *
       * Checks, whether the current a user is logged in and the admin backend
       * may be displayed. If no, the user is redirected to the list view.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.05.2009<br />
       */
      public function checkAccessAllowed(){

         $model = &$this->__getServiceObject('modules::guestbook2009::biz','GuestbookModel');
         $guestbookId = $model->get('GuestbookId');
         $session = new sessionManager('modules::guestbook2009::biz::'.$guestbookId);
         $loggedId = $session->loadSessionData('LoggedIn');

         // redirect to admin page
         if($loggedId !== 'true'){
            $startLink = frontcontrollerLinkHandler::generateLink(
               $_SERVER['REQUEST_URI'],
               array(
                  'gbview' => 'list',
                  'adminview' => null
               )
            );
            HeaderManager::forward($startLink);
          // end if
         }

       // end function
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
            $adminLink = frontcontrollerLinkHandler::generateLink(
               $_SERVER['REQUEST_URI'],
               array(
                  'gbview' => 'admin',
                  'adminview' => null
               )
            );
            HeaderManager::forward($adminLink);

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
         $entryId = $entry->getId();
         if(!empty($entryId)){
            $link = frontcontrollerLinkHandler::generateLink(
               $_SERVER['REQUEST_URI'],
               array(
                  'gbview' => 'admin'
               )
            );
          // end if
         }
         else{
            $link = frontcontrollerLinkHandler::generateLink(
               $_SERVER['REQUEST_URI'],
               array(
                  'gbview' => 'list'
               )
            );
          // end else
         }
         HeaderManager::forward($link);

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