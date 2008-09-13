<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class edit_controller extends baseController
   {

      function edit_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('groupid'));

         $Form__Edit = &$this->__getForm('GroupEdit');
         $GroupID = &$Form__Edit->getFormElementByName('groupid');
         $GroupID->setAttribute('value',$_LOCALS['groupid']);

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

         if($Form__Edit->get('isSent') == true){

            if($Form__Edit->get('isValid') == true){

               $Fields = &$Form__Edit->getFormElementsByTagName('form:text');

               $Group = new GenericDomainObject('Group');
               $Group->setProperty('GroupID',$_LOCALS['groupid']);

               $fieldcount = count($Fields);
               for($i = 0; $i < $fieldcount; $i++){
                  $Group->setProperty($Fields[$i]->getAttribute('name'),$Fields[$i]->getAttribute('value'));
                // end for
               }

               $uM->saveGroup($Group);
               header('Location: ?mainview=group');

             // end if
            }
            else{
               $this->setPlaceHolder('GroupEdit',$Form__Edit->transformForm());
             // end else
            }

          // end if
         }
         else{

            // load group
            $Group = $uM->loadGroupByID($_LOCALS['groupid']);

            // prefill form
            $DisplayName = &$Form__Edit->getFormElementByName('DisplayName');
            $DisplayName->setAttribute('value',$Group->getProperty('DisplayName'));

            // display form
            $this->setPlaceHolder('GroupEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>