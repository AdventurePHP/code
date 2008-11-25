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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::guestbook::biz','guestbookManager');


   /**
   *  @namespace modules::guestbook::pres::documentcontroller
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