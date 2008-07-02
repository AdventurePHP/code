<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::html::taglib::documentcontroller','iteratorBaseController');


   class list_controller extends iteratorBaseController
   {

      function list_controller(){
      }

      function transformContent(){

         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('getPagedUserList()');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $UserList = $uM->getPagedUserList();
         $T->stop('getPagedUserList()');

         $Iterator__User = &$this->__getIterator('User');
         $Iterator__User->fillDataContainer($UserList);
         $Iterator__User->transformOnPlace();

       // end function
      }

    // end class
   }
?>