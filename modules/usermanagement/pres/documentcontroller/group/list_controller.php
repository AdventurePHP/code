<?php
   import('modules::usermanagement::biz','umgtManager');
   import('modules::usermanagement::pres::documentcontroller', 'umgtiteratorbaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class list_controller
   *
   *  Implements the controller to list the groups.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class list_controller extends umgtbaseController
   {

      function list_controller(){
      }


      function transformContent(){

         // load group list
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $GroupList = $uM->getPagedGroupList();

         // display group list
         $buffer = (string) '';
         $template = &$this->__getTemplate('Group');

         foreach($GroupList as $Group){
            $groupID = $Group->getProperty('GroupID');
            $template->setPlaceHolder('DisplayName',$Group->getProperty('DisplayName'));
            $template->setPlaceHolder('group_edit',$this->__generateLink(array('mainview' => 'group', 'groupview' => 'edit', 'groupid' => $groupID)));
            $template->setPlaceHolder('group_details',$this->__generateLink(array('mainview' => 'group','groupview' => 'details','groupid' => $groupID)));
            $template->setPlaceHolder('group_delete',$this->__generateLink(array('mainview' => 'group','groupview' => 'delete','groupid' => $groupID)));
            $template->setPlaceHolder('group_useradd',$this->__generateLink(array('mainview' => 'group','groupview' => 'useradd','groupid' => $groupID)));
            $template->setPlaceHolder('group_userrem',$this->__generateLink(array('mainview' => 'group','groupview' => 'userrem','groupid' => $groupID)));
            $buffer .= $template->transformTemplate();
          // end foreach
         }

         $this->setPlaceHolder('Grouplist',$buffer);

       // end function
      }

    // end class
   }
?>