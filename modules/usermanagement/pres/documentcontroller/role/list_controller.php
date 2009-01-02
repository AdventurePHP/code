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
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class list_controller
   *
   *  Implements the controller list the existing roles.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class list_controller extends umgtbaseController
   {

      function list_controller(){
      }

      function transformContent(){

         // load role list
         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');
         $roleList = $uM->getPagedRoleList();

         // display list
         $buffer = (string) '';
         $template = &$this->__getTemplate('Role');
         foreach($roleList as $role){

            $roleID = $role->getProperty('RoleID');
            $template->setPlaceHolder('DisplayName',$role->getProperty('DisplayName'));
            $template->setPlaceHolder('role_details',$this->__generateLink(array('mainview' => 'role','roleview' => 'details','roleid' => $roleID)));
            $template->setPlaceHolder('role_edit',$this->__generateLink(array('mainview' => 'role','roleview' => 'edit','roleid' => $roleID)));
            $template->setPlaceHolder('role_delete',$this->__generateLink(array('mainview' => 'role','roleview' => 'delete','roleid' => $roleID)));
            $template->setPlaceHolder('role_ass2user',$this->__generateLink(array('mainview' => 'role','roleview' => 'ass2user','roleid' => $roleID)));
            $template->setPlaceHolder('role_detachfromuser',$this->__generateLink(array('mainview' => 'role','roleview' => 'detachfromuser','roleid' => $roleID)));
            $buffer .= $template->transformTemplate();

          // end foreach
         }
         $this->setPlaceHolder('RoleList',$buffer);

       // end function
      }

    // end class
   }
?>