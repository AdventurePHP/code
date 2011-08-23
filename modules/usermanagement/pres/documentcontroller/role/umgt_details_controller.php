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

   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgt_base_controller');

   /**
    * @package modules::usermanagement::pres::documentcontroller
    * @class umgt_details_controller
    *
    * Implements the controller to list the existing roles.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   class umgt_details_controller extends umgt_base_controller {

      public function transformContent(){

         // load data
         $uM = &$this->getManager();
         $roleid = RequestHandler::getValue('roleid');
         $role = $uM->loadRoleByID($roleid);

         // display user data
         $this->setPlaceHolder('DisplayName',$role->getProperty('DisplayName'));

         // display users
         $users = $uM->loadUsersWithRole($role);
         $iterator = &$this->getIterator('Users');
         $iterator->fillDataContainer($users);
         $iterator->transformOnPlace();

         // display permissions
         $permissions = $uM->loadPermissionsWithRole($role);
         $iterator = &$this->getIterator('Permissions');
         $iterator->fillDataContainer($permissions);
         $iterator->transformOnPlace();
         
      }

   }
?>