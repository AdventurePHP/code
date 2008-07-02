<?php
   import('tools::variablen','variablenHandler');
   import('modules::usermanagement::biz','umgtManager');
   import('tools::html::taglib::documentcontroller','iteratorBaseController');


   class details_controller extends iteratorBaseController
   {

      function details_controller(){
      }

      function transformContent(){

         // load data
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $_LOCALS = variablenHandler::registerLocal(array('roleid'));
         $Role = $uM->loadRoleByID($_LOCALS['roleid']);

         // display user data
         $Template__User = &$this->__getTemplate('Role');
         $Template__User->setPlaceHolder('DisplayName',$Role->getProperty('DisplayName'));
         $Template__User->transformOnPlace();

         // display users
         $Users = $uM->loadRoleUsers($Role);
         $Iterator__Users = &$this->__getIterator('Users');
         $Iterator__Users->fillDataContainer($Users);
         $Iterator__Users->transformOnPlace();

       // end function
      }

    // end class
   }
?>