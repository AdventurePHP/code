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
    * @class umgt_edit_controller
    *
    * Implements the controller to edit a role.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   class umgt_edit_controller extends umgtbaseController
   {

      function transformContent(){

         // get the current role id
         $roleid = RequestHandler::getValue('roleid');

         // initialize the form
         $Form__Edit = &$this->__getForm('RoleEdit');
         $GroupID = &$Form__Edit->getFormElementByName('roleid');
         $GroupID->setAttribute('value',$roleid);

         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');

         if($Form__Edit->isSent() == true){

            if($Form__Edit->isValid() == true){

               $Fields = &$Form__Edit->getFormElementsByTagName('form:text');

               $role = new GenericDomainObject('Role');
               $role->setProperty('RoleID',$roleid);

               $fieldcount = count($Fields);
               for($i = 0; $i < $fieldcount; $i++){
                  $role->setProperty($Fields[$i]->getAttribute('name'),$Fields[$i]->getAttribute('value'));
                // end for
               }

               $uM->saveRole($role);
               HeaderManager::forward($this->__generateLink(array('mainview' => 'role', 'roleview' => '','roleid' => '')));

             // end if
            }
            else{
               $this->setPlaceHolder('RoleEdit',$Form__Edit->transformForm());
             // end else
            }

          // end if
         }
         else{

            // load group
            $role = $uM->loadRoleByID($roleid);

            // prefill form
            $displayName = &$Form__Edit->getFormElementByName('DisplayName');
            $displayName->setAttribute('value',$role->getProperty('DisplayName'));

            // display form
            $this->setPlaceHolder('RoleEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>