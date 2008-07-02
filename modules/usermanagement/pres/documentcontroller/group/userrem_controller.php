<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class userrem_controller extends baseController
   {

      function userrem_controller(){
      }


      function transformContent(){

         $Form__User = &$this->__getForm('User');
         $User = &$Form__User->getFormElementByName('User');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $Users = $uM->loadUserList();
         $count = count($Users);

         for($i = 0; $i < $count; $i++){
            $User->addOption($Users[$i]->getProperty('LastName').', '.$Users[$i]->getProperty('FirstName'),$Users[$i]->getProperty('UserID'));
          // end for
         }

         if($Form__User->get('isSent') && $Form__User->get('isValid')){

            $Option = &$User->getSelectedOption();
            $UserID = $Option->getAttribute('value');
            $_LOCALS = variablenHandler::registerLocal(array('groupid'));
            $uM->removeUserFromGroup($UserID,$_LOCALS['groupid']);
            header('Location: ?mainview=group');

          // end if
         }
         else{
            $Form__User->transformOnPlace();
          // end else
         }

       // end function
      }

    // end class
   }
?>