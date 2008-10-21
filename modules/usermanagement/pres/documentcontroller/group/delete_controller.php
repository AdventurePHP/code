<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');

   class delete_controller extends baseController
   {

      function delete_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('groupid'));

         $Form__No = &$this->__getForm('GroupDelNo');
         $Form__Yes = &$this->__getForm('GroupDelYes');

         if($Form__Yes->get('isSent')){

            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
            $Group = new GenericDomainObject('Group');
            $Group->setProperty('GroupID',$_LOCALS['groupid']);
            $uM->deleteGroup($Group);

            header('Location: ?mainview=group');

          // end if
         }
         elseif($Form__No->get('isSent')){
            header('Location: ?mainview=group');
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