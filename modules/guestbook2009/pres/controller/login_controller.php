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

   import('modules::guestbook2009::biz','User');

   /**
    * @package modules::guestbook2009::pres
    * @class login_controller
    *
    * Implements the document controller, to handle the login subview.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.05.2009<br />
    */
   class login_controller extends baseController {

      public function transformContent(){
         
         $form = &$this->__getForm('login');

         if($form->isSent() && $form->isValid()){

            $fieldUser = &$form->getFormElementByName('username');
            $fieldPass = &$form->getFormElementByName('password');
            $user = new User();
            $user->setUsername($fieldUser->getAttribute('value'));
            $user->setPassword($fieldPass->getAttribute('value'));

            $gS = &$this->__getDIServiceObject('modules::guestbook2009::biz','GuestbookService');
            if(!$gS->validateCredentials($user)){
               $error = &$this->__getTemplate('error');
               $form->setPlaceHolder('error',$error->transformTemplate());
            }

          // end if
         }

         $form->transformOnPlace();

       // end function
      }

    // end class
   }
?>