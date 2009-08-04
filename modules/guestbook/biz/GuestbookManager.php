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

   import('modules::guestbook::biz','Guestbook');
   import('modules::guestbook::biz','Entry');
   import('modules::guestbook::biz','Comment');
   import('modules::guestbook::data','GuestbookMapper');
   import('modules::pager::biz','PagerManagerFabric');
   import('core::session','SessionManager');


   /**
   *  @namespace modules::guestbook::biz
   *  @class GuestbookManager
   *
   *  Business component of the guestbook module.
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class GuestbookManager extends coreObject
   {

      /**
      *  @private
      *  The id of the guestbook.
      */
      protected $__GuestbookID;


      /**
      *  @private
      *  Container of the guestbook.
      */
      protected $__Guestbook = null;


      /**
      *  @private
      *  Instance of the session manager.
      */
      protected $__sessMgr = null;


      public function GuestbookManager(){
      }


      /**
      *  @public
      *
      *  Implements the init() method used with the service manager.
      *
      *  @param string $initParam The guestbook id
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      */
      public function init($initParam){
         $this->__GuestbookID = $initParam;
         $this->__sessMgr = new SessionManager('Guestbook');
       // end function
      }


      /**
      *  @public
      *
      *  Loads a guestbook.
      *
      *  @return Guestbook $guestbook The guestbook domain object structure
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      */
      public function loadGuestbook(){

         if($this->__Guestbook == null){

            $pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
            $pM = &$pMF->getPagerManager('Guestbook');

            $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');
            $this->__Guestbook = $gM->loadGuestbookByID($this->__GuestbookID);

            $EntryIDs = $pM->loadEntries(array('GuestbookID' => $this->__GuestbookID));

            $Entries = array();

            for($i = 0; $i < count($EntryIDs); $i++){
               $Entries[] = $gM->loadEntryWithComments($EntryIDs[$i]);
             // end for
            }

            // add entries
            $this->__Guestbook->setEntries($Entries);

          // end if
         }

         return $this->__Guestbook;

       // end function
      }


      /**
      *  @public
      *
      *  Loads a guestbook object.
      *
      *  @return object $Guestbook; G�stebuch-Objekt ohne Eintr�ge
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 21.04.2007<br />
      */
      public function loadGuestbookObject(){
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');
         return $gM->loadGuestbookByID($this->__GuestbookID);
       // end function
      }


      /**
      *  @public
      *
      *  Returns the URL params of the pager configuration.
      *
      *  @return array $urlParameter The pager url params
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      */
      public function getURLParameters(){
         $pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
         $pM = &$pMF->getPagerManager('Guestbook');
         return $pM->getPagerURLParameters();
       // end function
      }


      /**
      *  @public
      *
      *  Returns the HTML representation of the pager.
      *
      *  @return string $pager The pager representation
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      */
      public function getPager(){
         $pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
         $pM = &$pMF->getPagerManager('Guestbook');
         return $pM->getPager(array('GuestbookID' => $this->__GuestbookID));
       // end function
      }


      /**
      *  @public
      *
      *  Saves an entry object
      *
      *  @param Entry $entry The entry object
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 14.04.2007<br />
      */
      public function saveEntry($entry){

         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         $guestbook = $gM->loadGuestbookByID($this->__GuestbookID);
         $guestbook->addEntry($entry);
         $gM->saveGuestbook($guestbook);

         // forward to the target page
         $urlParams = $this->getURLParameters();
         $link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array($urlParams['StartName'] => '', $urlParams['CountName'] => '', 'gbview' => 'display'));
         header('Location: '.$link);

       // end function
      }


      /**
      *  @public
      *
      *  Saves a comment object.
      *
      *  @param string $entryID The id of an entry
      *  @param Comment $comment The comment object
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      *  Version 0.2, 19.05.2007 (Added the redirect url generation)<br />
      */
      public function saveComment($EntryID,$Comment){

         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         $Guestbook = $gM->loadGuestbookByID($this->__GuestbookID);
         $Entry = $gM->loadEntryByID($EntryID);
         $Entry->addComment($Comment);
         $Guestbook->addEntry($Entry);
         $gM->saveGuestbook($Guestbook);

         // forward to the target page
         $urlParams = $this->getURLParameters();
         $link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array($urlParams['StartName'] => '', $urlParams['CountName'] => '', 'gbview' => 'display','commentid' => '', 'entryid' => ''));
         header('Location: '.$link);

       // end function
      }


      /**
      *  @public
      *
      *  Loads an entry by id.
      *
      *  @param string $entryID Id of the desired entry
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      public function loadEntry($entryID){
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');
         return $gM->loadEntryByID($entryID);
       // end function
      }


      /**
      *  @public
      *
      *  Loads a comment by id.
      *
      *  @param string $commentID The comment id
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 19.05.2007<br />
      */
      public function loadComment($commentID){
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');
         return $gM->loadCommentByID($commentID);
       // end function
      }


      /**
      *  @public
      *
      *  Validates the login credentials.
      *
      *  @param string $username Usernam
      *  @param string $password Password
      *  @return bool $login true | false
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      public function validateCrendentials($username,$password){

         $guestbook = $this->loadGuestbookObject();

         if($guestbook->get('Admin_Username') == $username && $guestbook->get('Admin_Password') == $password){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Deletes an guestbook entry.
      *
      *  @param Entry $entry The guestbook entry
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      public function deleteEntry($entry){
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');
         $gM->deleteEntry($entry);
       // end function
      }


      /**
      *  @public
      *
      *  Deletes a comment.
      *
      *  @param Comment $comment The comment object
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 19.05.2007<br />
      */
      public function deleteComment($comment){
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');
         $gM->deleteComment($comment);
       // end function
      }

    // end class
   }
?>