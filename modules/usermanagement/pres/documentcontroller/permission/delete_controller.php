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

   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');

   class delete_controller extends baseController
   {

      function delete_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('permissionid'));

         $Form__No = &$this->__getForm('PermissionDelNo');
         $Form__Yes = &$this->__getForm('PermissionDelYes');

         if($Form__Yes->get('isSent')){

            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
            $Permission = new GenericDomainObject('Permission');
            $Permission->setProperty('PermissionID',$_LOCALS['permissionid']);
            $uM->deletePermission($Permission);

            header('Location: ?mainview=permission');

          // end if
         }
         elseif($Form__No->get('isSent')){
            header('Location: ?mainview=permission');
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