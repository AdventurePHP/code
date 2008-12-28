<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class edit_controller
   *
   *  Implements the controller to edit a permission set.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class edit_controller extends umgtbaseController
   {

      function edit_controller(){
      }


      function transformContent(){

         // initialize the form
         $permissionsetid = RequestHandler::getValue('permissionsetid');
         $Form__Edit = &$this->__getForm('PermissionSetEdit');
         $PermissionSetID = &$Form__Edit->getFormElementByName('permissionsetid');
         $PermissionSetID->setAttribute('value',$permissionsetid);

         // prefill the multiselect field
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $permSet = new GenericDomainObject('PermissionSet');
         $permSet->setProperty('PermissionSetID',$permissionsetid);
         $allPermissions = $uM->loadPermissionList();
         $permField = &$Form__Edit->getFormElementByName('Permission[]');
         for($i = 0; $i < count($allPermissions); $i++){
            $permField->addOption($allPermissions[$i]->getProperty('DisplayName'),$allPermissions[$i]->getProperty('PermissionID'));
          // end foreach
         }

         // display / save the permission set
         if($Form__Edit->get('isSent') == true){

            if($Form__Edit->get('isValid') == true){

               // compose the permission set
               $permSetField = &$Form__Edit->getFormElementByName('DisplayName');
               $permissionSet = new GenericDomainObject('PermissionSet');
               $permissionSet->setProperty('DisplayName',$permSetField->getAttribute('value'));
               $permissionSet->setProperty('PermissionSetID',$permissionsetid);

               // add the selected permissions
               $permField = &$Form__Edit->getFormElementByName('Permission[]');
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
            $selectedPermissions = $uM->loadPermissionSetPermissions($permSet);
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