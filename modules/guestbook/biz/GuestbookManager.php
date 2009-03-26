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
   import('modules::pager::biz','pagerManager');
   import('core::session','sessionManager');


   /**
   *  @namespace modules::guestbook::biz
   *  @class GuestbookManager
   *
   *  Business-Komponente des Gästebuchs.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class GuestbookManager extends coreObject
   {


      /**
      *  @private
      *  Gästebuch-ID.
      */
      var $__GuestbookID;


      /**
      *  @private
      *  Container für ein Guestbook-Objekt.
      */
      var $__Guestbook = null;


      /**
      *  @private
      *  Instanz des Session-Managers.
      */
      var $__sessMgr = null;


      function GuestbookManager(){
      }


      /**
      *  @public
      *
      *  Implementierung der abstrakte "init()"-Methode.<br />
      *
      *  @param string $GuestbookID; Gästebuch-ID
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      */
      function init($guestbookID){

         // GuestbookID speichern
         $this->__GuestbookID = $guestbookID;

         // SessionManager initialisieren
         $this->__sessMgr = new sessionManager('Guestbook');

       // end function
      }


      /**
      *  @public
      *
      *  Läd ein Gästebuch per ID.<br />
      *
      *  @return object $Guestbook; Gästebuch-Objekt-Baum
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      */
      function loadGuestbook(){

         // Falls Gästebuch noch nicht geladen wurde...
         if($this->__Guestbook == null){

            // pagerManager holen
            $pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
            $pM = &$pMF->getPagerManager('Guestbook');

            // GuestbookMapper holen
            $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

            // Gästebuch laden
            $this->__Guestbook = $gM->loadGuestbookByID($this->__GuestbookID);

            // Eintrag-IDs, die angezeigt werden sollen laden
            $EntryIDs = $pM->loadEntries(array('GuestbookID' => $this->__GuestbookID));

            // Einträge laden
            $Entries = array();

            for($i = 0; $i < count($EntryIDs); $i++){
               $Entries[] = $gM->loadEntryWithComments($EntryIDs[$i]);
             // end for
            }

            // Einträge zum Gästebuch hinzufügen
            $this->__Guestbook->setEntries($Entries);

          // end if
         }

         // Gästebuch zurückgeben
         return $this->__Guestbook;

       // end function
      }


      /**
      *  @public
      *
      *  Läd ein Gästebuch-Objekt per ID.<br />
      *
      *  @return object $Guestbook; Gästebuch-Objekt ohne Einträge
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.04.2007<br />
      */
      function loadGuestbookObject(){

         // GuestbookMapper holen
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         // Gästebuch-Objekt laden
         return $gM->loadGuestbookByID($this->__GuestbookID);

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die URL-Parameter des Pagers zurück.<br />
      *
      *  @param string $GuestbookID; Gästebuch-ID
      *  @return array $URLParameter; Pager-URL-Parameter
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      */
      function getURLParameters(){
         $pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
         $pM = &$pMF->getPagerManager('Guestbook');
         return $pM->getPagerURLParameters();
       // end function
      }


      /**
      *  @public
      *
      *  Gibt die HTML-Ausgabe des Pagers zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      */
      function getPager(){
         $pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
         $pM = &$pMF->getPagerManager('Guestbook');
         return $pM->getPager(array('GuestbookID' => $this->__GuestbookID));
       // end function
      }


      /**
      *  @public
      *
      *  Speichert einen Eintrag in das aktuelle Gästebuch.<br />
      *
      *  @param Entry $Entry; Entry-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 14.04.2007<br />
      */
      function saveEntry($Entry){

         // GuestbookMapper holen
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         // Gästebuch laden
         $Guestbook = $gM->loadGuestbookByID($this->__GuestbookID);

         // Eintrag hinzufügen
         $Guestbook->addEntry($Entry);

         // Gästebuch speichern
         $gM->saveGuestbook($Guestbook);

         // Auf Anzeige-Seite weiterleiten
         $URLParams = $this->getURLParameters();
         $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array($URLParams['StartName'] => '', $URLParams['CountName'] => '', 'gbview' => 'display'));
         header('Location: '.$Link);

       // end function
      }


      /**
      *  @public
      *
      *  Speichert einen Kommentar zu einem Eintrag.<br />
      *
      *  @param string $EntryID; ID des Eintrags
      *  @param Comment $Comment; Kommentar-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      *  Version 0.2, 19.05.2007 (Generierung der Weiterleitungs-URL erweitert)<br />
      */
      function saveComment($EntryID,$Comment){

         // GuestbookMapper holen
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         // Gästebuch laden
         $Guestbook = $gM->loadGuestbookByID($this->__GuestbookID);

         // Entry laden
         $Entry = $gM->loadEntryByID($EntryID);

         // Comment zum Entry hinzufügen
         $Entry->addComment($Comment);

         // Eintrag hinzufügen
         $Guestbook->addEntry($Entry);

         // Gästebuch speichern
         $gM->saveGuestbook($Guestbook);

         // Auf Anzeige-Seite weiterleiten
         $URLParams = $this->getURLParameters();
         $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array($URLParams['StartName'] => '', $URLParams['CountName'] => '', 'gbview' => 'display','commentid' => '', 'entryid' => ''));
         header('Location: '.$Link);

       // end function
      }


      /**
      *  @public
      *
      *  Läd einen Eintrag per ID.<br />
      *
      *  @param string $EntryID; ID des Eintrags
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      function loadEntry($EntryID){

         // GuestbookMapper holen
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         // Eintrag ausgeben
         return $gM->loadEntryByID($EntryID);

       // end function
      }


      /**
      *  @public
      *
      *  Läd einen Kommentar per ID.<br />
      *
      *  @param string $CommentID; ID des Kommentars
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 19.05.2007<br />
      */
      function loadComment($CommentID){

         // GuestbookMapper holen
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         // Eintrag ausgeben
         return $gM->loadCommentByID($CommentID);

       // end function
      }


      /**
      *  @public
      *
      *  Validiert Zugangsdaten für ein Gästebuch.<br />
      *
      *  @param string $Username; Benutzername
      *  @param string $Password; Passwort
      *  @return bool $CredOK; true | false
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      function validateCrendentials($Username,$Password){

         // Gästebuch-Objekt laden
         $Guestbook = $this->loadGuestbookObject();

         // Zugangsdaten validieren
         if($Guestbook->get('Admin_Username') == $Username && $Guestbook->get('Admin_Password') == $Password){
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
      *  Löscht einen Eintrag eines Gästebuchs.<br />
      *
      *  @param Entry $Entry; Eintrags-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      function deleteEntry($Entry){

         // GuestbookMapper holen
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         // Eintrag löschen
         $gM->deleteEntry($Entry);

       // end function
      }


      /**
      *  @public
      *
      *  Löscht einen Kommentar eines Eintrags.<br />
      *
      *  @param Comment $Comment; Kommentar-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 19.05.2007<br />
      */
      function deleteComment($Comment){

         // GuestbookMapper holen
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         // Eintrag löschen
         $gM->deleteComment($Comment);

       // end function
      }

    // end class
   }
?>
