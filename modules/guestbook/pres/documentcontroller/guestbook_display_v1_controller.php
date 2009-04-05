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

   import('modules::guestbook::biz','GuestbookManager');
   import('tools::link','frontcontrollerLinkHandler');
   import('core::session','sessionManager');
   import('modules::guestbook::pres::documentcontroller','guestbookBaseController');
   import('tools::string','stringAssistant');


   /**
   *  @namespace modules::guestbook::pres::documentcontroller
   *  @class guestbook_display_v1_controller
   *
   *  Implementiert den DocumentController für das Stylesheet 'display.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   *  Version 0.2, 07.01.2008 (Änderungen zur Mehrsprachigkeit, Spamschutz für E-Mails)<br />
   */
   class guestbook_display_v1_controller extends guestbookBaseController
   {

      /**
      *  @protected
      *  Hält lokal verwendete Variablen.
      */
      protected $_LOCALS;


      /**
      *  @protected
      *  Hält eine Instanz des Session-Managers.
      */
      protected $__sessMgr;


      /**
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      public function guestbook_display_v1_controller(){
         $this->__sessMgr = new sessionManager('Module_Guestbook');
       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "transformContent()".<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      *  Version 0.2, 05.05.2007 (Admin-Link hinzugefügt)<br />
      */
      public function transformContent(){

         // Manager holen
         $gM = &$this->__getGuestbookManager();

         // Einträge ausgeben
         $this->setPlaceHolder('Content',$this->__generateEntryList());

         // Pager ausgeben
         $this->setPlaceHolder('Pager',$gM->getPager());

         // Eintragen-Link ausgeben
         $this->setPlaceHolder('CreateEntry',$this->__generateCreateEntryLink());

         // Eintragen-Link ausgeben
         $this->setPlaceHolder('ControlGuestbook',$this->__generateControlGuestbookLink());

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt die Ausgabe des "Eintrag verfassen"-Links.<br />
      *
      *  @return string $CreateEntry; HTML-Ausgabe des "Eintrag verfassen"-Links
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      *  Version 0.2, 07.01.2008 (Mehrsprachigkeit eingeführt)<br />
      */
      private function __generateCreateEntryLink(){

         // Referenz auf das Template holen
         $Template__CreateEntry = &$this->__getTemplate('CreateEntry_'.$this->__Language);

         // Link generieren und einsetzen
         $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'createentry','entryid' => ''));
         $Template__CreateEntry->setPlaceHolder('Link',$Link);

         // Ausgabe zurückgeben
         return $Template__CreateEntry->transformTemplate();

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt die Ausgabe des "Gästebuch administrieren"-Links, bzw. im eingeloggten Zustand den<br />
      *  Link zum verlassen des Admin-Modus.<br />
      *
      *  @return string $ControlGuestbook; HTML-Ausgabe des "Gästebuch administrieren"-Links
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      *  Version 0.2, 07.01.2008 (Mehrsprachigkeit eingeführt)<br />
      */
      private function __generateControlGuestbookLink(){

         // Inhalte der Session erstören
         $oSessMgr = new sessionManager('Module_Guestbook');

         if($oSessMgr->loadSessionData('AdminView') == 'true'){

            // Referenz auf das Template holen
            $Template__ControlGuestbook_Logout = &$this->__getTemplate('ControlGuestbook_Logout_'.$this->__Language);

            // Link generieren und einsetzen
            $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'adminlogin','logout' => 'true','entryid' => ''));
            $Template__ControlGuestbook_Logout->setPlaceHolder('Link',$Link);

            // Ausgabe zurückgeben
            return $Template__ControlGuestbook_Logout->transformTemplate();

          // end if
         }
         else{

            // Referenz auf das Template holen
            $Template__ControlGuestbook_Login = &$this->__getTemplate('ControlGuestbook_Login_'.$this->__Language);

            // Link generieren und einsetzen
            $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'adminlogin','entryid' => ''));
            $Template__ControlGuestbook_Login->setPlaceHolder('Link',$Link);

            // Ausgabe zurückgeben
            return $Template__ControlGuestbook_Login->transformTemplate();

          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt die Ausgabe der Eintrags-Liste.<br />
      *
      *  @return string $EntryList; HTML-Ausgabe der Eintrags-Liste
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      */
      private function __generateEntryList(){

         // Manager holen
         $gM = &$this->__getGuestbookManager();

         // Einträge holen
         $Guestbook = $gM->loadGuestbook();
         $Entries = $Guestbook->getEntries();

         // Ausgabe generieren
         $Buffer = (string)'';

         for($i = 0; $i < count($Entries); $i++){
            $Buffer .= $this->__generateEntry($Entries[$i]);
          // end for
         }

         // Puffer ausgeben
         return $Buffer;

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt die Ausgabe eines Eintrags.<br />
      *
      *  @param Entry $Entry; Entry-Objekt
      *  @return string $EntryOut; HTML-Ausgabe des Eintrags
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      *  Version 0.2, 07.01.2008 (E-Mail wird nun codiert dargestellt,Text mehrzeilig)<br />
      */
      private function __generateEntry($Entry){

         // Referenz auf das Template holen
         $Template__Entry = &$this->__getTemplate('Entry');

         // Werte setzen
         $Template__Entry->setPlaceHolder('AdminDelete',$this->__showAdminDelete($Entry->get('ID')));
         $Template__Entry->setPlaceHolder('AdminEdit',$this->__showAdminEdit($Entry->get('ID')));
         $Template__Entry->setPlaceHolder('AdminAddComment',$this->__showAdminAddComment($Entry->get('ID')));
         $Template__Entry->setPlaceHolder('ID',$Entry->get('ID'));
         $Template__Entry->setPlaceHolder('Name',$Entry->get('Name'));
         $Template__Entry->setPlaceHolder('EMail',stringAssistant::encodeCharactersToHTML($Entry->get('EMail')));
         $Template__Entry->setPlaceHolder('City',$Entry->get('City'));
         $Template__Entry->setPlaceHolder('Website',$Entry->get('Website'));
         $Template__Entry->setPlaceHolder('ICQ',$Entry->get('ICQ'));
         $Template__Entry->setPlaceHolder('MSN',$Entry->get('MSN'));
         $Template__Entry->setPlaceHolder('Skype',$Entry->get('Skype'));
         $Template__Entry->setPlaceHolder('AIM',$Entry->get('AIM'));
         $Template__Entry->setPlaceHolder('Yahoo',$Entry->get('Yahoo'));
         $Template__Entry->setPlaceHolder('Text',nl2br($Entry->get('Text')));
         $Template__Entry->setPlaceHolder('Date',$Entry->get('Date'));
         $Template__Entry->setPlaceHolder('Time',$Entry->get('Time'));

         // Comments einfügen
         $Comments = $Entry->getComments();
         $CommentBuffer = (string)'';

         if(count($Comments) > 0){

            for($i = 0; $i < count($Comments); $i++){
               $CommentBuffer .= $this->__generateComment($Comments[$i],$Entry->get('ID'));
             // end if
            }

          // end if
         }

         $Template__Entry->setPlaceHolder('Comments',$CommentBuffer);

         // Ausgabe zurückgeben
         return $Template__Entry->transformTemplate();

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt die Ausgabe eines Kommentars.<br />
      *
      *  @param Comment $Comment; Comment-Objekt
      *  @return string $CommentOut; HTML-Ausgabe des Kommenrats
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 14.04.2007<br />
      */
      private function __generateComment($Comment,$EntryID){

         $Template__Comment = &$this->__getTemplate('Comment');
         $Template__Comment->setPlaceHolder('AdminDeleteComment',$this->__showAdminDeleteComment($Comment->get('ID'),$EntryID));
         $Template__Comment->setPlaceHolder('AdminEditComment',$this->__showAdminEditComment($Comment->get('ID'),$EntryID));
         $Template__Comment->setPlaceHolder('Title',$Comment->get('Title'));
         $Template__Comment->setPlaceHolder('Text',$Comment->get('Text'));
         $Template__Comment->setPlaceHolder('Date',$Comment->get('Date'));
         $Template__Comment->setPlaceHolder('Time',$Comment->get('Time'));
         return $Template__Comment->transformTemplate();

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt einen Link für das Löschen eines Beitrags.<br />
      *
      *  @param string $EntryID; ID des Eintrags
      *  @return string $CommentOut; HTML-Ausgabe des Kommenrats
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 14.04.2007<br />
      */
      private function __showAdminDelete($EntryID){

         if($this->__sessMgr->loadSessionData('AdminView') == true){

            // Template holen
            $Template__AdminDelete = &$this->__getTemplate('AdminDelete');

            // Link generieren und einsetzen
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'admindelete','entryid' => $EntryID));
            $Template__AdminDelete->setPlaceHolder('Link',$Link);

            // Template zurückgeben
            return $Template__AdminDelete->transformTemplate();

          // end if
         }

         return (string)'';

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt den Link zum Editieren eines Eintrags.<br />
      *
      *  @param Comment $Comment; Comment-Objekt
      *  @return string $CommentOut; HTML-Ausgabe des Kommenrats
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 14.04.2007<br />
      */
      private function __showAdminEdit($EntryID){

         if($this->__sessMgr->loadSessionData('AdminView') == true){

            // Template holen
            $Template__AdminEdit = &$this->__getTemplate('AdminEdit');

            // Link generieren und einsetzen
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'adminedit','entryid' => $EntryID));
            $Template__AdminEdit->setPlaceHolder('Link',$Link);

            // Template zurückgeben
            return $Template__AdminEdit->transformTemplate();

          // end if
         }

         return (string)'';

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt den Link zum Hinzufügen eines Kommentars.<br />
      *
      *  @param string $EntryID; ID des aktuell angezeigten Eintrags
      *  @return string $CommentOut; HTML-Ausgabe des Kommenrats
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 14.04.2007<br />
      */
      private function __showAdminAddComment($EntryID){

         if($this->__sessMgr->loadSessionData('AdminView') == true){

            // Template holen
            $Template__AdminAddComment = &$this->__getTemplate('AdminAddComment');

            // Link generieren und einsetzen
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'adminaddcomment','entryid' => $EntryID));
            $Template__AdminAddComment->setPlaceHolder('Link',$Link);

            // Template zurückgeben
            return $Template__AdminAddComment->transformTemplate();

          // end if
         }

         return (string)'';

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt den Link zum Löschen eines Kommentars.<br />
      *
      *  @param string $CommentID; ID des Kommentars
      *  @param string $EntryID; ID des Eintrags
      *  @return string $AdminDeleteComment; HTML-Ausgabe für den Link zum Löschen eines Kommentars
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 19.04.2007<br />
      */
      private function __showAdminDeleteComment($CommentID,$EntryID){

         if($this->__sessMgr->loadSessionData('AdminView') == true){

            // Template holen
            $Template__AdminDeleteComment = &$this->__getTemplate('AdminDeleteComment');

            // Link generieren und einsetzen
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'admindeletecomment','commentid' => $CommentID));
            $Template__AdminDeleteComment->setPlaceHolder('Link',$Link);

            // Template zurückgeben
            return $Template__AdminDeleteComment->transformTemplate();

          // end if
         }

         return (string)'';

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt den Link zum Editieren eines Kommentars.<br />
      *
      *  @param string $CommentID; ID des Kommentars
      *  @param string $EntryID; ID des Eintrags
      *  @return string $AdminDeleteComment; HTML-Ausgabe für den Link zum Editieren eines Kommentars
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 19.04.2007<br />
      */
      private function __showAdminEditComment($CommentID,$EntryID){

         if($this->__sessMgr->loadSessionData('AdminView') == true){

            // Template holen
            $Template__AdminEditComment = &$this->__getTemplate('AdminEditComment');

            // Link generieren und einsetzen
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'admineditcomment','commentid' => $CommentID, 'entryid' => $EntryID));
            $Template__AdminEditComment->setPlaceHolder('Link',$Link);

            // Template zurückgeben
            return $Template__AdminEditComment->transformTemplate();

          // end if
         }

         return (string)'';

       // end function
      }

    // end class
   }
?>