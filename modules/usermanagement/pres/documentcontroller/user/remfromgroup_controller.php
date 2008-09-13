<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class remfromgroup_controller extends baseController
   {

      function remfromgroup_controller(){
      }


      function transformContent(){

         $Form__Group = &$this->__getForm('Group');
         $Group = &$Form__Group->getFormElementByName('Group');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $Groups = $uM->loadGroupList();
         $count = count($Groups);

         for($i = 0; $i < $count; $i++){
            $Group->addOption($Groups[$i]->getProperty('DisplayName'),$Groups[$i]->getProperty('GroupID'));
          // end for
         }

         if($Form__Group->get('isSent') && $Form__Group->get('isValid')){

            $Option = &$Group->getSelectedOption();
            $GroupID = $Option->getAttribute('value');
            $_LOCALS = variablenHandler::registerLocal(array('userid'));
            $uM->removeUserFromGroup($_LOCALS['userid'],$GroupID);
            header('Location: ?mainview=user');

          // end if
         }
         else{
            $Form__Group->transformOnPlace();
          // end else
         }

       // end function
      }

    // end class
   }
?>