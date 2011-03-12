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
    * Implements the controller to show a group's details.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   class umgt_details_controller extends umgt_base_controller {

      public function transformContent(){

         // load data
         $uM = &$this->getManager();
         $groupid = RequestHandler::getValue('groupid');
         $group = $uM->loadGroupByID($groupid);

         // display user data
         $Template__User = &$this->getTemplate('Group');
         $Template__User->setPlaceHolder('DisplayName',$group->getProperty('DisplayName'));
         $Template__User->transformOnPlace();

         // display users
         $users = $uM->loadUsersWithGroup($group);
         $Iterator__Users = &$this->__getIterator('Users');
         $Iterator__Users->fillDataContainer($users);
         $Iterator__Users->transformOnPlace();

       // end function
      }

    // end class
   }
?>