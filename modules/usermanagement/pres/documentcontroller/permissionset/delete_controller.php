<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class delete_controller
   *
   *  Implements the controller to delete a permission set.
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

         // get current permission set id
         $permissionSetId = RequestHandler::getValue('permissionsetid');

         // initialize the forms
         $Form__No = &$this->__getForm('PermissionSetDelNo');
         $Form__Yes = &$this->__getForm('PermissionSetDelYes');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $permissionSet = $uM->loadPermissionSetByID($permissionSetId);
         $this->setPlaceHolder('DisplayName',$permissionSet->getProperty('DisplayName'));

         if($Form__Yes->get('isSent')){
            $PermissionSet = new GenericDomainObject('PermissionSet');
            $PermissionSet->setProperty('PermissionSetID',$permissionSetId);
            $uM->deletePermissionSet($permissionSet);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'permissionset', 'permissionsetview' => '','permissionsetid' => '')));
          // end if
         }
         elseif($Form__No->get('isSent')){
            HeaderManager::forward($this->__generateLink(array('mainview' => 'permissionset', 'permissionsetview' => '','permissionsetid' => '')));
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