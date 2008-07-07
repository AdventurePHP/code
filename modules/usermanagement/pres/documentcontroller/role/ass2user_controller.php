<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class ass2user_controller extends baseController
   {

      function ass2user_controller(){
      }


      function transformContent(){

         $Form__User = &$this->__getForm('User');
         $User = &$Form__User->getFormElementByName('User[]');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $Users = $uM->loadUserList();
         $count = count($Users);

         for($i = 0; $i < $count; $i++){
            $User->addOption($Users[$i]->getProperty('LastName').', '.$Users[$i]->getProperty('FirstName'),$Users[$i]->getProperty('UserID'));
          // end for
         }

         if($Form__User->get('isSent') && $Form__User->get('isValid')){

            $Options = &$User->getSelectedOptions();
            $count = count($Options);

            $NewUsers = array();
            for($i = 0; $i < $count; $i++){
               $NewUsers[] = $Options[$i]->getAttribute('value');
             // end for
            }

            $_LOCALS = variablenHandler::registerLocal(array('roleid'));
            $uM->assignRole2Users($_LOCALS['roleid'],$NewUsers);
            header('Location: ?mainview=role');

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