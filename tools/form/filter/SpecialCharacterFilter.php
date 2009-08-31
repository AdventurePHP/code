<?php
   import('tools::form::filter','AbstractFormFilter');

   /**
    *
    */
   class SpecialCharacterFilter extends AbstractFormFilter {

      public function filter($input){
         return preg_replace('/[^0-9A-Za-z-_\.& ]/i','',$input);
       // end function
      }

    // end class
   }
?>