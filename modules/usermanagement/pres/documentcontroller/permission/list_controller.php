<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::html::taglib::documentcontroller','iteratorBaseController');


   class list_controller extends iteratorBaseController
   {

      function list_controller(){
      }

      function transformContent(){

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $PermissionList = $uM->getPagedPermissionList();

         $Iterator__Permission = &$this->__getIterator('Permission');
         $Iterator__Permission->fillDataContainer($PermissionList);
         $Iterator__Permission->transformOnPlace();

       // end function
      }

    // end class
   }
?>