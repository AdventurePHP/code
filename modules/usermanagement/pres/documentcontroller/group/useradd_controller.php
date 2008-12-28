<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class useradd_controller
   *
   *  Implements the controller to list the groups.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class useradd_controller extends umgtbaseController
   {

      function useradd_controller(){
      }


      function transformContent(){

         // initialize form
         $Form__User = &$this->__getForm('User');
         $user = &$Form__User->getFormElementByName('User[]');
         $groupid = RequestHandler::getValue('groupid');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $group = $uM->loadGroupById($groupid);
         $users = $uM->loadUsersNotInGroup($group);
         $count = count($users);

         // display hint, if group has associated all users
         if($count == 0){
            $template = &$this->__getTemplate('NoMoreUser');
            $template->transformOnPlace();
            return true;
          // end if
         }

         // fill multi select field
         for($i = 0; $i < $count; $i++){
            $user->addOption($users[$i]->getProperty('LastName').', '.$users[$i]->getProperty('FirstName'),$users[$i]->getProperty('UserID'));
         // end for
         }

         // add users, if selected
         if($Form__User->get('isSent') && $Form__User->get('isValid')){

            $options = &$user->getSelectedOptions();
            $count = count($options);

            $newUsers = array();
            for($i = 0; $i < $count; $i++){
               $newUsers[] = $options[$i]->getAttribute('value');
             // end for
            }

            $uM->addUsers2Group($newUsers,$groupid);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'group','groupview' => '','groupid' => '')));

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