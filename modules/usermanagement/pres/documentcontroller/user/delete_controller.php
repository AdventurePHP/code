<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');

   class delete_controller extends baseController
   {

      function delete_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('userid'));

         $Form__No = &$this->__getForm('UserDelNo');
         $Form__Yes = &$this->__getForm('UserDelYes');

         if($Form__Yes->get('isSent')){

            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
            $User = new GenericDomainObject('User');
            $User->setProperty('UserID',$_LOCALS['userid']);
            $uM->deleteUser($User);

            header('Location: ?mainview=user');

          // end if
         }
         elseif($Form__No->get('isSent')){
            header('Location: ?mainview=user');
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