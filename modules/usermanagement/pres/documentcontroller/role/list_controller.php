<?php
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
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
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