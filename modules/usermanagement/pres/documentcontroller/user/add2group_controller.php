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
   *  @package modules::usermanagement::pres::documentcontroller
   *  @class umgt_add2group_controller
   *
   *  Implements the controller to add a user to a group.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.12.2008<br />
   */
   class umgt_add2group_controller extends umgtbaseController
   {

      /**
      *  @public
      *
      *  Displays the views to add a user to groups.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.12.2008<br />
      *  Version 0.2, 29.12.2008 (Applied API change of the usermanagement manager)<br />
      */
      function transformContent(){

         // init the form and load the relevant groups
         $userid = RequestHandler::getValue('userid');
         $Form__Group = &$this->__getForm('Group');
         $Group = &$Form__Group->getFormElementByName('Group');
         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');
         $user = $uM->loadUserById($userid);
         $Groups = $uM->loadGroupsNotWithUser($user);
         $count = count($Groups);

         // display a note, if there are no groups to add the user to
         if($count == 0) {
            $Template = &$this->__getTemplate('NoMoreGroups');
            $Template->transformOnPlace();
            return true;
          // end if
         }

         // add the groups to the option field
         for($i = 0; $i < $count; $i++){
            $Group->addOption($Groups[$i]->getProperty('DisplayName'),$Groups[$i]->getProperty('GroupID'));
          // end for
         }

         // handle the click event
         if($Form__Group->isSent() && $Form__Group->isValid()){

            $options = &$Group->getSelectedOptions();
            $count = count($options);

            $newGroups = array();
            for($i = 0; $i < $count; $i++){
               $newGroup = new GenericDomainObject('Group');
               $newGroup->setProperty('GroupID',$options[$i]->getAttribute('value'));
               $newGroups[] = $newGroup;
               unset($newGroup);
             // end for
            }

            $uM->assignUser2Groups($user,$newGroups);
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