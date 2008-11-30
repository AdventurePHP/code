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

         $_LOCALS = variablenHandler::registerLocal(array('permissionsetid'));

         $Form__Edit = &$this->__getForm('PermissionSetEdit');
         $PermissionSetID = &$Form__Edit->getFormElementByName('permissionsetid');
         $PermissionSetID->setAttribute('value',$_LOCALS['permissionsetid']);

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

         if($Form__Edit->get('isSent') == true){

            if($Form__Edit->get('isValid') == true){

               $Fields = &$Form__Edit->getFormElementsByTagName('form:text');

               $PermissionSet = new GenericDomainObject('PermissionSet');
               $PermissionSet->setProperty('PermissionSetID',$_LOCALS['permissionsetid']);

               $fieldcount = count($Fields);
               for($i = 0; $i < $fieldcount; $i++){
                  $PermissionSet->setProperty($Fields[$i]->getAttribute('name'),$Fields[$i]->getAttribute('value'));
                // end for
               }

               $uM->savePermissionSet($PermissionSet);
               header('Location: ?mainview=permissionset');

             // end if
            }
            else{
               $this->setPlaceHolder('PermissionSetEdit',$Form__Edit->transformForm());
             // end else
            }

          // end if
         }
         else{

            // load group
            $PermissionSet = $uM->loadPermissionSetByID($_LOCALS['permissionsetid']);

            // prefill form
            $DisplayName = &$Form__Edit->getFormElementByName('DisplayName');
            $DisplayName->setAttribute('value',$PermissionSet->getProperty('DisplayName'));

            // display form
            $this->setPlaceHolder('PermissionSetEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>