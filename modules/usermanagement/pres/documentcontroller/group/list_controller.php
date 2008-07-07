<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::html::taglib::documentcontroller','iteratorBaseController');


   class list_controller extends iteratorBaseController
   {

      function list_controller(){
      }

      function transformContent(){

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $GroupList = $uM->getPagedGroupList();

         $Iterator__User = &$this->__getIterator('Group');
         $Iterator__User->fillDataContainer($GroupList);
         $Iterator__User->transformOnPlace();

       // end function
      }

    // end class
   }
?>