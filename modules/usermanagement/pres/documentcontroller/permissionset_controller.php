<?php
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class permissionset_controller
   *
   *  Displays the permission set sub menu.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.12.2008<br />
   */
   class permissionset_controller extends umgtbaseController
   {

      function transformContent(){
         $this->setPlaceHolder('manage_permissionsets',$this->__generateLink(array('mainview' => 'permissionset','permissionsetview' => '','permissionsetid' => '')));
         $this->setPlaceHolder('permissionset_add',$this->__generateLink(array('mainview' => 'permissionset','permissionsetview' => 'add','permissionsetid' => '')));
       // end function
      }

    // end class
   }
?>