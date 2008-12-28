<?php
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class permission_controller
   *
   *  Displays the user sub menu.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.12.2008<br />
   */
   class user_controller extends umgtbaseController
   {

      function transformContent(){
         $this->setPlaceHolder('manage_user', $this->__generateLink(array('mainview' => 'user', 'userview' => '','userid' => '')));
         $this->setPlaceHolder('user_add', $this->__generateLink(array('mainview' => 'user', 'userview' => 'add','userid' => '')));
       // end function
      }

    // end class
   }
?>