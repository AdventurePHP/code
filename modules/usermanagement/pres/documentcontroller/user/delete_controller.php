<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class edit_controller
   *
   *  Implements the controller to delete a user.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.12.2008<br />
   */
   class delete_controller extends umgtbaseController
   {

      function delete_controller(){
      }


      function transformContent(){

         $userid = RequestHandler::getValue('userid');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $User = $uM->loadUserById($userid);
         $this->setPlaceHolder('DisplayName', $User->getProperty('DisplayName'));
         $Form__No = &$this->__getForm('UserDelNo');
         $Form__Yes = &$this->__getForm('UserDelYes');

         if($Form__Yes->get('isSent')){

            $User = new GenericDomainObject('User');
            $User->setProperty('UserID',$userid);
            $uM->deleteUser($User);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'user', 'userview' => '','userid' => '')));

          // end if
         }
         elseif($Form__No->get('isSent')){
            HeaderManager::forward($this->__generateLink(array('mainview' => 'user', 'userview' => '','userid' => '')));
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