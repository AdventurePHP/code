<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class userrem_controller
   *
   *  Implements the controller to remove a user from a group.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.12.2008<br />
   */
   class userrem_controller extends umgtbaseController
   {

      function userrem_controller(){
      }


      function transformContent(){

         // initialize the form
         $Form__User = &$this->__getForm('User');
         $user = &$Form__User->getFormElementByName('User[]');
         $groupid = RequestHandler::getValue('groupid');
         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');
         $group = $uM->loadGroupById($groupid);
         $users = $uM->loadUsersWithGroup($group);
         $count = count($users);

         // display hint, if no user is assignet to this group
         if($count == 0){
           $template = &$this->__getTemplate('NoMoreUser');
           $template->transformOnPlace();
           return true;
          // end if
         }

         // fill the multiselect field
         for($i = 0; $i < $count; $i++){
            $user->addOption($users[$i]->getProperty('LastName').', '.$users[$i]->getProperty('FirstName'),$users[$i]->getProperty('UserID'));
          // end for
         }

         // remove the desired users
         if($Form__User->get('isSent') && $Form__User->get('isValid')){

            $options = &$user->getSelectedOptions();

            $userIDs = array();
            for($i = 0; $i < count($options); $i++){
               $userIDs[] = $options[$i]->getAttribute('value');
             // end for
            }

            $uM->removeUsersFromGroup($userIDs,$groupid);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'group', 'groupview' => '')));

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