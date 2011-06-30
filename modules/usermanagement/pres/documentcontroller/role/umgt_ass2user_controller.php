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
    * @class umgt_ass2user_controller
    *
    * Implements the controller to assign a role to a user.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    * Version 0.2, 29.12.2008 (Applied API change of the usermanagement manager)<br />
    */
   class umgt_ass2user_controller extends umgt_base_controller {

      public function transformContent(){

         // get role id
         $roleid = RequestHandler::getValue('roleid');

         // initialize the form
         $Form__User = &$this->getForm('User');
         $user = &$Form__User->getFormElementByName('User');
         $uM = &$this->getManager();
         $role = $uM->loadRoleById($roleid);
         $users = $uM->loadUsersNotWithRole($role);
         $count = count($users);

         // display a hint, if a role already assigned to all users
         if($count == 0){
           $template = &$this->getTemplate('NoMoreUser');
           $template->transformOnPlace();
           return true;
         }

         // fill multiselect field
         for($i = 0; $i < $count; $i++){
            $user->addOption($users[$i]->getProperty('LastName').', '.$users[$i]->getProperty('FirstName'),$users[$i]->getProperty('UserID'));
         }

         // assign role to the desired users
         if($Form__User->isSent() && $Form__User->isValid()){

            $options = &$user->getSelectedOptions();
            $newUsers = array();

            for($i = 0; $i < count($options); $i++){
               $newUser = new GenericDomainObject('User');
               $newUser->setProperty('UserID',$options[$i]->getAttribute('value'));
               $newUsers[] = $newUser;
               unset($newUser);
            }

            $uM->assignRole2Users($role,$newUsers);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'role', 'roleview' => '','roleid' => '')));

         } else {
            $Form__User->transformOnPlace();
         }

      }

   }
?>