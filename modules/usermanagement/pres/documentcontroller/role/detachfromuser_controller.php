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

   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgt_base_controller');
   import('tools::http','HeaderManager');

   /**
    * @package modules::usermanagement::pres::documentcontroller
    * @class umgt_detachfromuser_controller
    *
    * Implements the controller to detach a role from a user.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   class umgt_detachfromuser_controller extends umgt_base_controller {

      public function transformContent(){

         // get the current roleid
         $roleid = RequestHandler::getValue('roleid');

         // initialize the form
         $Form__User = &$this->__getForm('User');
         $user = &$Form__User->getFormElementByName('User');
         $uM = &$this->getManager();
         $role = $uM->loadRoleByID($roleid);
         $users = $uM->loadUsersWithRole($role);
         $count = count($users);

         // display a hint, if no users are assigned to this role
         if($count == 0){
           $template = &$this->__getTemplate('NoMoreUser');
           $template->transformOnPlace();
           return true;
          // end if
         }

         // fill the multiselect field
         for($i = 0; $i < $count; $i++){
            $user->addOption($users[$i]->getProperty('LastName').', '.$users[$i]->getProperty('FirstName'),$users[$i]->getProperty('UserID'));
          // end for
         }

         // detach users from the role
         if($Form__User->isSent() && $Form__User->isValid()){

            $options = &$user->getSelectedOptions();
            $newUsers = array();
            for($i = 0; $i< count($options); $i++){
               $newUser = new GenericDomainObject('User');
               $newUser->setProperty('UserID',$options[$i]->getAttribute('value'));
               $newUsers[] = $newUser;
               unset($newUser);
             // end for
            }
            $uM->detachUsersFromRole($newUsers,$role);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'role', 'roleview' => '','roleid' => '')));

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