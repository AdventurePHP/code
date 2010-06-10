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
    * @class umgt_detachfromrole_controller
    *
    * Implements the controller to detach a permission set from a role.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   class umgt_detachfromrole_controller extends umgt_base_controller {

      public function transformContent(){

         // get the current permission set id
         $permissionSetId = RequestHandler::getValue('permissionsetid');

         // initialize the form
         $Form__Role = &$this->__getForm('Role');
         $roleField = &$Form__Role->getFormElementByName('Role');
         $uM = &$this->getManager();
         $permissionSet = $uM->loadPermissionSetByID($permissionSetId);
         $roles = $uM->loadRolesWithPermissionSet($permissionSet);
         $count = count($roles);

         // display a hint, if no users are assigned to this role
         if($count == 0){
           $template = &$this->__getTemplate('NoMoreRole');
           $template->transformOnPlace();
           return true;
          // end if
         }

         // fill the multiselect field
         for($i = 0; $i < $count; $i++){
            $roleField->addOption($roles[$i]->getProperty('DisplayName'),$roles[$i]->getProperty('RoleID'));
          // end for
         }

         // detach users from the role
         if($Form__Role->isSent() && $Form__Role->isValid()){

            $options = &$roleField->getSelectedOptions();
            $newRoles = array();

            for($i = 0; $i< count($options); $i++){
               $newRole = new GenericDomainObject('Role');
               $newRole->setProperty('RoleID',$options[$i]->getAttribute('value'));
               $newRoles[] = $newRole;
               unset($newRole);
             // end for
            }

            $uM->detachPermissionSetFromRoles($permissionSet,$newRoles);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'permissionset', 'permissionsetview' => '','permissionsetid' => '')));

          // end if
         }
         else{
            $Form__Role->transformOnPlace();
          // end else
         }

       // end function
      }

    // end class
   }
?>