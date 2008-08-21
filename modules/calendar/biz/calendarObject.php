<?php
   class calendarObject extends coreObject
   {

      function calendarObject(){
      }


      function add($Attribute,$Value){
         $this->{'__'.$Attribute}[] = $Value;
       // end function
      }

    // end class
   }
?>