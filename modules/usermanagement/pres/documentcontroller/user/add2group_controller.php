<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class add2group_controller extends baseController
   {

      function add2group_controller(){
      }


      function transformContent(){

         $Form__Group = &$this->__getForm('Group');
         $Group = &$Form__Group->getFormElementByName('Group[]');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $_LOCALS = variablenHandler::registerLocal(array('userid'));
         $Groups = $uM->loadGroupList($_LOCALS['userid'],true);
         $count = count($Groups);

         for($i = 0; $i < $count; $i++){
            $Group->addOption($Groups[$i]->getProperty('DisplayName'),$Groups[$i]->getProperty('GroupID'));
          // end for
         }

         if($Form__Group->get('isSent') && $Form__Group->get('isValid')){

            $Options = &$Group->getSelectedOptions();
            $count = count($Options);

            $NewGroups = array();
            for($i = 0; $i < $count; $i++){
               $NewGroups[] = $Options[$i]->getAttribute('value');
             // end for
            }


            $uM->addUser2Groups($_LOCALS['userid'],$NewGroups);
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