<?php
   /**
   *  @package modules::guestbook::pres::documentcontroller
   *  @class guestbookBaseController
   *  @abstract
   *
   *  Abstrakter DocumentController, der Basis-Funktionen für weitere DocumentController des Moduls bereitstellt..<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class guestbookBaseController extends baseController
   {

      function guestbookBaseController(){
      }


      /**
      *  @private
      *
      *  Gibt die Instanz des Managers zurück.<br />
      *
      *  @return guestbookManager $gM; Instanz des Gästebuch-Managers
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      function &__getGuestbookManager(){

         // guestbookid vom Vater holen
         $Parent = &$this->__Document->getByReference('ParentObject');
         $GuestbookID = $Parent->getAttribute('guestbookid');

         // Manager holen
         return $this->__getAndInitServiceObject('modules::guestbook::biz','guestbookManager',$GuestbookID);

       // end function
      }

    // end class
   }
?>