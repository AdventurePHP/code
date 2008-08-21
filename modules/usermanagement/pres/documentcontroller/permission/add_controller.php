<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class add_controller extends baseController
   {

      function add_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('permissionsetid' => null));
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

         if($_LOCALS['permissionsetid'] == null){

            $Form__PermissionSetSelect = &$this->__getForm('PermissionSetSelect');
            $PermissionSets = $uM->loadPermissionSetList();

            $PermissionSet = &$Form__PermissionSetSelect->getFormElementByName('permissionsetid');

            $count = count($PermissionSets);
            for($i = 0; $i < $count; $i++){
               $PermissionSet->addOption($PermissionSets[$i]->getProperty('DisplayName'),$PermissionSets[$i]->getProperty('PermissionSetID'));
             // end for
            }

            $Form__PermissionSetSelect->transformOnPlace();

          // end if
         }
         else{

            $Form__PermissionAdd = &$this->__getForm('PermissionAdd');

            if($Form__PermissionAdd->get('isSent') == true && $Form__PermissionAdd->get('isValid') == true){

               $FormValues = variablenHandler::registerLocal(array('DisplayName','Name','Value'));

               $Permission = new GenericDomainObject('Permission');

               foreach($FormValues as $Key => $Value){

                  if(!empty($Value)){
                     $Permission->setProperty($Key,$Value);
                   // end if
                  }

                // end foreach
               }

               $PermissionSet = new GenericDomainObject('PermissionSet');
               $PermissionSet->setProperty('PermissionSetID',$_LOCALS['permissionsetid']);
               $uM->savePermission($Permission,$PermissionSet);
               header('Location: ?mainview=permission');

             // end else
            }

            $PermissionSet = &$Form__PermissionAdd->getFormElementByName('permissionsetid');
            $PermissionSet->setAttribute('value',$_LOCALS['permissionsetid']);
            $Form__PermissionAdd->transformOnPlace();

          // end else
         }

       // end function
      }

    // end class
   }
?>