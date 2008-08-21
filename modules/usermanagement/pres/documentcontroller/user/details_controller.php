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
         $_LOCALS = variablenHandler::registerLocal(array('userid'));
         $User = $uM->loadUserByID($_LOCALS['userid']);

         // display user data
         $Template__User = &$this->__getTemplate('User');
         $Template__User->setPlaceHolder('FirstName',$User->getProperty('FirstName'));
         $Template__User->setPlaceHolder('LastName',$User->getProperty('LastName'));
         $Template__User->setPlaceHolder('EMail',$User->getProperty('EMail'));
         $Template__User->transformOnPlace();

         // display groups
         $Groups = $uM->loadUserGroups($User);
         $Iterator__Groups = &$this->__getIterator('Groups');
         $Iterator__Groups->fillDataContainer($Groups);
         $Iterator__Groups->transformOnPlace();

         // display roles
         $Roles = $uM->loadUserRoles($User);
         $Iterator__Roles = &$this->__getIterator('Roles');
         $Iterator__Roles->fillDataContainer($Roles);
         $Iterator__Roles->transformOnPlace();

       // end function
      }

    // end class
   }
?>