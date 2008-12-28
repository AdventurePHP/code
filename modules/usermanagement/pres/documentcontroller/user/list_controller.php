<?php
   import('modules::usermanagement::biz','umgtManager');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class edit_controller
   *
   *  Implements the list controller for users.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.12.2008<br />
   */
   class list_controller extends umgtbaseController
   {

      function list_controller(){
      }


      function transformContent(){

         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('getPagedUserList()');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $userList = $uM->getPagedUserList();
         $T->stop('getPagedUserList()');

         $buffer = '';
         $template = $this->__getTemplate('ListUser');
         foreach($userList as $user){
            $template->setPlaceHolder('FirstName',$user->getProperty('FirstName'));
            $template->setPLaceHolder('LastName',$user->getProperty('LastName'));
            $template->setPLaceHolder('Username',$user->getProperty('Username'));
            $userid = $user->getProperty('UserID');
            $template->setPLaceHolder('LinkUserDetails',$this->__generateLink(array('mainview' => 'user','userview' => 'details','userid' => $userid)));
            $template->setPlaceHolder('LinkUserEdit',$this->__generateLink(array('mainview' => 'user','userview' => 'edit','userid' => $userid)));
            $template->setPlaceHolder('LinkUserDelete',$this->__generateLink(array('mainview' => 'user','userview' => 'delete','userid' => $userid)));
            $template->setPlaceHolder('LinkUserAdd2Group',$this->__generateLink(array('mainview' => 'user','userview' => 'add2group','userid' => $userid)));
            $template->setPlaceHolder('LinkUserRemFromGroup',$this->__generateLink(array('mainview' => 'user','userview' => 'remfromgroup','userid' => $userid)));
            $buffer .= $template->transformTemplate();
          // end foreach
         }

         $this->setPlaceHolder('TemplateUserList', $buffer);

       // end function
      }

    // end class
   }
?>