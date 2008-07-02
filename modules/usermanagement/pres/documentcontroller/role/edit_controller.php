<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class edit_controller extends baseController
   {

      function edit_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('roleid'));

         $Form__Edit = &$this->__getForm('RoleEdit');
         $GroupID = &$Form__Edit->getFormElementByName('roleid');
         $GroupID->setAttribute('value',$_LOCALS['roleid']);

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

         if($Form__Edit->get('isSent') == true){

            if($Form__Edit->get('isValid') == true){

               $Fields = &$Form__Edit->getFormElementsByTagName('form:text');

               $Role = new GenericDomainObject('Role');
               $Role->setProperty('RoleID',$_LOCALS['roleid']);

               $fieldcount = count($Fields);
               for($i = 0; $i < $fieldcount; $i++){
                  $Role->setProperty($Fields[$i]->getAttribute('name'),$Fields[$i]->getAttribute('value'));
                // end for
               }

               $uM->saveRole($Role);
               header('Location: ?mainview=role');

             // end if
            }
            else{
               $this->setPlaceHolder('RoleEdit',$Form__Edit->transformForm());
             // end else
            }

          // end if
         }
         else{

            // load group
            $Role = $uM->loadRoleByID($_LOCALS['roleid']);

            // prefill form
            $DisplayName = &$Form__Edit->getFormElementByName('DisplayName');
            $DisplayName->setAttribute('value',$Role->getProperty('DisplayName'));

            // display form
            $this->setPlaceHolder('RoleEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>