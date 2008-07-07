<?php
   import('modules::calendar::biz','calendarObject');


   class Day extends calendarObject
   {

      var $__DayNumber;
      var $__hasEvents = false;


      function Day(){
         $this->__DayNumber = null;
       // end function
      }

    // end class
   }
?>