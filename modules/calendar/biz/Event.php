<?php
   class Event extends coreObject
   {
      var $__EventID;
      var $__Title;
      var $__Description;


      function Event(){
         $this->__EventID = null;
         $this->__Title = null;
         $this->__Description = array();
       // end function
      }

    // end class
   }
?>