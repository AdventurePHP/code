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
   import('core::session','SessionManager');
   import('tools::link','FrontcontrollerLinkHandler');
   import('modules::pager::biz','PagerManagerFabric');
   
   /**
    * @package modules::guestbook2009::biz
    * @class GuestbookService
    * 
    * Implements the central business component of the guestbook. Must be initialized with
    * the DIServiceManager to get the necessary information injected (pager config).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   final class GuestbookService extends APFObject {

      /**
       * Stores the pager instance for further usage.
       * @var PagerManager
       */
      private $pager = null;

      /**
       * Defines the name of the pager config section.
       * @var string
       */
      private $pagerConfigSection;

      /**
       * @public
       *
       * Implements an initializer method for the DIServiceManager to inject the
       * name of the pager's config section.
       *
       * @param string $pagerConfigSection The name of the config section.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 13.06.2009<br />
       */
      public function setPagerConfigSection($pagerConfigSection){
         $this->pagerConfigSection = $pagerConfigSection;
      }

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

         $t = &Singleton::getInstance('BenchmarkTimer');
         $t->start('loadPagedEntryList');

         $pager = &$this->getPager();

         $model = &$this->getServiceObject('modules::guestbook2009::biz','GuestbookModel');
         $entryIds = $pager->loadEntries(array('GuestbookID' => $model->getGuestbookId()));

         $entries = array();
         $mapper = &$this->getMapper();
         foreach($entryIds as $entryId){
            $entries[] = $mapper->loadEntry($entryId);
         }

         $t->stop('loadPagedEntryList');
         return $entries;

       // end function
      }

      /**
       * @public
       *
       * Returns the string representation of the current pager status.
       *
       * @return string The pager output.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 13.06.2009<br />
       */
      public function getPagerOutput(){
         $model = &$this->getServiceObject('modules::guestbook2009::biz','GuestbookModel');
         $pager = &$this->getPager();
         return $pager->getPager(array('GuestbookID' => $model->getGuestbookId()));
      }

      /**
       * @public
       *
       * Returns the pager's url params.
       *
       * @return string[] The pager's url params.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 13.06.2009<br />
       */
      public function getPagerURLParams(){
         $pager = &$this->getPager();
         return $pager->getPagerURLParameters();
      }

      /**
       * @private
       *
       * Returns the pager instance for the current guestbook instance.
       * Internally abstracts the access of the pager instance to enable
       * lazy initialization.
       *
       * @return PagerManager The pager instance.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 13.06.2009<br />
       */
      private function &getPager(){
         
         if($this->pager === null){
            $pMF = &$this->getServiceObject('modules::pager::biz','PagerManagerFabric');
            $this->pager = &$pMF->getPagerManager($this->pagerConfigSection);
         }
         
         return $this->pager;
      
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
         $mapper = &$this->getMapper();
         return $mapper->loadEntryListForSelection();
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
         $mapper = &$this->getMapper();
         return $mapper->loadEntry($id);
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
         $mapper = &$this->getMapper();
         return $mapper->loadGuestbook();
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
            $mapper = &$this->getMapper();
            $mapper->deleteEntry($entry);
          // end if
         }

         // display the admin start page
         $link = FrontcontrollerLinkHandler::generateLink(
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
         $model = &$this->getServiceObject('modules::guestbook2009::biz','GuestbookModel');
         $guestbookId = $model->getGuestbookId();
         $session = new SessionManager('modules::guestbook2009::biz::'.$guestbookId);
         $session->deleteSessionData('LoggedIn');
         
         // display the list view
         $link = FrontcontrollerLinkHandler::generateLink(
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

         $model = &$this->getServiceObject('modules::guestbook2009::biz','GuestbookModel');
         $guestbookId = $model->getGuestbookId();
         $session = new SessionManager('modules::guestbook2009::biz::'.$guestbookId);
         $loggedId = $session->loadSessionData('LoggedIn');

         // redirect to admin page
         if($loggedId !== 'true'){
            $startLink = FrontcontrollerLinkHandler::generateLink(
               $_SERVER['REQUEST_URI'],
               array(
                  'gbview' => 'list',
                  'adminview' => null
               )
            );
            HeaderManager::forward($startLink);
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

         $mapper = &$this->getMapper();
         if($mapper->validateCredentials($user)){

            // log user in
            $model = &$this->getServiceObject('modules::guestbook2009::biz','GuestbookModel');
            $guestbookId = $model->getGuestbookId();
            $session = new SessionManager('modules::guestbook2009::biz::'.$guestbookId);
            $session->saveSessionData('LoggedIn','true');

            // redirect to admin page
            $adminLink = FrontcontrollerLinkHandler::generateLink(
               $_SERVER['REQUEST_URI'],
               array(
                  'gbview' => 'admin',
                  'adminview' => null
               )
            );
            HeaderManager::forward($adminLink);

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

         $mapper = &$this->getMapper();
         $mapper->saveEntry($entry);

         // Forward to the desired view to prevent F5-bugs.
         $entryId = $entry->getId();
         if(!empty($entryId)){
            $link = FrontcontrollerLinkHandler::generateLink(
               $_SERVER['REQUEST_URI'],
               array(
                  'gbview' => 'admin'
               )
            );
         } else {
            $link = FrontcontrollerLinkHandler::generateLink(
               $_SERVER['REQUEST_URI'],
               array(
                  'gbview' => 'list'
               )
            );
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
      private function &getMapper(){
         return $this->getDIServiceObject('modules::guestbook2009::data','GuestbookMapper');
      }

    // end class
   }
?>