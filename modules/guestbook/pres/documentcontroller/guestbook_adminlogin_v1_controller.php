<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   import('modules::guestbook::biz','GuestbookManager');
   import('core::session','SessionManager');
   import('modules::guestbook::pres::documentcontroller','guestbookBaseController');
   import('tools::link','LinkHandler');
   import('tools::request','RequestHandler');
   import('tools::http','HeaderManager');

   /**
    *  @package modules::guestbook::pres::documentcontroller
    *  @class guestbook_adminlogin_v1_controller
    *
    *  Implementiert den DocumentController f�r das Stylesheet 'adminlogin.html'.<br />
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 05.05.2007<br />
    */
   class guestbook_adminlogin_v1_controller extends guestbookBaseController {

      /**
       *  @public
       *
       *  Implementiert die abstrakte Methode aus APFObject.<br />
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 05.05.2007<br />
       */
      public function transformContent() {

         $values = RequestHandler::getValues(array('Username', 'Password', 'logout'));

         // Handle logout, in case the url contains true for the logout param. This is done, because
         // we don't want to use a front controller action here.
         if ($values['logout'] == 'true') {
            $guestbookNamespace = $this->getGuestbookNamespace();
            $oSessMgr = new SessionManager($guestbookNamespace);
            $oSessMgr->destroySession($guestbookNamespace);
            $link = LinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'display', 'logout' => ''));
            HeaderManager::redirect($link);
         }

         $Form__AdminLogin = &$this->__getForm('AdminLogin');

         if ($Form__AdminLogin->isValid() && $Form__AdminLogin->isSent()) {

            $gM = &$this->getGuestbookManager();

            if ($gM->validateCrendentials($values['Username'], $values['Password']) == true) {

               $oSessMgr = new SessionManager($this->getGuestbookNamespace());
               $oSessMgr->saveSessionData('LoginDate', date('Y-m-d'));
               $oSessMgr->saveSessionData('LoginTime', date('H:i:s'));
               $oSessMgr->saveSessionData('AdminView', true);

               $link = LinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'display'));
               HeaderManager::forward($link);

            } else {
               $this->setPlaceHolder('Form', $this->displayForm(true));
            }

         } else {
            $this->setPlaceHolder('Form', $this->displayForm());
         }

      }

      /**
       *  @private
       *
       *  Implementiert einen Wrapper f�r die Formular-Darstellung.<br />
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 05.05.2007<br />
       */
      private function displayForm($ShowLogInError = false) {

         $adminLogin = &$this->__getForm('AdminLogin');

         if ($ShowLogInError == true) {
            $adminLogin->setPlaceHolder('LogInError', $this->__getTemplate('LogInError')->transformTemplate());
         }

         return $adminLogin->transformForm();

      }

   }
?>