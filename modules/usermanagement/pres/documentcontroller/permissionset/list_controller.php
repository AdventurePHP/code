<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::html::taglib::documentcontroller','iteratorBaseController');


   class list_controller extends iteratorBaseController
   {

      function list_controller(){
      }

      function transformContent(){

         $T = &Singleton::getInstance('benchmarkTimer');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $PermissionSetList = $uM->getPagedPermissionSetList();

         $Iterator__PermissionSet = &$this->__getIterator('PermissionSet');
         $Iterator__PermissionSet->fillDataContainer($PermissionSetList);
         $Iterator__PermissionSet->transformOnPlace();

       // end function
      }

    // end class
   }
?>