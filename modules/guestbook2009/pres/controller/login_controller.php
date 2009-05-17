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
    * Description of login_controller
    *
    * @author Administrator
    */
   class login_controller extends baseController {

      public function transformContent(){
         
         $form = &$this->__getForm('login');

         if($form->get('isSent') && $form->get('isValid')){

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
