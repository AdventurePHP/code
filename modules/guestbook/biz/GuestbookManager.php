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

   import('modules::guestbook::biz', 'Guestbook');
   import('modules::guestbook::biz', 'Entry');
   import('modules::guestbook::biz', 'Comment');
   import('modules::guestbook::data', 'GuestbookMapper');
   import('modules::pager::biz', 'PagerManagerFabric');
   import('core::session', 'SessionManager');
   import('tools::http', 'HeaderManager');

   /**
    * @package modules::guestbook::biz
    * @class GuestbookManager
    *
    * Business component of the guestbook module.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.04.2007<br />
    */
   class GuestbookManager extends APFObject {

      /**
       * @var int The id of the guestbook.
       */
      private $guestbookId;
      
      /**
       * @var Guestbook Container of the guestbook.
       */
      private $guestbook = null;

      /**
       *  @public
       *
       *  Implements the init() method used with the service manager.
       *
       *  @param string $initParam The guestbook id
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 13.04.2007<br />
       */
      public function init($initParam) {
         $this->guestbookId = $initParam;
      }

      /**
       * @return PagerManager The pager for the current guestbook instance.
       */
      private function &getPager() {
         $pMF = &$this->getServiceObject('modules::pager::biz', 'PagerManagerFabric');
         return $pMF->getPagerManager('Guestbook');
      }

      /**
       *  @public
       *
       *  Loads a guestbook.
       *
       *  @return Guestbook The guestbook domain object structure
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 13.04.2007<br />
       */
      public function loadGuestbook() {

         if ($this->guestbook == null) {

            $gM = &$this->getMapper();
            $this->guestbook = $gM->loadGuestbookByID($this->guestbookId);

            $entryIds = $this->getPager()->loadEntries(array('GuestbookID' => $this->guestbookId));

            $entries = array();

            for ($i = 0; $i < count($entryIds); $i++) {
               $entries[] = $gM->loadEntryWithComments($entryIds[$i]);
            }

            $this->guestbook->setEntries($entries);
         }

         return $this->guestbook;
      }

      /**
       *  @public
       *
       *  Returns the URL params of the pager configuration.
       *
       *  @return array The pager url params.
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 13.04.2007<br />
       */
      public function getURLParameters() {
         return $this->getPager()->getPagerURLParameters();
      }

      /**
       *  @public
       *
       *  Returns the HTML representation of the pager.
       *
       *  @return string The pager representation
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 13.04.2007<br />
       */
      public function getPagerPresentation() {
         return $this->getPager()->getPager(array('GuestbookID' => $this->guestbookId));
      }

      /**
       *  @public
       *
       *  Saves an entry object
       *
       *  @param Entry $entry The entry object
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 14.04.2007<br />
       */
      public function saveEntry(Entry $entry) {

         $gM = &$this->getMapper();

         $guestbook = $gM->loadGuestbookByID($this->guestbookId);
         $guestbook->addEntry($entry);
         $gM->saveGuestbook($guestbook);

         // forward to the target page
         $urlParams = $this->getURLParameters();
         $link = LinkHandler::generateLink($_SERVER['REQUEST_URI'], array($urlParams['PageName'] => '', $urlParams['CountName'] => '', 'gbview' => 'display'));
         HeaderManager::forward($link);
      }

      /**
       *  @public
       *
       *  Saves a comment object.
       *
       *  @param string $entryID The id of an entry
       *  @param Comment $comment The comment object
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 05.05.2007<br />
       *  Version 0.2, 19.05.2007 (Added the redirect url generation)<br />
       */
      public function saveComment($entryID, Comment $comment) {

         $gM = &$this->getMapper();

         $Guestbook = $gM->loadGuestbookByID($this->guestbookId);
         $Entry = $gM->loadEntryByID($entryID);
         $Entry->addComment($comment);
         $Guestbook->addEntry($Entry);
         $gM->saveGuestbook($Guestbook);

         // forward to the target page
         $urlParams = $this->getURLParameters();
         $link = LinkHandler::generateLink($_SERVER['REQUEST_URI'], array($urlParams['PageName'] => '', $urlParams['CountName'] => '', 'gbview' => 'display', 'commentid' => '', 'entryid' => ''));
         HeaderManager::forward($link);
      }

      /**
       * @public
       *
       * Loads an entry by id.
       *
       * @param string $entryID Id of the desired entry.
       * @return Entry The desired entry.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 05.05.2007<br />
       */
      public function loadEntry($entryID) {
         return $this->getMapper()->loadEntryByID($entryID);
      }

      /**
       * @public
       *
       * Loads a comment by id.
       *
       * @param string $commentID The comment id,
       * @return Comment The desired comment.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 19.05.2007<br />
       */
      public function loadComment($commentID) {
         return $this->getMapper()->loadCommentByID($commentID);
      }

      /**
       *  @public
       *
       *  Validates the login credentials.
       *
       *  @param string $username Username
       *  @param string $password Password
       *  @return bool true | false
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 05.05.2007<br />
       */
      public function validateCrendentials($username, $password) {

         $guestbook = $this->loadGuestbook();

         if ($guestbook->getAdminUsername() == $username && $guestbook->getAdminPassword() == $password) {
            return true;
         }
         return false;
      }

      /**
       *  @public
       *
       *  Deletes an guestbook entry.
       *
       *  @param Entry $entry The guestbook entry
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 05.05.2007<br />
       */
      public function deleteEntry(Entry $entry) {
         $this->getMapper()->deleteEntry($entry);
      }

      /**
       *  @public
       *
       *  Deletes a comment.
       *
       *  @param Comment $comment The comment object
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 19.05.2007<br />
       */
      public function deleteComment(Comment $comment) {
         $this->getMapper()->deleteComment($comment);
      }

      /**
       * @return GuestbookMapper The mapper instance of the guestbook implementation.
       */
      private function &getMapper(){
         return $this->getServiceObject('modules::guestbook::data', 'GuestbookMapper');
      }

   }
?>