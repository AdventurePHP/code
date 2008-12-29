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


      /**
      *  @public
      *
      *  Displays the details of a permission set.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      *  Version 0.2, 29.12.2008 (Added the role list)<br />
      */
      function transformContent(){

         // load data
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $permissionSetId = RequestHandler::getValue('permissionsetid');
         $permissionSet = $uM->loadPermissionSetByID($permissionSetId);

         // display user data
         $Template__PermissionSet = &$this->__getTemplate('PermissionSet');
         $Template__PermissionSet->setPlaceHolder('DisplayName',$permissionSet->getProperty('DisplayName'));
         $Template__PermissionSet->transformOnPlace();

         // display permissions
         $Permissions = $uM->loadPermissionsOfPermissionSet($permissionSet);
         $Iterator__Permissions = &$this->__getIterator('Permissions');
         $Iterator__Permissions->fillDataContainer($Permissions);
         $Iterator__Permissions->transformOnPlace();

         // display roles
         $Roles = $uM->loadRolesWithPermissionSet($permissionSet);
         $Iterator__Roles = &$this->__getIterator('Roles');
         $Iterator__Roles->fillDataContainer($Roles);
         $Iterator__Roles->transformOnPlace();

       // end function
      }

    // end class
   }
?>