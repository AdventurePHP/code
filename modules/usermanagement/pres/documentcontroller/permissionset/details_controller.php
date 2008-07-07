<?php
   import('tools::variablen','variablenHandler');
   import('modules::usermanagement::biz','umgtManager');
   import('tools::html::taglib::documentcontroller','iteratorBaseController');


   class details_controller extends iteratorBaseController
   {

      function details_controller(){
      }

      function transformContent(){

         // load data
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $_LOCALS = variablenHandler::registerLocal(array('permissionsetid'));
         $PermissionSet = $uM->loadPermissionSetByID($_LOCALS['permissionsetid']);

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