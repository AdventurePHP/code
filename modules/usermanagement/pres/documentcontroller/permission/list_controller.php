<?php
   import('modules::usermanagement::biz','umgtManager');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class list_controller
   *
   *  Implements the controller to list the existing permissions.
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

         // load the permission list
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
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