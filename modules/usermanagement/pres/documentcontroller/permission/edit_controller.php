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

   import('modules::usermanagement::pres::documentcontroller','umgt_base_controller');
   import('tools::http','HeaderManager');
   import('tools::request','RequestHandler');

   /**
    * @package modules::usermanagement::pres::documentcontroller
    * @class umgt_edit_controller
    *
    * Implements the controller to edit a permission.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   class umgt_edit_controller extends umgt_base_controller {

      public function transformContent(){

         // get current permission id
         $permissionid = RequestHandler::getValue('permissionid');

         // initialize the form
         $Form__Edit = &$this->__getForm('PermissionEdit');
         $permID = &$Form__Edit->getFormElementByName('permissionid');
         $permID->setAttribute('value',$permissionid);

         $uM = &$this->getManager();

         if($Form__Edit->isSent() == true){

            if($Form__Edit->isValid() == true){

               $Fields = &$Form__Edit->getFormElementsByTagName('form:text');
               $permission = new GenericDomainObject('Permission');
               $permission->setProperty('PermissionID',$permissionid);

               $fieldcount = count($Fields);
               for($i = 0; $i < $fieldcount; $i++){
                  $permission->setProperty($Fields[$i]->getAttribute('name'),$Fields[$i]->getAttribute('value'));
                // end for
               }

               $uM->savePermission($permission);
               HeaderManager::forward($this->__generateLink(array('mainview' => 'permission','permissionview' => '','permissionid' => '')));

             // end if
            }
            else{
               $this->setPlaceHolder('PermissionEdit',$Form__Edit->transformForm());
             // end else
            }

          // end if
         }
         else{

            // load permission
            $permission = $uM->loadPermissionByID($permissionid);

            // prefill form
            $displayName = &$Form__Edit->getFormElementByName('DisplayName');
            $displayName->setAttribute('value',$permission->getProperty('DisplayName'));

            $name = &$Form__Edit->getFormElementByName('Name');
            $name->setAttribute('value',$permission->getProperty('Name'));

            $value = &$Form__Edit->getFormElementByName('Value');
            $value->setAttribute('value',$permission->getProperty('Value'));

            // display form
            $this->setPlaceHolder('PermissionEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>