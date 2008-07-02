<?php
   import('modules::guestbook::biz','guestbookManager');
   import('core::session','sessionManager');
   import('modules::guestbook::pres::documentcontroller','guestbookBaseController');


   /**
   *  @package modules::guestbook::pres::documentcontroller
   *  @class guestbook_adminlogin_v1_controller
   *
   *  Implementiert den DocumentController f�r das Stylesheet 'adminlogin.html'.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 05.05.2007<br />
   */
   class guestbook_adminlogin_v1_controller extends guestbookBaseController
   {

      /**
      *  @private
      *  H�lt lokal verwendete Variablen.
      */
      var $_LOCALS;


      function guestbook_adminlogin_v1_controller(){

         $this->_LOCALS = variablenHandler::registerLocal(array(
                                                                'Username',
                                                                'Password',
                                                                'logout'
                                                               )
                                                         );

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode aus coreObject.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      function transformContent(){

         // Logout
         if($this->_LOCALS['logout'] == 'true'){

            // Inhalte der Session erst�ren
            $oSessMgr = new sessionManager('Module_Guestbook');
            $oSessMgr->destroySession('Module_Guestbook');

            // Auf Anzeige-Seite weiterleiten
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'display','logout' => ''));
            header('Location: '.$Link);

          // end if
         }


         // Referenz auf das Formular holen
         $Form__AdminLogin = &$this->__getForm('AdminLogin');

         if($Form__AdminLogin->get('isValid') && $Form__AdminLogin->get('isSent')){

            // Manager holen
            $gM = &$this->__getGuestbookManager();

            // Pr�fen, ob Zugangsdaten g�ltig sind
            if($gM->validateCrendentials($this->_LOCALS['Username'],$this->_LOCALS['Password']) == true){

               // Session starten
               $oSessMgr = new sessionManager('Module_Guestbook');
               $oSessMgr->saveSessionData('LoginDate',date('Y-m-d'));
               $oSessMgr->saveSessionData('LoginTime',date('H:i:s'));
               $oSessMgr->saveSessionData('AdminView',true);

               // Auf Anzeige-Seite weiterleiten
               $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'display'));
               header('Location: '.$Link);

             // end if
            }
            else{
               $this->setPlaceHolder('Form',$this->__displayForm(true));
             // end else
            }

          // end if
         }
         else{
            $this->setPlaceHolder('Form',$this->__displayForm());
          // end elseif
         }

       // end function
      }


      /**
      *  @private
      *
      *  Implementiert einen Wrapper f�r die Formular-Darstellung.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      function __displayForm($ShowLogInError = false){

         // Referenz auf das Formular holen
         $Form__AdminLogin = &$this->__getForm('AdminLogin');

         // Fehlermeldung ausgeben, falls gew�nscht
         if($ShowLogInError == true){

            // Template mit Meldung holen
            $Template__LogInError = &$this->__getTemplate('LogInError');

            // Template in Form einsetzen
            $Form__AdminLogin->setPlaceHolder('LogInError',$Template__LogInError->transformTemplate());

          // end if
         }

         // Formular transformieren und zur�ckgeben
         return $Form__AdminLogin->transformForm();

       // end function
      }

    // end class
   }
?>