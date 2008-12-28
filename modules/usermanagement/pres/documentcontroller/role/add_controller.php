<?php
   import('modules::usermanagement::biz','umgtManager');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class add_controller
   *
   *  Implements the controller to add a role.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class add_controller extends umgtbaseController
   {

      function add_controller(){
      }


      function transformContent(){

         $Form__Add = &$this->__getForm('RoleAdd');
         if($Form__Add->get('isSent') == true && $Form__Add->get('isValid') == true){

            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
            $Role = new GenericDomainObject('Role');

            $displayName = &$Form__Add->getFormElementByName('DisplayName');
            $Role->setProperty('DisplayName',$displayName->getAttribute('value'));
            $uM->saveRole($Role);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'role','roleview' => '','roleid' => '')));

          // end else
         }
         $Form__Add->transformOnPlace();

       // end function
      }

    // end class
   }
?>