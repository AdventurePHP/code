<?php
   import('modules::usermanagement::biz','umgtManager');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class add_controller
   *
   *  Implements the controller to add a permission set.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class add_controller extends umgtbaseController
   {

      function add_controller(){
      }


      function transformContent(){

         // initialize the form
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $Form__Add = &$this->__getForm('PermissionSetAdd');

         // prefill the multiselect field
         $perms = $uM->loadPermissionList();
         $permission = &$Form__Add->getFormElementByName('Permission[]');
         foreach($perms as $perm){
            $permission->addOption($perm->getProperty('DisplayName'),$perm->getProperty('PermissionID'));
          // end foreach
         }

         // add the permission set
         if($Form__Add->get('isSent') == true && $Form__Add->get('isValid') == true){

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