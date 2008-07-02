<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class add_controller extends baseController
   {

      function add_controller(){
      }


      function transformContent(){

         $Form__Add = &$this->__getForm('GroupAdd');
         if($Form__Add->get('isSent') == true && $Form__Add->get('isValid') == true){

            $FormValues = variablenHandler::registerLocal(array('DisplayName'));

            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
            $Group = new GenericDomainObject('Group');

            foreach($FormValues as $Key => $Value){

               if(!empty($Value)){
                  $Group->setProperty($Key,$Value);
                // end if
               }

             // end foreach
            }

            $uM->saveGroup($Group);
            header('Location: ?mainview=group');

          // end else
         }
         $this->setPlaceHolder('GroupAdd',$Form__Add->transformForm());

       // end function
      }

    // end class
   }
?>