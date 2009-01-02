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
   *  @class useradd_controller
   *
   *  Implements the controller to list the groups.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class useradd_controller extends umgtbaseController
   {

      function useradd_controller(){
      }


      /**
      *  @public
      *
      *  Displays the view to add users to a group.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      *  Version 0.2, 29.12.2008 (Applied API change of the usermanagement manager)<br />
      */
      function transformContent(){

         // initialize form
         $Form__User = &$this->__getForm('User');
         $user = &$Form__User->getFormElementByName('User[]');
         $groupid = RequestHandler::getValue('groupid');
         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');
         $group = $uM->loadGroupById($groupid);
         $users = $uM->loadUsersNotWithGroup($group);
         $count = count($users);

         // display hint, if group has associated all users
         if($count == 0){
            $template = &$this->__getTemplate('NoMoreUser');
            $template->transformOnPlace();
            return true;
          // end if
         }

         // fill multi select field
         for($i = 0; $i < $count; $i++){
            $user->addOption($users[$i]->getProperty('LastName').', '.$users[$i]->getProperty('FirstName'),$users[$i]->getProperty('UserID'));
         // end for
         }

         // add users, if selected
         if($Form__User->get('isSent') && $Form__User->get('isValid')){

            $options = &$user->getSelectedOptions();
            $count = count($options);

            $newUsers = array();
            for($i = 0; $i < $count; $i++){
               $newUser = new GenericDomainObject('User');
               $newUser->setProperty('UserID',$options[$i]->getAttribute('value'));
               $newUsers[] = $newUser;
               unset($newUser);
             // end for
            }

            $uM->assignUsers2Group($newUsers,$group);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'group','groupview' => '','groupid' => '')));

          // end if
         }
         else{
            $Form__User->transformOnPlace();
         // end else
         }

       // end function
      }

    // end class
   }
?>