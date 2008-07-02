<?php
   import('modules::guestbook::biz','guestbookManager');


   /**
   *  @package modules::guestbook::pres::documentcontroller
   *  @class guestbook_v1_controller
   *
   *  Implementiert den DocumentController f�r das Stylesheet 'guestbook.html'.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class guestbook_v1_controller extends baseController
   {

      /**
      *  @private
      *  H�lt lokal verwendete Variablen.
      */
      var $_LOCALS;


      function guestbook_v1_controller(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "transformContent()" aus "coreObject".<br />
      *  Gibt den Header des G�stebuchs aus.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      *  Version 0.2, 21.04.2007 (G�stebuch wird nicht komplett gezogen, sondern nur der Header)<br />
      */
      function transformContent(){

         // guestbookid holen
         $GuestbookID = $this->__Attributes['guestbookid'];

         // G�stebuch laden
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