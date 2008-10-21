<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::html::taglib::documentcontroller','iteratorBaseController');


   class list_controller extends iteratorBaseController
   {

      function list_controller(){
      }

      function transformContent(){

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $RoleList = $uM->getPagedRoleList();

         $Iterator__User = &$this->__getIterator('Role');
         $Iterator__User->fillDataContainer($RoleList);
         $Iterator__User->transformOnPlace();

       // end function
      }

    // end class
   }
?>