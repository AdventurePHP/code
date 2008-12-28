<?php
   import('modules::usermanagement::biz','umgtManager');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');
   import('tools::request','RequestHandler');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class delete_controller
   *
   *  Implements the delete controller for a permission.
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

         $permissionid = RequestHandler::getValue('permissionid');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $Permission = $uM->loadPermissionByID($permissionid);
         $this->setPlaceHolder('DisplayName',$Permission->getProperty('DisplayName'));

         $Form__No = &$this->__getForm('PermissionDelNo');
         $Form__Yes = &$this->__getForm('PermissionDelYes');

         if($Form__Yes->get('isSent')){

            $Permission = new GenericDomainObject('Permission');
            $Permission->setProperty('PermissionID',$permissionid);
            $uM->deletePermission($Permission);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'permission', 'permissionview' => '','permissionid' => '')));

          // end if
         }
         elseif($Form__No->get('isSent')){
            HeaderManager::forward($this->__generateLink(array('mainview' => 'permission', 'permissionview' => '','permissionid' => '')));
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