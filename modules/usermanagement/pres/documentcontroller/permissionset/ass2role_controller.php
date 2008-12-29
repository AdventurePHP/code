<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class ass2role_controller
   *
   *  Implements the controller to assign a permission set to a role.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class ass2role_controller extends umgtbaseController
   {

      function ass2role_controller(){
      }


      function transformContent(){

         // get permission set id
         $permissionSetId = RequestHandler::getValue('permissionsetid');

         // initialize the form
         $Form__Role = &$this->__getForm('Role');
         $role = &$Form__Role->getFormElementByName('Role[]');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $permissionSet = $uM->loadPermissionSetByID($permissionSetId);
         $roles = $uM->loadRolesNotWithPermissionSet($permissionSet);
         $count = count($roles);

         // display a hint, if a role already assigned to all users
         if($count == 0){
           $template = &$this->__getTemplate('NoMoreRole');
           $template->transformOnPlace();
           return true;
          // end if
         }

         // fill multiselect field
         for($i = 0; $i < $count; $i++){
            $role->addOption($roles[$i]->getProperty('DisplayName'),$roles[$i]->getProperty('RoleID'));
          // end for
         }

         // assign permission set to the desired roles
         if($Form__Role->get('isSent') && $Form__Role->get('isValid')){

            $options = &$role->getSelectedOptions();
            $count = count($options);

            $newRoles = array();
            for($i = 0; $i < $count; $i++){
               $newRole = new GenericDomainObject('Role');
               $newRole->setProperty('RoleID',$options[$i]->getAttribute('value'));
               $newRoles[] = $newRole;
               unset($newRole);
             // end for
            }

            $uM->assignPermissionSet2Roles($permissionSet,$newRoles);
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