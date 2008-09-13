<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class edit_controller extends baseController
   {

      function edit_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('permissionid'));

         $Form__Edit = &$this->__getForm('PermissionEdit');
         $PermissionID = &$Form__Edit->getFormElementByName('permissionid');
         $PermissionID->setAttribute('value',$_LOCALS['permissionid']);

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

         if($Form__Edit->get('isSent') == true){

            if($Form__Edit->get('isValid') == true){

               $Fields = &$Form__Edit->getFormElementsByTagName('form:text');

               $Permission = new GenericDomainObject('Permission');
               $Permission->setProperty('PermissionID',$_LOCALS['permissionid']);

               $fieldcount = count($Fields);
               for($i = 0; $i < $fieldcount; $i++){
                  $Permission->setProperty($Fields[$i]->getAttribute('name'),$Fields[$i]->getAttribute('value'));
                // end for
               }

               $uM->savePermission($Permission);
               header('Location: ?mainview=permission');

             // end if
            }
            else{
               $this->setPlaceHolder('PermissionEdit',$Form__Edit->transformForm());
             // end else
            }

          // end if
         }
         else{

            // load user
            $Permission = $uM->loadPermissionByID($_LOCALS['permissionid']);

            // prefill form
            $DisplayName = &$Form__Edit->getFormElementByName('DisplayName');
            $DisplayName->setAttribute('value',$Permission->getProperty('DisplayName'));

            $Name = &$Form__Edit->getFormElementByName('Name');
            $Name->setAttribute('value',$Permission->getProperty('Name'));

            $Value = &$Form__Edit->getFormElementByName('Value');
            $Value->setAttribute('value',$Permission->getProperty('Value'));

            // display form
            $this->setPlaceHolder('PermissionEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>