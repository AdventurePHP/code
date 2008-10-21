<?php
   import('modules::calendar::biz','calendarObject');

   class Month extends calendarObject
   {
      var $__MonthNumber;
      var $__MonthName;
      var $__Weeks;


      function Month(){
         $this->__MonthNumber = null;
         $this->__MonthName = null;
         $this->__Weeks = array();
       // end function
      }

    // end class
   }
?>