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

   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');

   /**
    * @namespace modules::usermanagement::pres::documentcontroller
    * @class umgt_edit_controller
    *
    * Implements the edit controller for a user.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
    */
   class umgt_edit_controller extends umgtbaseController {

      /**
       * @public
       *
       * Displays and handles the user edit form.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.12.2008<br />
       * Version 0.2, 02.01.2009 (Added the password fields handling)<br />
       */
      function transformContent(){

         // get the userid from the request
         $userid = RequestHandler::getValue('userid');

         // setup the form
         $formEdit = &$this->__getForm('UserForm');
         $fieldUserId = &$formEdit->getFormElementByName('userid');
         $fieldUserId->setAttribute('value',$userid);

         // get the manager
         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');

         if($formEdit->isSent() == true){

            if($formEdit->isValid() == true){

               // setup the domain object
               $user = new GenericDomainObject('User');
               $user->setProperty('UserID',$userid);

               // read the "normal" fields
               $textFields = &$formEdit->getFormElementsByTagName('form:text');

               for($i = 0; $i < count($textFields); $i++){
                  $user->setProperty($textFields[$i]->getAttribute('name'),$textFields[$i]->getAttribute('value'));
                // end for
               }

               // read the password field
               $passField1 = &$formEdit->getFormElementByName('Password');
               $passField2 = &$formEdit->getFormElementByName('Password2');
               $pass1 = $passField1->getAttribute('value');
               $pass2 = $passField2->getAttribute('value');

               if(!empty($pass1)){

                  if($pass1 !== $pass2){
                     $formEdit->set('isValid',false);
                     $passField1->addAttribute('style','border: 2px solid red;');
                     $passField2->addAttribute('style','border: 2px solid red;');
                     $this->setPlaceHolder('UserEdit',$formEdit->transformForm());
                   // end if
                  }
                  else{

                     // add the password to the object
                     $user->setProperty('Password',$pass2);

                     // save the user
                     $uM->saveUser($user);
                     HeaderManager::forward($this->__generateLink(array('mainview' => 'user', 'userview' => '','userid' => '')));

                   // end else
                  }

                // end if
               }
               else{
                  $uM->saveUser($user);
                  HeaderManager::forward($this->__generateLink(array('mainview' => 'user', 'userview' => '','userid' => '')));
                // end else
               }

             // end if
            }
            else{
               $this->setPlaceHolder('UserEdit',$formEdit->transformForm());
             // end else
            }

          // end if
         }
         else{

            // load user
            $user = $uM->loadUserByID($userid);

            // prefill form
            $firstName = &$formEdit->getFormElementByName('FirstName');
            $firstName->setAttribute('value',$user->getProperty('FirstName'));

            $lastName = &$formEdit->getFormElementByName('LastName');
            $lastName->setAttribute('value',$user->getProperty('LastName'));

            $streetName = &$formEdit->getFormElementByName('StreetName');
            $streetName->setAttribute('value',$user->getProperty('StreetName'));

            $streetNumber = &$formEdit->getFormElementByName('StreetNumber');
            $streetNumber->setAttribute('value',$user->getProperty('StreetNumber'));

            $zipCode = &$formEdit->getFormElementByName('ZIPCode');
            $zipCode->setAttribute('value',$user->getProperty('ZIPCode'));

            $city = &$formEdit->getFormElementByName('City');
            $city->setAttribute('value',$user->getProperty('City'));

            $email = &$formEdit->getFormElementByName('EMail');
            $email->setAttribute('value',$user->getProperty('EMail'));

            $mobile = &$formEdit->getFormElementByName('Mobile');
            $mobile->setAttribute('value',$user->getProperty('Mobile'));

            $username = &$formEdit->getFormElementByName('Username');
            $username->setAttribute('value',$user->getProperty('Username'));

            // display form
            $this->setPlaceHolder('UserEdit',$formEdit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>