<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');

   class delete_controller extends baseController
   {

      function delete_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('roleid'));

         $Form__No = &$this->__getForm('RoleDelNo');
         $Form__Yes = &$this->__getForm('RoleDelYes');

         if($Form__Yes->get('isSent')){

            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
            $Role = new GenericDomainObject('Role');
            $Role->setProperty('RoleID',$_LOCALS['roleid']);
            $uM->deleteRole($Role);

            header('Location: ?mainview=role');

          // end if
         }
         elseif($Form__No->get('isSent')){
            header('Location: ?mainview=role');
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