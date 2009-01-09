<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

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