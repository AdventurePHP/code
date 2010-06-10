<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   import('modules::usermanagement::pres::documentcontroller','umgt_base_controller');
   import('tools::http','HeaderManager');
   import('tools::request','RequestHandler');

   /**
    * @package modules::usermanagement::pres::documentcontroller
    * @class umgt_delete_controller
    *
    * Implements the delete controller for a permission.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   class umgt_delete_controller extends umgt_base_controller {

      public function transformContent(){

         $permissionid = RequestHandler::getValue('permissionid');
         $uM = &$this->getManager();
         $Permission = $uM->loadPermissionByID($permissionid);
         $this->setPlaceHolder('DisplayName',$Permission->getProperty('DisplayName'));

         $Form__No = &$this->__getForm('PermissionDelNo');
         $Form__Yes = &$this->__getForm('PermissionDelYes');

         if($Form__Yes->isSent()){

            $Permission = new GenericDomainObject('Permission');
            $Permission->setProperty('PermissionID',$permissionid);
            $uM->deletePermission($Permission);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'permission', 'permissionview' => '','permissionid' => '')));

          // end if
         }
         elseif($Form__No->isSent()){
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