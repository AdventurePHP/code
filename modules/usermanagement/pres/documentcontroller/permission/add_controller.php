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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class umgt_add_controller
   *
   *  Implements the controller to add a permission.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class umgt_add_controller extends umgtbaseController
   {

      function transformContent(){

         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');

         $Form__PermissionAdd = &$this->__getForm('PermissionAdd');

         if($Form__PermissionAdd->get('isSent') == true && $Form__PermissionAdd->get('isValid') == true){

            $FormValues = RequestHandler::getValues(array('DisplayName','Name','Value'));

            $Permission = new GenericDomainObject('Permission');

            foreach($FormValues as $Key => $Value){

               if(!empty($Value)){
                  $Permission->setProperty($Key,$Value);
                // end if
               }

             // end foreach
            }

            $uM->savePermission($Permission);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'permission', 'permissionview' => '')));

          // end else
         }

         $Form__PermissionAdd->transformOnPlace();

       // end function
      }

    // end class
   }
?>