<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgt_base_controller');
   import('tools::http','HeaderManager');

   /**
    * @package modules::usermanagement::pres::documentcontroller
    * @class umgt_remfromgroup_controller
    *
    * Implements the controller to remove a user from a group.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
    */
   class umgt_remfromgroup_controller extends umgt_base_controller {

      public function transformContent(){

         $Form__Group = &$this->getForm('Group');
         $groupField = &$Form__Group->getFormElementByName('Groups');
         $uM = &$this->getManager();
         $userid = RequestHandler::getValue('userid');
         $user = $uM->loadUserbyId($userid);
         $groups = $uM->loadGroupsWithUser($user);
         $count = count($groups);

         if($count == 0){
            $template = $this->getTemplate('NoMoreGroups');
            $template->transformOnPlace();
            return true;
          // end if
         }

         for($i = 0; $i < $count; $i++){
            $groupField->addOption($groups[$i]->getProperty('DisplayName'),$groups[$i]->getProperty('GroupID'));
          // end for
         }

         if($Form__Group->isSent() && $Form__Group->isValid()){

            // read the groups from the form field
            $options = &$groupField->getSelectedOptions();
            $newGroups = array();
            foreach($options as $option){
               $newGroup = new GenericDomainObject('Group');
               $newGroup->setProperty('GroupID',$option->getAttribute('value'));
               $newGroups[] = $newGroup;
               unset($newGroup);
             // end foreach
            }

            $uM->detachUserFromGroups($user,$newGroups);
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