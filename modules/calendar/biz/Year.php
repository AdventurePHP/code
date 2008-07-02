<?php
   import('modules::calendar::biz','calendarObject');

   class Year extends calendarObject
   {
      var $__YearNumber;
      var $__Months;


      function Year(){
         $this->__YearNumber = null;
         $this->__Months = array();
       // end function
      }

    // end class
   }
?>