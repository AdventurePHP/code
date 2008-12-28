<?php
   import('tools::request','RequestHandler');
   import('modules::usermanagement::biz','umgtManager');
   import('tools::html::taglib::documentcontroller','iteratorBaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class details_controller
   *
   *  Implements the controller to show a group's details.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class details_controller extends iteratorBaseController
   {

      function details_controller(){
      }

      function transformContent(){

         // load data
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $groupid = RequestHandler::getValue('groupid');
         $Group = $uM->loadGroupByID($groupid);

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