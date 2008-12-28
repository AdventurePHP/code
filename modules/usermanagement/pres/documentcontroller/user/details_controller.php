<?php
   import('tools::request','RequestHandler');
   import('modules::usermanagement::biz','umgtManager');
   import('modules::usermanagement::pres::documentcontroller', 'umgtiteratorbaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class edit_controller
   *
   *  Implements the controller to dispolay a user's details.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.12.2008<br />
   */
   class details_controller extends umgtiteratorbaseController
   {

      function details_controller(){
      }

      function transformContent(){

         // load data
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $userid = RequestHandler::getValue('userid');
         $User = $uM->loadUserByID($userid);

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