<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class add_controller extends baseController
   {

      function add_controller(){
      }


      function transformContent(){

         $Form__Add = &$this->__getForm('PermissionSetAdd');
         if($Form__Add->get('isSent') == true && $Form__Add->get('isValid') == true){

            $FormValues = variablenHandler::registerLocal(array('DisplayName'));

            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
            $PermissionSet = new GenericDomainObject('PermissionSet');

            foreach($FormValues as $Key => $Value){

               if(!empty($Value)){
                  $PermissionSet->setProperty($Key,$Value);
                // end if
               }

             // end foreach
            }

            $uM->savePermissionSet($PermissionSet);
            header('Location: ?mainview=permissionset');

          // end else
         }
         $this->setPlaceHolder('PermissionSetAdd',$Form__Add->transformForm());

       // end function
      }

    // end class
   }
?>