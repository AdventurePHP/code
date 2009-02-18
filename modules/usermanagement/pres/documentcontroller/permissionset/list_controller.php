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
   *  @class umgt_list_controller
   *
   *  Implements the controller to list the existing permission sets.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.12.2008<br />
   */
   class umgt_list_controller extends umgtbaseController
   {

      function transformContent(){

         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');
         $permissionSetList = $uM->getPagedPermissionSetList();
         $buffer = (string)'';
         $template = &$this->__getTemplate('PermissionSet');

         foreach($permissionSetList as $permissionSet){

            $id = $permissionSet->getProperty('PermissionSetID');
            $template->setPlaceHolder('permissionset_details',$this->__generateLink(array('mainview' => 'permissionset','permissionsetview' => 'details','permissionsetid' => $id)));
            $template->setPlaceHolder('permissionset_edit',$this->__generateLink(array('mainview' => 'permissionset','permissionsetview' => 'edit','permissionsetid' => $id)));
            $template->setPlaceHolder('permissionset_delete',$this->__generateLink(array('mainview' => 'permissionset','permissionsetview' => 'delete','permissionsetid' => $id)));
            $template->setPlaceHolder('permissionset_ass2role',$this->__generateLink(array('mainview' => 'permissionset','permissionsetview' => 'ass2role','permissionsetid' => $id)));
            $template->setPlaceHolder('permissionset_detachfromrole',$this->__generateLink(array('mainview' => 'permissionset','permissionsetview' => 'detachfromrole','permissionsetid' => $id)));
            $template->setPlaceHolder('DisplayName',$permissionSet->getProperty('DisplayName'));
            $buffer .= $template->transformTemplate();

          // end foreach
         }

         $this->setPlaceHolder('PermissionSetList',$buffer);

       // end function
      }

    // end class
   }
?>