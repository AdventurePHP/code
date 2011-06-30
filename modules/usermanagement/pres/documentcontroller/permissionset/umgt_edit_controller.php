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
    * @class umgt_edit_controller
    *
    * Implements the controller to edit a permission set.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   class umgt_edit_controller extends umgt_base_controller {

      public function transformContent(){

         // initialize the form
         $permissionsetid = RequestHandler::getValue('permissionsetid');
         $Form__Edit = &$this->getForm('PermissionSetEdit');
         $PermissionSetID = &$Form__Edit->getFormElementByName('permissionsetid');
         $PermissionSetID->setAttribute('value',$permissionsetid);

         // prefill the multiselect field
         $uM = &$this->getManager();
         $permSet = new GenericDomainObject('PermissionSet');
         $permSet->setProperty('PermissionSetID',$permissionsetid);
         $allPermissions = $uM->loadPermissionList();
         $permField = &$Form__Edit->getFormElementByName('Permission');
         for($i = 0; $i < count($allPermissions); $i++){
            $permField->addOption($allPermissions[$i]->getProperty('DisplayName'),$allPermissions[$i]->getProperty('PermissionID'));
          // end foreach
         }

         // display / save the permission set
         if($Form__Edit->isSent() == true){

            if($Form__Edit->isValid() == true){

               // compose the permission set
               $permSetField = &$Form__Edit->getFormElementByName('DisplayName');
               $permissionSet = new GenericDomainObject('PermissionSet');
               $permissionSet->setProperty('DisplayName',$permSetField->getAttribute('value'));
               $permissionSet->setProperty('PermissionSetID',$permissionsetid);

               // add the selected permissions
               $permField = &$Form__Edit->getFormElementByName('Permission');
               $options = &$permField->getSelectedOptions();

               for($i = 0; $i < count($options); $i++){
                  $permission = new GenericDomainObject('Permission');
                  $permission->setProperty('PermissionID',$options[$i]->getAttribute('value'));
                  $permissionSet->addRelatedObject('PermissionSet2Permission',$permission);
                  unset($permission);
                // end for
               }

               $uM->savePermissionSet($permissionSet);
               HeaderManager::forward($this->__generateLink(array('mainview' => 'permissionset', 'permissionsetview' => '','permissionsetid' => '')));

             // end if
            }
            else{
               $this->setPlaceHolder('PermissionSetEdit',$Form__Edit->transformForm());
             // end else
            }

          // end if
         }
         else{

            // load group
            $PermissionSet = $uM->loadPermissionSetByID($permissionsetid);

            // prefill form
            $DisplayName = &$Form__Edit->getFormElementByName('DisplayName');
            $DisplayName->setAttribute('value',$PermissionSet->getProperty('DisplayName'));

            // preselect the options
            $selectedPermissions = $uM->loadPermissionsOfPermissionSet($permSet);
            for($i = 0; $i < count($selectedPermissions); $i++){
               $permField->setOption2Selected($selectedPermissions[$i]->getProperty('PermissionID'));
             // end for
            }

            // display form
            $this->setPlaceHolder('PermissionSetEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>