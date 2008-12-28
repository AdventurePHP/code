<?php
   import('modules::usermanagement::biz','umgtManager');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class list_controller
   *
   *  Implements the controller to list the existing permission sets.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.12.2008<br />
   */
   class list_controller extends umgtbaseController
   {

      function list_controller(){
      }


      function transformContent(){

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $permissionSetList = $uM->getPagedPermissionSetList();
         $buffer = (string)'';
         $template = &$this->__getTemplate('PermissionSet');

         foreach($permissionSetList as $permissionSet){

            $id = $permissionSet->getProperty('PermissionSetID');
            $template->setPlaceHolder('permissionset_details',$this->__generateLink(array('mainview' => 'permissionset','permissionsetview' => 'details','permissionsetid' => $id)));
            $template->setPlaceHolder('permissionset_edit',$this->__generateLink(array('mainview' => 'permissionset','permissionsetview' => 'edit','permissionsetid' => $id)));
            $template->setPlaceHolder('permissionset_delete',$this->__generateLink(array('mainview' => 'permissionset','permissionsetview' => 'delete','permissionsetid' => $id)));
            $template->setPlaceHolder('DisplayName',$permissionSet->getProperty('DisplayName'));
            $buffer .= $template->transformTemplate();

          // end foreach
         }

         $this->setPlaceHolder('PermissionSetList',$buffer);

       // end function
      }

    // end class
   }
?>