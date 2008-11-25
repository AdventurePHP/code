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


   class add_controller extends baseController
   {

      function add_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('permissionsetid' => null));
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

         if($_LOCALS['permissionsetid'] == null){

            $Form__PermissionSetSelect = &$this->__getForm('PermissionSetSelect');
            $PermissionSets = $uM->loadPermissionSetList();

            $PermissionSet = &$Form__PermissionSetSelect->getFormElementByName('permissionsetid');

            $count = count($PermissionSets);
            for($i = 0; $i < $count; $i++){
               $PermissionSet->addOption($PermissionSets[$i]->getProperty('DisplayName'),$PermissionSets[$i]->getProperty('PermissionSetID'));
             // end for
            }

            $Form__PermissionSetSelect->transformOnPlace();

          // end if
         }
         else{

            $Form__PermissionAdd = &$this->__getForm('PermissionAdd');

            if($Form__PermissionAdd->get('isSent') == true && $Form__PermissionAdd->get('isValid') == true){

               $FormValues = variablenHandler::registerLocal(array('DisplayName','Name','Value'));

               $Permission = new GenericDomainObject('Permission');

               foreach($FormValues as $Key => $Value){

                  if(!empty($Value)){
                     $Permission->setProperty($Key,$Value);
                   // end if
                  }

                // end foreach
               }

               $PermissionSet = new GenericDomainObject('PermissionSet');
               $PermissionSet->setProperty('PermissionSetID',$_LOCALS['permissionsetid']);
               $uM->savePermission($Permission,$PermissionSet);
               header('Location: ?mainview=permission');

             // end else
            }

            $PermissionSet = &$Form__PermissionAdd->getFormElementByName('permissionsetid');
            $PermissionSet->setAttribute('value',$_LOCALS['permissionsetid']);
            $Form__PermissionAdd->transformOnPlace();

          // end else
         }

       // end function
      }

    // end class
   }
?>