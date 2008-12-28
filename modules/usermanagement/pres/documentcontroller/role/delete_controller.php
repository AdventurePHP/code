<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class delete_controller
   *
   *  Implements the controller to delete a role.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class delete_controller extends umgtbaseController
   {

      function delete_controller(){
      }


      function transformContent(){

         $roleid = RequestHandler::getValue('roleid');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $Role = $uM->loadRoleById($roleid);
         $this->sph('DisplayName', $Role->getProperty('DisplayName'));

         $Form__No = &$this->__getForm('RoleDelNo');
         $Form__Yes = &$this->__getForm('RoleDelYes');

         if($Form__Yes->get('isSent')){

            $Role = new GenericDomainObject('Role');
            $Role->setProperty('RoleID',$roleid);
            $uM->deleteRole($Role);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'role', 'roleview' => '','roleid' => '')));

          // end if
         }
         elseif($Form__No->get('isSent')){
            HeaderManager::forward($this->__generateLink(array('mainview' => 'role', 'roleview' => '','roleid' => '')));
          // end elseif
         }
         else{
            $Form__No->transformOnPlace();
            $Form__Yes->transformOnPlace();
          // end else
         }

       // end function
      }

    // end class
   }
?>