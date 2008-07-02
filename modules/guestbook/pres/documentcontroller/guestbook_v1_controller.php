<?php
   import('modules::guestbook::biz','guestbookManager');


   /**
   *  @package modules::guestbook::pres::documentcontroller
   *  @class guestbook_v1_controller
   *
   *  Implementiert den DocumentController für das Stylesheet 'guestbook.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class guestbook_v1_controller extends baseController
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen.
      */
      var $_LOCALS;


      function guestbook_v1_controller(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "transformContent()" aus "coreObject".<br />
      *  Gibt den Header des Gästebuchs aus.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 21.04.2007 (Gästebuch wird nicht komplett gezogen, sondern nur der Header)<br />
      */
      function transformContent(){

         // guestbookid holen
         $GuestbookID = $this->__Attributes['guestbookid'];

         // Gästebuch laden
         $gM = &$this->__getAndInitServiceObject('modules::guestbook::biz','guestbookManager',$GuestbookID);
         $Guestbook = $gM->loadGuestbookObject();

         // Name und Beschreibung anzeigen
         $this->setPlaceHolder('Name',$Guestbook->get('Name'));
         $this->setPlaceHolder('Description',$Guestbook->get('Description'));

       // end function
      }

    // end class
   }
?>