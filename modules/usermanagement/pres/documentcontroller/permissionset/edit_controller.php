<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class edit_controller extends baseController
   {

      function edit_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('permissionsetid'));

         $Form__Edit = &$this->__getForm('PermissionSetEdit');
         $PermissionSetID = &$Form__Edit->getFormElementByName('permissionsetid');
         $PermissionSetID->setAttribute('value',$_LOCALS['permissionsetid']);

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

         if($Form__Edit->get('isSent') == true){

            if($Form__Edit->get('isValid') == true){

               $Fields = &$Form__Edit->getFormElementsByTagName('form:text');

               $PermissionSet = new GenericDomainObject('PermissionSet');
               $PermissionSet->setProperty('PermissionSetID',$_LOCALS['permissionsetid']);

               $fieldcount = count($Fields);
               for($i = 0; $i < $fieldcount; $i++){
                  $PermissionSet->setProperty($Fields[$i]->getAttribute('name'),$Fields[$i]->getAttribute('value'));
                // end for
               }

               $uM->savePermissionSet($PermissionSet);
               header('Location: ?mainview=permissionset');

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
            $PermissionSet = $uM->loadPermissionSetByID($_LOCALS['permissionsetid']);

            // prefill form
            $DisplayName = &$Form__Edit->getFormElementByName('DisplayName');
            $DisplayName->setAttribute('value',$PermissionSet->getProperty('DisplayName'));

            // display form
            $this->setPlaceHolder('PermissionSetEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>