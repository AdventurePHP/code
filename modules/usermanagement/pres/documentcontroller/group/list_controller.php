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


   /**
   *  @package modules::usermanagement::pres::documentcontroller
   *  @class umgt_list_controller
   *
   *  Implements the controller to list the groups.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class umgt_list_controller extends umgtbaseController
   {

      function transformContent(){

         // load group list
         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');
         $GroupList = $uM->getPagedGroupList();

         // display group list
         $buffer = (string) '';
         $template = &$this->__getTemplate('Group');

         foreach($GroupList as $Group){
            $groupID = $Group->getProperty('GroupID');
            $template->setPlaceHolder('DisplayName',$Group->getProperty('DisplayName'));
            $template->setPlaceHolder('group_edit',$this->__generateLink(array('mainview' => 'group', 'groupview' => 'edit', 'groupid' => $groupID)));
            $template->setPlaceHolder('group_details',$this->__generateLink(array('mainview' => 'group','groupview' => 'details','groupid' => $groupID)));
            $template->setPlaceHolder('group_delete',$this->__generateLink(array('mainview' => 'group','groupview' => 'delete','groupid' => $groupID)));
            $template->setPlaceHolder('group_useradd',$this->__generateLink(array('mainview' => 'group','groupview' => 'useradd','groupid' => $groupID)));
            $template->setPlaceHolder('group_userrem',$this->__generateLink(array('mainview' => 'group','groupview' => 'userrem','groupid' => $groupID)));
            $buffer .= $template->transformTemplate();
          // end foreach
         }

         $this->setPlaceHolder('Grouplist',$buffer);

       // end function
      }

    // end class
   }
?>