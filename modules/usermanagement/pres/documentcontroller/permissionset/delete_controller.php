<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');

   class delete_controller extends baseController
   {

      function delete_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('permissionsetid'));

         $Form__No = &$this->__getForm('PermissionSetDelNo');
         $Form__Yes = &$this->__getForm('PermissionSetDelYes');

         if($Form__Yes->get('isSent')){

            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
            $PermissionSet = new GenericDomainObject('PermissionSet');
            $PermissionSet->setProperty('PermissionSetID',$_LOCALS['permissionsetid']);
            $uM->deletePermissionSet($PermissionSet);

            //header('Location: ?mainview=permissionset');

          // end if
         }
         elseif($Form__No->get('isSent')){
            header('Location: ?mainview=permissionset');
          // end elseif
         }
         else{
            $Form__No->transformOnPlace();
            $Form__Yes->transformOnPlace();
          // end else
         }

       // end function
      }

    // end class
   }
?>