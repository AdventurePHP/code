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
   *  @class edit_controller
   *
   *  Implements the edit controller for a user.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.12.2008<br />
   */
   class edit_controller extends umgtbaseController
   {

      function edit_controller(){
      }


      function transformContent(){

         $userid = RequestHandler::getValue('userid');

         $Form__Edit = &$this->__getForm('UserForm');
         $UserID = &$Form__Edit->getFormElementByName('userid');
         $UserID->setAttribute('value',$userid);

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

         if($Form__Edit->get('isSent') == true){

            if($Form__Edit->get('isValid') == true){

               $Fields = &$Form__Edit->getFormElementsByTagName('form:text');

               $User = new GenericDomainObject('User');
               $User->setProperty('UserID',$userid);

               $fieldcount = count($Fields);
               for($i = 0; $i < $fieldcount; $i++){
                  $User->setProperty($Fields[$i]->getAttribute('name'),$Fields[$i]->getAttribute('value'));
                // end for
               }

               $uM->saveUser($User);
               HeaderManager::forward($this->__generateLink(array('mainview' => 'user', 'userview' => '','userid' => '')));

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
            $User = $uM->loadUserByID($userid);

            // prefill form
            $FirstName = &$Form__Edit->getFormElementByName('FirstName');
            $FirstName->setAttribute('value',$User->getProperty('FirstName'));

            $LastName = &$Form__Edit->getFormElementByName('LastName');
            $LastName->setAttribute('value',$User->getProperty('LastName'));

            $StreetName = &$Form__Edit->getFormElementByName('StreetName');
            $StreetName->setAttribute('value',$User->getProperty('StreetName'));

            $StreetNumber = &$Form__Edit->getFormElementByName('StreetNumber');
            $StreetNumber->setAttribute('value',$User->getProperty('StreetNumber'));

            $ZIPCode = &$Form__Edit->getFormElementByName('ZIPCode');
            $ZIPCode->setAttribute('value',$User->getProperty('ZIPCode'));

            $City = &$Form__Edit->getFormElementByName('City');
            $City->setAttribute('value',$User->getProperty('City'));

            $EMail = &$Form__Edit->getFormElementByName('EMail');
            $EMail->setAttribute('value',$User->getProperty('EMail'));

            $Mobile = &$Form__Edit->getFormElementByName('Mobile');
            $Mobile->setAttribute('value',$User->getProperty('Mobile'));

            $Username = &$Form__Edit->getFormElementByName('Username');
            $Username->setAttribute('value',$User->getProperty('Username'));

            $Password = &$Form__Edit->getFormElementByName('Password');
            $Password->setAttribute('value',$User->getProperty('Password'));

            // display form
            $this->setPlaceHolder('UserEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>