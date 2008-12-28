<?php
   import ('modules::usermanagement::pres::documentcontroller', 'umgtbaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class group_controller
   *
   *  Displays the group sub menu.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.12.2008<br />
   */
   class group_controller extends umgtbaseController
   {

      function transformContent(){
         $this->setPlaceHolder('manage_groups',$this->__generateLink(array('mainview' => 'group', 'groupview' => '','groupid' => '')));
         $this->setPlaceHolder('add_group',$this->__generateLink(array('mainview' => 'group', 'groupview' => 'add','groupid' => '')));
       // end function
      }

    // end class
   }
?>