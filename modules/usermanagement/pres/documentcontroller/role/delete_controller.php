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

   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');

   /**
    * @namespace modules::usermanagement::pres::documentcontroller
    * @class umgt_delete_controller
    *
    * Implements the controller to delete a role.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   class umgt_delete_controller extends umgtbaseController {

      function transformContent(){

         $roleId = RequestHandler::getValue('roleid');
         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');
         $role = $uM->loadRoleById($roleId);
         $this->setPlaceHolder('DisplayName',$role->getProperty('DisplayName'));

         $formNo = &$this->__getForm('RoleDelNo');
         $formYes = &$this->__getForm('RoleDelYes');

         if($formYes->isSent()){

            $role = new GenericDomainObject('Role');
            $role->setProperty('RoleID',$roleId);
            $uM->deleteRole($role);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'role', 'roleview' => '','roleid' => '')));

          // end if
         }
         elseif($formNo->isSent()){
            HeaderManager::forward($this->__generateLink(array('mainview' => 'role', 'roleview' => '','roleid' => '')));
          // end elseif
         }
         else{
            $formNo->transformOnPlace();
            $formYes->transformOnPlace();
          // end else
         }

       // end function
      }

    // end class
   }
?>