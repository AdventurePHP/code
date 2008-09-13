<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');

   class delete_controller extends baseController
   {

      function delete_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('permissionid'));

         $Form__No = &$this->__getForm('PermissionDelNo');
         $Form__Yes = &$this->__getForm('PermissionDelYes');

         if($Form__Yes->get('isSent')){

            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
            $Permission = new GenericDomainObject('Permission');
            $Permission->setProperty('PermissionID',$_LOCALS['permissionid']);
            $uM->deletePermission($Permission);

            header('Location: ?mainview=permission');

          // end if
         }
         elseif($Form__No->get('isSent')){
            header('Location: ?mainview=permission');
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