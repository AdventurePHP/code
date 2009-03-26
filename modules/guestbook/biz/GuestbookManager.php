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
   *  Business-Komponente des G�stebuchs.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class GuestbookManager extends coreObject
   {


      /**
      *  @private
      *  G�stebuch-ID.
      */
      var $__GuestbookID;


      /**
      *  @private
      *  Container f�r ein Guestbook-Objekt.
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
      *  @param string $GuestbookID; G�stebuch-ID
      *
      *  @author Christian Sch�fer
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
      *  L�d ein G�stebuch per ID.<br />
      *
      *  @return object $Guestbook; G�stebuch-Objekt-Baum
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      */
      function loadGuestbook(){

         // Falls G�stebuch noch nicht geladen wurde...
         if($this->__Guestbook == null){

            // pagerManager holen
            $pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
            $pM = &$pMF->getPagerManager('Guestbook');

            // GuestbookMapper holen
            $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

            // G�stebuch laden
            $this->__Guestbook = $gM->loadGuestbookByID($this->__GuestbookID);

            // Eintrag-IDs, die angezeigt werden sollen laden
            $EntryIDs = $pM->loadEntries(array('GuestbookID' => $this->__GuestbookID));

            // Eintr�ge laden
            $Entries = array();

            for($i = 0; $i < count($EntryIDs); $i++){
               $Entries[] = $gM->loadEntryWithComments($EntryIDs[$i]);
             // end for
            }

            // Eintr�ge zum G�stebuch hinzuf�gen
            $this->__Guestbook->setEntries($Entries);

          // end if
         }

         // G�stebuch zur�ckgeben
         return $this->__Guestbook;

       // end function
      }


      /**
      *  @public
      *
      *  L�d ein G�stebuch-Objekt per ID.<br />
      *
      *  @return object $Guestbook; G�stebuch-Objekt ohne Eintr�ge
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 21.04.2007<br />
      */
      function loadGuestbookObject(){

         // GuestbookMapper holen
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         // G�stebuch-Objekt laden
         return $gM->loadGuestbookByID($this->__GuestbookID);

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die URL-Parameter des Pagers zur�ck.<br />
      *
      *  @param string $GuestbookID; G�stebuch-ID
      *  @return array $URLParameter; Pager-URL-Parameter
      *
      *  @author Christian Sch�fer
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
      *  Gibt die HTML-Ausgabe des Pagers zur�ck.<br />
      *
      *  @author Christian Sch�fer
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
      *  Speichert einen Eintrag in das aktuelle G�stebuch.<br />
      *
      *  @param Entry $Entry; Entry-Objekt
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 14.04.2007<br />
      */
      function saveEntry($Entry){

         // GuestbookMapper holen
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         // G�stebuch laden
         $Guestbook = $gM->loadGuestbookByID($this->__GuestbookID);

         // Eintrag hinzuf�gen
         $Guestbook->addEntry($Entry);

         // G�stebuch speichern
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
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      *  Version 0.2, 19.05.2007 (Generierung der Weiterleitungs-URL erweitert)<br />
      */
      function saveComment($EntryID,$Comment){

         // GuestbookMapper holen
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         // G�stebuch laden
         $Guestbook = $gM->loadGuestbookByID($this->__GuestbookID);

         // Entry laden
         $Entry = $gM->loadEntryByID($EntryID);

         // Comment zum Entry hinzuf�gen
         $Entry->addComment($Comment);

         // Eintrag hinzuf�gen
         $Guestbook->addEntry($Entry);

         // G�stebuch speichern
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
      *  L�d einen Eintrag per ID.<br />
      *
      *  @param string $EntryID; ID des Eintrags
      *
      *  @author Christian Sch�fer
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
      *  L�d einen Kommentar per ID.<br />
      *
      *  @param string $CommentID; ID des Kommentars
      *
      *  @author Christian Sch�fer
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
      *  Validiert Zugangsdaten f�r ein G�stebuch.<br />
      *
      *  @param string $Username; Benutzername
      *  @param string $Password; Passwort
      *  @return bool $CredOK; true | false
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      function validateCrendentials($Username,$Password){

         // G�stebuch-Objekt laden
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
      *  L�scht einen Eintrag eines G�stebuchs.<br />
      *
      *  @param Entry $Entry; Eintrags-Objekt
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      function deleteEntry($Entry){

         // GuestbookMapper holen
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         // Eintrag l�schen
         $gM->deleteEntry($Entry);

       // end function
      }


      /**
      *  @public
      *
      *  L�scht einen Kommentar eines Eintrags.<br />
      *
      *  @param Comment $Comment; Kommentar-Objekt
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 19.05.2007<br />
      */
      function deleteComment($Comment){

         // GuestbookMapper holen
         $gM = &$this->__getServiceObject('modules::guestbook::data','GuestbookMapper');

         // Eintrag l�schen
         $gM->deleteComment($Comment);

       // end function
      }

    // end class
   }
?>
