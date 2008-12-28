<?php
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class permission_controller
   *
   *  Displays the permission sub menu.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.12.2008<br />
   */
   class permission_controller extends umgtbaseController
   {

      function transformContent(){
         $this->setPlaceHolder('manage_permissions',$this->__generateLink(array('mainview' => 'permission', 'permissionview' => '','permissionid' => '')));
         $this->setPlaceHolder('permission_add',$this->__generateLink(array('mainview' => 'permission','permissionview' => 'add','permissionsetid' => '','permissionid' => '')));
       // end function
      }

    // end class
   }
?>