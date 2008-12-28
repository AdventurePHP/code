<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class edit_controller
   *
   *  Implements the controller to edit a group.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class edit_controller extends umgtbaseController
   {

      function edit_controller(){
      }


      function transformContent(){

         $groupid = RequestHandler::getValue('groupid');

         $Form__Edit = &$this->__getForm('GroupEdit');
         $GroupID = &$Form__Edit->getFormElementByName('groupid');
         $GroupID->setAttribute('value',$groupid);

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

         if($Form__Edit->get('isSent') == true){

            if($Form__Edit->get('isValid') == true){

               $Fields = &$Form__Edit->getFormElementsByTagName('form:text');

               $Group = new GenericDomainObject('Group');
               $Group->setProperty('GroupID',$groupid);

               $fieldcount = count($Fields);
               for($i = 0; $i < $fieldcount; $i++){
                  $Group->setProperty($Fields[$i]->getAttribute('name'),$Fields[$i]->getAttribute('value'));
                // end for
               }
               $uM->saveGroup($Group);
               HeaderManager::forward($this->__generateLink(array('mainview' => 'group','groupview' => '','groupid' => '')));

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
            $Group = $uM->loadGroupByID($groupid);

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