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


   class edit_controller extends baseController
   {

      function edit_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('permissionid'));

         $Form__Edit = &$this->__getForm('PermissionEdit');
         $PermissionID = &$Form__Edit->getFormElementByName('permissionid');
         $PermissionID->setAttribute('value',$_LOCALS['permissionid']);

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

         if($Form__Edit->get('isSent') == true){

            if($Form__Edit->get('isValid') == true){

               $Fields = &$Form__Edit->getFormElementsByTagName('form:text');

               $Permission = new GenericDomainObject('Permission');
               $Permission->setProperty('PermissionID',$_LOCALS['permissionid']);

               $fieldcount = count($Fields);
               for($i = 0; $i < $fieldcount; $i++){
                  $Permission->setProperty($Fields[$i]->getAttribute('name'),$Fields[$i]->getAttribute('value'));
                // end for
               }

               $uM->savePermission($Permission);
               header('Location: ?mainview=permission');

             // end if
            }
            else{
               $this->setPlaceHolder('PermissionEdit',$Form__Edit->transformForm());
             // end else
            }

          // end if
         }
         else{

            // load user
            $Permission = $uM->loadPermissionByID($_LOCALS['permissionid']);

            // prefill form
            $DisplayName = &$Form__Edit->getFormElementByName('DisplayName');
            $DisplayName->setAttribute('value',$Permission->getProperty('DisplayName'));

            $Name = &$Form__Edit->getFormElementByName('Name');
            $Name->setAttribute('value',$Permission->getProperty('Name'));

            $Value = &$Form__Edit->getFormElementByName('Value');
            $Value->setAttribute('value',$Permission->getProperty('Value'));

            // display form
            $this->setPlaceHolder('PermissionEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>