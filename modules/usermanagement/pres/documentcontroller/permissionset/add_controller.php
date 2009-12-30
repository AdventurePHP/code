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
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @package modules::usermanagement::pres::documentcontroller
   *  @class umgt_add_controller
   *
   *  Implements the controller to add a permission set.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class umgt_add_controller extends umgtbaseController
   {

      function transformContent(){

         // initialize the form
         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');
         $Form__Add = &$this->__getForm('PermissionSetAdd');

         // prefill the multiselect field
         $perms = $uM->loadPermissionList();
         $permission = &$Form__Add->getFormElementByName('Permission');
         foreach($perms as $perm){
            $permission->addOption($perm->getProperty('DisplayName'),$perm->getProperty('PermissionID'));
          // end foreach
         }

         // add the permission set
         if($Form__Add->isSent() == true && $Form__Add->isValid() == true){

            // create and fill permission set
            $permSet = &$Form__Add->getFormElementByName('DisplayName');
            $permissionSet = new GenericDomainObject('PermissionSet');
            $permissionSet->setProperty('DisplayName',$permSet->getAttribute('value'));
            $options = &$permission->getSelectedOptions();

            for($i = 0; $i < count($options); $i++){
               $permission = new GenericDomainObject('Permission');
               $permission->setProperty('PermissionID',$options[$i]->getAttribute('value'));
               $permissionSet->addRelatedObject('PermissionSet2Permission',$permission);
               unset($permission);
             // end foreach
            }

            $uM->savePermissionSet($permissionSet);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'permissionset','permissionsetview' => '','permissionsetid' => '')));

          // end else
         }
         $this->setPlaceHolder('PermissionSetAdd',$Form__Add->transformForm());

       // end function
      }

    // end class
   }
?>