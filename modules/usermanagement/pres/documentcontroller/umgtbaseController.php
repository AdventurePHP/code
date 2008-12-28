<?php
   import('tools::link','frontcontrollerLinkHandler');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class umgtbaseController
   *
   *  Implements a base controller for the concrete document controllers of
   *  the usermanagement module. Includes helper functions.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.12.2008<br />
   */
   class umgtbaseController extends baseController
   {


      /**
      *  @private
      *
      *  Returns a link including the desired params and some standard parts.
      *
      *  @param array $linkParams the desired link params
      *  @param string $baseURL the desired base url
      *  @return string $link the generated link
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.12.2008<br />
      */
      function __generateLink($linkParams,$baseURL = null) {

         if($baseURL === null){
            $baseURL = $_SERVER['REQUEST_URI'];
          // end if
         }

         return frontcontrollerLinkHandler::generateLink($baseURL,$linkParams);

       // end function
      }


      /**
      *  @private
      *
      *  Sets a place holder, if it exists. Avoids error messages if a place holder does not exist
      *  in a template.
      *
      *  @param string $placeHolderName the name of the desired place holder
      *  @param string $value the value
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.12.2008<br />
      */
      //
      function sph($placeHolderName,$value){

         if($this->__placeHolderExists($placeHolderName)){
            $this->setPlaceHolder($placeHolderName,$value);
          // end if
         }

       // end function
      }

    // end class
   }

?>