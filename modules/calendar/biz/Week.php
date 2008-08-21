<?php
   import('modules::calendar::biz','calendarObject');

   class Week extends calendarObject
   {
      var $__WeekNumber;
      var $__Days;


      function Week(){
         $this->__WeekNumber = null;
         $this->__Days = array();
       // end function
      }

    // end class
   }
?>