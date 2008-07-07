<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class add_controller extends baseController
   {

      function add_controller(){
      }


      function transformContent(){

         $Form__Add = &$this->__getForm('RoleAdd');
         if($Form__Add->get('isSent') == true && $Form__Add->get('isValid') == true){

            $FormValues = variablenHandler::registerLocal(array('DisplayName'));

            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
            $Role = new GenericDomainObject('Role');

            foreach($FormValues as $Key => $Value){

               if(!empty($Value)){
                  $Role->setProperty($Key,$Value);
                // end if
               }

             // end foreach
            }

            $uM->saveRole($Role);
            header('Location: ?mainview=role');

          // end else
         }
         $this->setPlaceHolder('RoleAdd',$Form__Add->transformForm());

       // end function
      }

    // end class
   }
?>