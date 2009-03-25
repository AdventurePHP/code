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
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class umgt_edit_controller
   *
   *  Implements the edit controller for a user.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.12.2008<br />
   */
   class umgt_edit_controller extends umgtbaseController
   {

      /**
      *  @public
      *
      *  Displays and handles the user edit form.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.12.2008<br />
      *  Version 0.2, 02.01.2009 (Added the password fields handling)<br />
      */
      function transformContent(){

         // get the userid from the request
         $userid = RequestHandler::getValue('userid');

         // setup the form
         $Form__Edit = &$this->__getForm('UserForm');
         $fieldUserId = &$Form__Edit->getFormElementByName('userid');
         $fieldUserId->setAttribute('value',$userid);

         // get the manager
         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');

         if($Form__Edit->get('isSent') == true){

            if($Form__Edit->get('isValid') == true){

               // setup the domain object
               $user = new GenericDomainObject('User');
               $user->setProperty('UserID',$userid);

               // read the "normal" fields
               $textFields = &$Form__Edit->getFormElementsByTagName('form:text');

               for($i = 0; $i < count($textFields); $i++){
                  $user->setProperty($textFields[$i]->getAttribute('name'),$textFields[$i]->getAttribute('value'));
                // end for
               }

               // read the password field
               $passField1 = &$Form__Edit->getFormElementByName('Password');
               $passField2 = &$Form__Edit->getFormElementByName('Password2');
               $pass1 = $passField1->getAttribute('value');
               $pass2 = $passField2->getAttribute('value');

               if(!empty($pass1)){

                  if($pass1 !== $pass2){
                     $Form__Edit->set('isValid',false);
                     $passField1->addAttribute('style','border: 2px solid red;');
                     $passField2->addAttribute('style','border: 2px solid red;');
                     $this->setPlaceHolder('UserEdit',$Form__Edit->transformForm());
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
               $this->setPlaceHolder('UserEdit',$Form__Edit->transformForm());
             // end else
            }

          // end if
         }
         else{

            // load user
            $user = $uM->loadUserByID($userid);

            // prefill form
            $FirstName = &$Form__Edit->getFormElementByName('FirstName');
            $FirstName->setAttribute('value',$user->getProperty('FirstName'));

            $LastName = &$Form__Edit->getFormElementByName('LastName');
            $LastName->setAttribute('value',$user->getProperty('LastName'));

            $StreetName = &$Form__Edit->getFormElementByName('StreetName');
            $StreetName->setAttribute('value',$user->getProperty('StreetName'));

            $StreetNumber = &$Form__Edit->getFormElementByName('StreetNumber');
            $StreetNumber->setAttribute('value',$user->getProperty('StreetNumber'));

            $ZIPCode = &$Form__Edit->getFormElementByName('ZIPCode');
            $ZIPCode->setAttribute('value',$user->getProperty('ZIPCode'));

            $City = &$Form__Edit->getFormElementByName('City');
            $City->setAttribute('value',$user->getProperty('City'));

            $EMail = &$Form__Edit->getFormElementByName('EMail');
            $EMail->setAttribute('value',$user->getProperty('EMail'));

            $Mobile = &$Form__Edit->getFormElementByName('Mobile');
            $Mobile->setAttribute('value',$user->getProperty('Mobile'));

            $username = &$Form__Edit->getFormElementByName('Username');
            $username->setAttribute('value',$user->getProperty('Username'));

            // display form
            $this->setPlaceHolder('UserEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>