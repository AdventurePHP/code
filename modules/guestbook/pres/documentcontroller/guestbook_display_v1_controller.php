<?php
   import('modules::guestbook::biz','guestbookManager');
   import('tools::link','frontcontrollerLinkHandler');
   import('core::session','sessionManager');
   import('modules::guestbook::pres::documentcontroller','guestbookBaseController');
   import('tools::string','stringAssistant');


   /**
   *  @package modules::guestbook::pres::documentcontroller
   *  @class guestbook_display_v1_controller
   *
   *  Implementiert den DocumentController f�r das Stylesheet 'display.html'.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   *  Version 0.2, 07.01.2008 (�nderungen zur Mehrsprachigkeit, Spamschutz f�r E-Mails)<br />
   */
   class guestbook_display_v1_controller extends guestbookBaseController
   {

      /**
      *  @private
      *  H�lt lokal verwendete Variablen.
      */
      var $_LOCALS;


      /**
      *  @private
      *  H�lt eine Instanz des Session-Managers.
      */
      var $__sessMgr;


      /**
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      function guestbook_display_v1_controller(){
         $this->__sessMgr = new sessionManager('Module_Guestbook');
       // end function
      }


      /**
      *  @private
      *
      *  Implementiert die abstrakte Methode "transformContent()".<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      *  Version 0.2, 05.05.2007 (Admin-Link hinzugef�gt)<br />
      */
      function transformContent(){

         // Manager holen
         $gM = &$this->__getGuestbookManager();

         // Eintr�ge ausgeben
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
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      *  Version 0.2, 07.01.2008 (Mehrsprachigkeit eingef�hrt)<br />
      */
      function __generateCreateEntryLink(){

         // Referenz auf das Template holen
         $Template__CreateEntry = &$this->__getTemplate('CreateEntry_'.$this->__Language);

         // Link generieren und einsetzen
         $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'createentry','entryid' => ''));
         $Template__CreateEntry->setPlaceHolder('Link',$Link);

         // Ausgabe zur�ckgeben
         return $Template__CreateEntry->transformTemplate();

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt die Ausgabe des "G�stebuch administrieren"-Links, bzw. im eingeloggten Zustand den<br />
      *  Link zum verlassen des Admin-Modus.<br />
      *
      *  @return string $ControlGuestbook; HTML-Ausgabe des "G�stebuch administrieren"-Links
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      *  Version 0.2, 07.01.2008 (Mehrsprachigkeit eingef�hrt)<br />
      */
      function __generateControlGuestbookLink(){

         // Inhalte der Session erst�ren
         $oSessMgr = new sessionManager('Module_Guestbook');

         if($oSessMgr->loadSessionData('AdminView') == 'true'){

            // Referenz auf das Template holen
            $Template__ControlGuestbook_Logout = &$this->__getTemplate('ControlGuestbook_Logout_'.$this->__Language);

            // Link generieren und einsetzen
            $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'adminlogin','logout' => 'true','entryid' => ''));
            $Template__ControlGuestbook_Logout->setPlaceHolder('Link',$Link);

            // Ausgabe zur�ckgeben
            return $Template__ControlGuestbook_Logout->transformTemplate();

          // end if
         }
         else{

            // Referenz auf das Template holen
            $Template__ControlGuestbook_Login = &$this->__getTemplate('ControlGuestbook_Login_'.$this->__Language);

            // Link generieren und einsetzen
            $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'adminlogin','entryid' => ''));
            $Template__ControlGuestbook_Login->setPlaceHolder('Link',$Link);

            // Ausgabe zur�ckgeben
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
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      */
      function __generateEntryList(){

         // Manager holen
         $gM = &$this->__getGuestbookManager();

         // Eintr�ge holen
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
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      *  Version 0.2, 07.01.2008 (E-Mail wird nun codiert dargestellt,Text mehrzeilig)<br />
      */
      function __generateEntry($Entry){

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

         // Comments einf�gen
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

         // Ausgabe zur�ckgeben
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
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 14.04.2007<br />
      */
      function __generateComment($Comment,$EntryID){

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
      *  Erzeugt einen Link f�r das L�schen eines Beitrags.<br />
      *
      *  @param string $EntryID; ID des Eintrags
      *  @return string $CommentOut; HTML-Ausgabe des Kommenrats
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 14.04.2007<br />
      */
      function __showAdminDelete($EntryID){

         if($this->__sessMgr->loadSessionData('AdminView') == true){

            // Template holen
            $Template__AdminDelete = &$this->__getTemplate('AdminDelete');

            // Link generieren und einsetzen
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'admindelete','entryid' => $EntryID));
            $Template__AdminDelete->setPlaceHolder('Link',$Link);

            // Template zur�ckgeben
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
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 14.04.2007<br />
      */
      function __showAdminEdit($EntryID){

         if($this->__sessMgr->loadSessionData('AdminView') == true){

            // Template holen
            $Template__AdminEdit = &$this->__getTemplate('AdminEdit');

            // Link generieren und einsetzen
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'adminedit','entryid' => $EntryID));
            $Template__AdminEdit->setPlaceHolder('Link',$Link);

            // Template zur�ckgeben
            return $Template__AdminEdit->transformTemplate();

          // end if
         }

         return (string)'';

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt den Link zum Hinzuf�gen eines Kommentars.<br />
      *
      *  @param string $EntryID; ID des aktuell angezeigten Eintrags
      *  @return string $CommentOut; HTML-Ausgabe des Kommenrats
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 14.04.2007<br />
      */
      function __showAdminAddComment($EntryID){

         if($this->__sessMgr->loadSessionData('AdminView') == true){

            // Template holen
            $Template__AdminAddComment = &$this->__getTemplate('AdminAddComment');

            // Link generieren und einsetzen
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'adminaddcomment','entryid' => $EntryID));
            $Template__AdminAddComment->setPlaceHolder('Link',$Link);

            // Template zur�ckgeben
            return $Template__AdminAddComment->transformTemplate();

          // end if
         }

         return (string)'';

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt den Link zum L�schen eines Kommentars.<br />
      *
      *  @param string $CommentID; ID des Kommentars
      *  @param string $EntryID; ID des Eintrags
      *  @return string $AdminDeleteComment; HTML-Ausgabe f�r den Link zum L�schen eines Kommentars
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 19.04.2007<br />
      */
      function __showAdminDeleteComment($CommentID,$EntryID){

         if($this->__sessMgr->loadSessionData('AdminView') == true){

            // Template holen
            $Template__AdminDeleteComment = &$this->__getTemplate('AdminDeleteComment');

            // Link generieren und einsetzen
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'admindeletecomment','commentid' => $CommentID));
            $Template__AdminDeleteComment->setPlaceHolder('Link',$Link);

            // Template zur�ckgeben
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
      *  @return string $AdminDeleteComment; HTML-Ausgabe f�r den Link zum Editieren eines Kommentars
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 19.04.2007<br />
      */
      function __showAdminEditComment($CommentID,$EntryID){

         if($this->__sessMgr->loadSessionData('AdminView') == true){

            // Template holen
            $Template__AdminEditComment = &$this->__getTemplate('AdminEditComment');

            // Link generieren und einsetzen
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'admineditcomment','commentid' => $CommentID, 'entryid' => $EntryID));
            $Template__AdminEditComment->setPlaceHolder('Link',$Link);

            // Template zur�ckgeben
            return $Template__AdminEditComment->transformTemplate();

          // end if
         }

         return (string)'';

       // end function
      }

    // end class
   }
?>