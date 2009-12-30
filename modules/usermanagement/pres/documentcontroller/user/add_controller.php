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

   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');

   /**
    * @package modules::usermanagement::pres::documentcontroller
    * @class umgt_add_controller
    *
    * Implements the controller to add a user.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
    */
   class umgt_add_controller extends umgtbaseController {

      function transformContent(){

         $formAdd = &$this->__getForm('UserForm');
         if($formAdd->isSent() == true && $formAdd->isValid() == true){

            $formValues = RequestHandler::getValues(array(
                                                          'DisplayName',
                                                          'FirstName',
                                                          'LastName',
                                                          'StreetName',
                                                          'StreetNumber',
                                                          'ZIPCode',
                                                          'City',
                                                          'EMail',
                                                          'Phone',
                                                          'Mobile',
                                                          'Username',
                                                          'Password'
                                                         )
                                                   );

            $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');
            $user = new GenericDomainObject('User');

            foreach($formValues as $key => $value){

               if(!empty($value)){
                  $user->setProperty($key,$value);
                // end if
               }

             // end foreach
            }

            $uM->saveUser($user);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'user','userview' => '')));

          // end else
         }

         $this->setPlaceHolder('UserAdd',$formAdd->transformForm());

       // end function
      }

    // end class
   }
?>