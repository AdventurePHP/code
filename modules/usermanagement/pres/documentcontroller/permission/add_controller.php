<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class add_controller
   *
   *  Implements the controller to add a permission.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class add_controller extends umgtbaseController
   {

      function add_controller(){
      }


      function transformContent(){

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

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