<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class edit_controller
   *
   *  Implements the controller to remove a user from a group.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.12.2008<br />
   */
   class remfromgroup_controller extends umgtbaseController
   {

      function remfromgroup_controller(){
      }


      function transformContent(){

         $Form__Group = &$this->__getForm('Group');
         $groupField = &$Form__Group->getFormElementByName('Groups[]');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $userid = RequestHandler::getValue('userid');
         $User = $uM->loadUserbyId($userid);
         $Groups = $uM->loadUserGroups($User);
         $count = count($Groups);

         if($count == 0){
            $Template = $this->__getTemplate('NoMoreGroups');
            $Template->transformOnPlace();
            return true;
          // end if
         }

         for($i = 0; $i < $count; $i++){
            $groupField->addOption($Groups[$i]->getProperty('DisplayName'),$Groups[$i]->getProperty('GroupID'));
          // end for
         }

         if($Form__Group->get('isSent') && $Form__Group->get('isValid')){

            // read the groups from the form field
            $options = &$groupField->getSelectedOptions();
            $groupIDs = array();
            foreach($options as $option){
               $groupIDs[] = $option->getAttribute('value');
             // end foreach
            }

            // detatch user from the groups
            $uM->removeUserFromGroups($userid,$groupIDs);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'user', 'userview' => '','userid' => '')));

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