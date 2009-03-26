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
   import('core::session','sessionManager');
   import('modules::guestbook::pres::documentcontroller','guestbookBaseController');


   /**
   *  @namespace modules::guestbook::pres::documentcontroller
   *  @class guestbook_admindeletecomment_v1_controller
   *
   *  Implementiert den DocumentController für das Stylesheet 'admindeletecomment.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 19.05.2007<br />
   */
   class guestbook_admindeletecomment_v1_controller extends guestbookBaseController
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen.
      */
      var $_LOCALS;


      function guestbook_admindeletecomment_v1_controller(){
         $this->_LOCALS = RequestHandler::getValues(array('commentid'));
       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode aus coreObject.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 19.05.2007<br />
      */
      function transformContent(){

         // Referenz auf die Formulare holen
         $Form__FormNo = &$this->__getForm('FormNo');
         $Form__FormYes = &$this->__getForm('FormYes');

         // SessionManager erzeugen
         $oSessMgr = new sessionManager('Module_Guestbook');

         if($oSessMgr->loadSessionData('AdminView') == true){

            // Aktion ausführen für abgesendetes NEIN-Formular
            if($Form__FormNo->get('isSent')){
               $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'display','commentid' => ''));
               header('Location: '.$Link);
             // end if
            }

            // Aktion ausführen für abgesendetes JA-Formular
            if($Form__FormYes->get('isSent')){

               // Manager holen
               $gM = &$this->__getGuestbookManager();

               // Entry erzeugen
               $Comment= new Entry();
               $Comment->set('ID',$this->_LOCALS['commentid']);

               // Eintrag löschen
               $gM->deleteComment($Comment);

               // Auf Anzeige-Seite weiterleiten
               $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'display','commentid' => ''));
               header('Location: '.$Link);

             // end if
            }

            // Formular anzeigen
            $this->setPlaceHolder('FormNo',$Form__FormNo->transformForm());
            $this->setPlaceHolder('FormYes',$Form__FormYes->transformForm());

          // end if
         }
         else{
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'display','commentid' => ''));
            header('Location: '.$Link);
          // end else
         }

       // end function
      }

    // end class
   }
?>