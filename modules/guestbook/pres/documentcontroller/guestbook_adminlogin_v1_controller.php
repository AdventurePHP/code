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
   import('tools::link','linkHandler');
   import('tools::request','RequestHandler');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::guestbook::pres::documentcontroller
   *  @class guestbook_adminlogin_v1_controller
   *
   *  Implementiert den DocumentController für das Stylesheet 'adminlogin.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 05.05.2007<br />
   */
   class guestbook_adminlogin_v1_controller extends guestbookBaseController
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen.
      */
      private $_LOCALS;


      public function guestbook_adminlogin_v1_controller(){

         $this->_LOCALS = RequestHandler::getValues(array(
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
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      public function transformContent(){

         // Handle logout, in case the url contains true for the logout param. This is done, because
         // we don't want to use a front controller action here.
         if($this->_LOCALS['logout'] == 'true'){
            $guestbookNamespace = $this->__getGuestbookNamespace();
            $oSessMgr = new sessionManager($guestbookNamespace);
            $oSessMgr->destroySession($guestbookNamespace);
            $link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'display','logout' => ''));
            HeaderManager::redirect($link);
          // end if
         }

         $Form__AdminLogin = &$this->__getForm('AdminLogin');

         if($Form__AdminLogin->get('isValid') && $Form__AdminLogin->get('isSent')){

            $gM = &$this->__getGuestbookManager();

            if($gM->validateCrendentials($this->_LOCALS['Username'],$this->_LOCALS['Password']) == true){

               $oSessMgr = new sessionManager($this->__getGuestbookNamespace());
               $oSessMgr->saveSessionData('LoginDate',date('Y-m-d'));
               $oSessMgr->saveSessionData('LoginTime',date('H:i:s'));
               $oSessMgr->saveSessionData('AdminView',true);

               $link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('gbview' => 'display'));
               HeaderManager::forward($link);

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
      *  Implementiert einen Wrapper für die Formular-Darstellung.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      private function __displayForm($ShowLogInError = false){

         $form__AdminLogin = &$this->__getForm('AdminLogin');

         if($ShowLogInError == true){

            $template__LogInError = &$this->__getTemplate('LogInError');
            $form__AdminLogin->setPlaceHolder('LogInError',$template__LogInError->transformTemplate());

          // end if
         }

         return $form__AdminLogin->transformForm();

       // end function
      }

    // end class
   }
?>