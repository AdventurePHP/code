<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class delete_controller
   *
   *  Implements the controller to delete a group.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class delete_controller extends umgtbaseController
   {

      function delete_controller(){
      }


      function transformContent(){

         // get the group id from the request
         $groupid = RequestHandler::getValue('groupid');

         // load the current group and print the display name
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $Group = $uM->loadGroupByID($groupid);
         $this->setPlaceHolder('DisplayName', $Group->getProperty('DisplayName'));

         // prepare the forms and execute action
         $Form__No = &$this->__getForm('GroupDelNo');
         $Form__Yes = &$this->__getForm('GroupDelYes');

         if($Form__Yes->get('isSent')){
            $Group = new GenericDomainObject('Group');
            $Group->setProperty('GroupID',$groupid);
            $uM->deleteGroup($Group);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'group','groupview' => '','groupid' => '')));
          // end if
         }
         elseif($Form__No->get('isSent')){
            HeaderManager::forward($this->__generateLink(array('mainview' => 'group','groupview' => '','groupid' => '')));
          // end elseif
         }
         else{
            $Form__No->transformOnPlace();
            $Form__Yes->transformOnPlace();
          // end else
         }

       // end function
      }

    // end class
   }
?>