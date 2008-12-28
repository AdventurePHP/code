<?php
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class menu_controller
   *
   *  Displays the user management menu.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.12.2008<br />
   */
   class menu_controller extends umgtbaseController
   {

      function menu_controller(){
      }



      /**
      *  @public
      *
      *  Displays the main menu.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 2008<br />
      *  Version 0.2, 28.12.2008 (Introducted the excusion array to clean the links)<br />
      */
      function transformContent(){

         // define the general param exclusion array
         $generalExclusion = array('userid' => '','groupid' => '','roleid' => '','permissionsetid' => '','permissionid' => '');

         // display the links
         $this->setPlaceHolder('home',$this->__generateLink(array_merge($generalExclusion,array('mainview' => '','userview' => '','groupview' => '','roleview' => '','permissionsetview' => '','permissionview' => ''))));
         $this->setPlaceHolder('manage_user',$this->__generateLink(array_merge($generalExclusion,array('mainview' => 'user','userview' => ''))));
         $this->setPlaceHolder('manage_groups',$this->__generateLink(array_merge($generalExclusion,array('mainview' => 'group','groupview' => ''))));
         $this->setPlaceHolder('manage_roles',$this->__generateLink(array_merge($generalExclusion,array('mainview' => 'role','roleview' => ''))));
         $this->setPlaceHolder('manage_permissionsets',$this->__generateLink(array_merge($generalExclusion,array('mainview' => 'permissionset','permissionsetview' => ''))));
         $this->setPlaceHolder('manage_permissions',$this->__generateLink(array_merge($generalExclusion,array('mainview' => 'permission','permissionview' => ''))));

       // end function
      }

    // end class
   }
?>