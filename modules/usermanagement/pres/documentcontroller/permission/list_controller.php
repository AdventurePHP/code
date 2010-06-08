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
   import('modules::usermanagement::pres::documentcontroller','umgt_base_controller');


   /**
   *  @package modules::usermanagement::pres::documentcontroller
   *  @class umgt_list_controller
   *
   *  Implements the controller to list the existing permissions.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class umgt_list_controller extends umgt_base_controller
   {

      function transformContent(){

         // load the permission list
         $uM = &$this->getManager();
         $permissionList = $uM->getPagedPermissionList();

         // display list
         $buffer = (string)'';
         $template = &$this->__getTemplate('Permission');
         foreach($permissionList as $permission){
            $template->setPlaceHolder('DisplayName', $permission->getProperty('DisplayName'));
            $template->setPlaceHolder('Name',$permission->getProperty('Name'));
            $template->setPlaceHolder('Value',$permission->getProperty('Value'));
            $permID = $permission->getProperty('PermissionID');
            $template->setPlaceHolder('permission_edit',$this->__generateLink(array('mainview' => 'permission','permissionview' => 'edit','permissionid' => $permID)));
            $template->setPlaceHolder('permission_delete',$this->__generateLink(array('mainview' => 'permission','permissionview' => 'delete','permissionid' => $permID)));
            $buffer .= $template->transformTemplate();
          // end foreach
         }
         $this->setPlaceHolder('ListPermissions',$buffer);

       // end function
      }

    // end class
   }
?>