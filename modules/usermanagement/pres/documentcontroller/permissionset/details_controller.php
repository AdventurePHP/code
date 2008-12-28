<?php
   import('tools::request','RequestHandler');
   import('modules::usermanagement::biz','umgtManager');
   import('tools::html::taglib::documentcontroller','iteratorBaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class details_controller
   *
   *  Implements the controller to list the existing permission sets.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class details_controller extends iteratorBaseController
   {

      function details_controller(){
      }

      function transformContent(){

         // load data
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $permissionSetId = RequestHandler::getValue('permissionsetid');
         $PermissionSet = $uM->loadPermissionSetByID($permissionSetId);

         // display user data
         $Template__PermissionSet = &$this->__getTemplate('PermissionSet');
         $Template__PermissionSet->setPlaceHolder('DisplayName',$PermissionSet->getProperty('DisplayName'));
         $Template__PermissionSet->transformOnPlace();

         // display permissions
         $Permissions = $uM->loadPermissionSetPermissions($PermissionSet);
         $Iterator__Permissions = &$this->__getIterator('Permissions');
         $Iterator__Permissions->fillDataContainer($Permissions);
         $Iterator__Permissions->transformOnPlace();

       // end function
      }

    // end class
   }
?>