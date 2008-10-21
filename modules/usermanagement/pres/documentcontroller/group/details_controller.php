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
         $_LOCALS = variablenHandler::registerLocal(array('groupid'));
         $Group = $uM->loadGroupByID($_LOCALS['groupid']);

         // display user data
         $Template__User = &$this->__getTemplate('Group');
         $Template__User->setPlaceHolder('DisplayName',$Group->getProperty('DisplayName'));
         $Template__User->transformOnPlace();

         // display users
         $Users = $uM->loadGroupUsers($Group);
         $Iterator__Users = &$this->__getIterator('Users');
         $Iterator__Users->fillDataContainer($Users);
         $Iterator__Users->transformOnPlace();

       // end function
      }

    // end class
   }
?>