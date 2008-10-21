<?php
   import('core::filter','abstractRequestFilter');


   /**
   *  @package core::filter
   *  @class standardRequestFilter
   *
   *  Implementiert den URL-Filter für den PageController ohne URL-Rewrite-Modus.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 02.06.2007<br />
   */
   class standardRequestFilter extends abstractRequestFilter
   {

      function standardRequestFilter(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Filter-Funktion aus "abstractRequestFilter".<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.06.2007<br />
      */
      function filter(){

         // Request-Array filtern
         $this->__filterRequestArray();

       // end function
      }

    // end class
   }
?>