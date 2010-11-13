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

   import('modules::guestbook::biz','GuestbookManager');
   import('tools::link','FrontcontrollerLinkHandler');
   import('core::session','SessionManager');
   import('modules::guestbook::pres::documentcontroller','guestbookBaseController');
   import('tools::string','stringAssistant');

   /**
    *  @package modules::guestbook::pres::documentcontroller
    *  @class guestbook_display_v1_controller
    *
    *  Implementiert den DocumentController f�r das Stylesheet 'display.html'.<br />
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 12.04.2007<br />
    *  Version 0.2, 07.01.2008 (�nderungen zur Mehrsprachigkeit, Spamschutz f�r E-Mails)<br />
    */
   class guestbook_display_v1_controller extends guestbookBaseController {

      /**
       * @var SessionManager The instance of the session manager.
       */
      private $session;

      /**
       *  @public
       *
       *  Implementiert die abstrakte Methode "transformContent()".<br />
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 13.04.2007<br />
       *  Version 0.2, 05.05.2007 (Admin-Link hinzugef�gt)<br />
       */
      public function transformContent() {
         $this->session = new SessionManager($this->getGuestbookNamespace());
         $gM = &$this->getGuestbookManager();
         $this->setPlaceHolder('Content', $this->generateEntryList());
         $this->setPlaceHolder('Pager', $gM->getPagerPresentation());
         $this->setPlaceHolder('CreateEntry', $this->generateCreateEntryLink());
         $this->setPlaceHolder('ControlGuestbook', $this->generateControlGuestbookLink());
      }

      /**
       *  @private
       *
       *  Erzeugt die Ausgabe des "Eintrag verfassen"-Links.<br />
       *
       *  @return string $CreateEntry; HTML-Ausgabe des "Eintrag verfassen"-Links
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 13.04.2007<br />
       *  Version 0.2, 07.01.2008 (Mehrsprachigkeit eingef�hrt)<br />
       */
      private function generateCreateEntryLink() {

         $Template__CreateEntry = &$this->__getTemplate('CreateEntry_' . $this->__Language);

         $Link = FrontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'createentry', 'entryid' => ''));
         $Template__CreateEntry->setPlaceHolder('Link', $Link);

         return $Template__CreateEntry->transformTemplate();

      }

      /**
       *  @private
       *
       *  Erzeugt die Ausgabe des "G�stebuch administrieren"-Links, bzw. im eingeloggten Zustand den<br />
       *  Link zum verlassen des Admin-Modus.<br />
       *
       *  @return string $ControlGuestbook; HTML-Ausgabe des "G�stebuch administrieren"-Links
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 05.05.2007<br />
       *  Version 0.2, 07.01.2008 (Mehrsprachigkeit eingef�hrt)<br />
       */
      private function generateControlGuestbookLink() {

         if ($this->session->loadSessionData('AdminView') == 'true') {

            $Template__ControlGuestbook_Logout = &$this->__getTemplate('ControlGuestbook_Logout_' . $this->__Language);

            $Link = FrontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'adminlogin', 'logout' => 'true', 'entryid' => ''));
            $Template__ControlGuestbook_Logout->setPlaceHolder('Link', $Link);

            return $Template__ControlGuestbook_Logout->transformTemplate();

         } else {

            $Template__ControlGuestbook_Login = &$this->__getTemplate('ControlGuestbook_Login_' . $this->__Language);

            $Link = FrontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'adminlogin', 'entryid' => ''));
            $Template__ControlGuestbook_Login->setPlaceHolder('Link', $Link);

            return $Template__ControlGuestbook_Login->transformTemplate();

         }

      }

      /**
       *  @private
       *
       *  Erzeugt die Ausgabe der Eintrags-Liste.<br />
       *
       *  @return string $EntryList; HTML-Ausgabe der Eintrags-Liste
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 13.04.2007<br />
       */
      private function generateEntryList() {

         $gM = &$this->getGuestbookManager();

         $Guestbook = $gM->loadGuestbook();
         $Entries = $Guestbook->getEntries();

         $Buffer = (string) '';

         for ($i = 0; $i < count($Entries); $i++) {
            $Buffer .= $this->generateEntry($Entries[$i]);
         }

         return $Buffer;

      }

      private function generateEntry(Entry $entry) {

         $Template__Entry = &$this->__getTemplate('Entry');

         $Template__Entry->setPlaceHolder('AdminDelete', $this->showAdminDelete($entry->getId()));
         $Template__Entry->setPlaceHolder('AdminEdit', $this->showAdminEdit($entry->getId()));
         $Template__Entry->setPlaceHolder('AdminAddComment', $this->showAdminAddComment($entry->getId()));
         $Template__Entry->setPlaceHolder('ID', $entry->getId());
         $Template__Entry->setPlaceHolder('Name', $entry->getName());
         $Template__Entry->setPlaceHolder('EMail', stringAssistant::encodeCharactersToHTML($entry->getEmail()));
         $Template__Entry->setPlaceHolder('City', $entry->getCity());
         $Template__Entry->setPlaceHolder('Website', $entry->getWebsite());
         $Template__Entry->setPlaceHolder('ICQ', $entry->getIcq());
         $Template__Entry->setPlaceHolder('MSN', $entry->getMsn());
         $Template__Entry->setPlaceHolder('Skype', $entry->getSkype());
         $Template__Entry->setPlaceHolder('AIM', $entry->getAim());
         $Template__Entry->setPlaceHolder('Yahoo', $entry->getYahoo());
         $Template__Entry->setPlaceHolder('Text', nl2br($entry->getText()));
         $Template__Entry->setPlaceHolder('Date', $entry->getDate());
         $Template__Entry->setPlaceHolder('Time', $entry->getTime());

         $Comments = $entry->getComments();
         $CommentBuffer = (string) '';

         if (count($Comments) > 0) {

            for ($i = 0; $i < count($Comments); $i++) {
               $CommentBuffer .= $this->generateComment($Comments[$i], $entry->getId());
            }

         }

         $Template__Entry->setPlaceHolder('Comments', $CommentBuffer);

         return $Template__Entry->transformTemplate();

      }

      private function generateComment(Comment $comment, $entryId) {

         $Template__Comment = &$this->__getTemplate('Comment');
         $Template__Comment->setPlaceHolder('AdminDeleteComment', $this->showAdminDeleteComment($comment->getId(), $entryId));
         $Template__Comment->setPlaceHolder('AdminEditComment', $this->showAdminEditComment($comment->getId(), $entryId));
         $Template__Comment->setPlaceHolder('Title', $comment->getTitle());
         $Template__Comment->setPlaceHolder('Text', $comment->getText());
         $Template__Comment->setPlaceHolder('Date', $comment->getDate());
         $Template__Comment->setPlaceHolder('Time', $comment->getTime());
         return $Template__Comment->transformTemplate();

      }

      private function showAdminDelete($entryId) {

         if ($this->session->loadSessionData('AdminView') == true) {

            $Template__AdminDelete = &$this->__getTemplate('AdminDelete');

            $Link = LinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'admindelete', 'entryid' => $entryId));
            $Template__AdminDelete->setPlaceHolder('Link', $Link);

            return $Template__AdminDelete->transformTemplate();

         }

         return (string) '';

      }

      private function showAdminEdit($entryId) {

         if ($this->session->loadSessionData('AdminView') == true) {

            $Template__AdminEdit = &$this->__getTemplate('AdminEdit');

            $Link = LinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'adminedit', 'entryid' => $entryId));
            $Template__AdminEdit->setPlaceHolder('Link', $Link);

            return $Template__AdminEdit->transformTemplate();

         }

         return (string) '';

      }

      private function showAdminAddComment($entryId) {

         if ($this->session->loadSessionData('AdminView') == true) {

            $Template__AdminAddComment = &$this->__getTemplate('AdminAddComment');

            $Link = LinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'adminaddcomment', 'entryid' => $entryId));
            $Template__AdminAddComment->setPlaceHolder('Link', $Link);

            return $Template__AdminAddComment->transformTemplate();

         }

         return (string) '';

      }

      private function showAdminDeleteComment($dommentId, $entryId) {

         if ($this->session->loadSessionData('AdminView') == true) {

            $Template__AdminDeleteComment = &$this->__getTemplate('AdminDeleteComment');

            $Link = LinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'admindeletecomment', 'commentid' => $dommentId));
            $Template__AdminDeleteComment->setPlaceHolder('Link', $Link);

            return $Template__AdminDeleteComment->transformTemplate();

         }

         return (string) '';

      }

      private function showAdminEditComment($commentId, $entryId) {

         if ($this->session->loadSessionData('AdminView') == true) {

            $Template__AdminEditComment = &$this->__getTemplate('AdminEditComment');

            $Link = LinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'admineditcomment', 'commentid' => $commentId, 'entryid' => $entryId));
            $Template__AdminEditComment->setPlaceHolder('Link', $Link);

            return $Template__AdminEditComment->transformTemplate();

         }

         return (string) '';

      }

   }
?>