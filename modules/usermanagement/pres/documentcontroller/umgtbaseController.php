<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   import('tools::link','frontcontrollerLinkHandler');
   import('modules::genericormapper::data','GenericDomainObject');

   /**
    * @namespace modules::usermanagement::pres::documentcontroller
    * @class umgtbaseController
    *
    * Implements a base controller for the concrete document controllers of
    * the usermanagement module. Includes helper functions.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
    */
   class umgtbaseController extends baseController {

      /**
       * @private
       *
       * Returns a link including the desired params and some standard parts.
       *
       * @param string[] $linkParams the desired link params.
       * @param string $baseURL the desired base url.
       * @return string $link the generated link.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.12.2008<br />
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
       * @private
       *
       * Sets a place holder, if it exists. Avoids error messages if a place holder does not exist
       * in a template.
       *
       * @param string $placeHolderName The name of the desired place holder.
       * @param string $value The value.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.12.2008<br />
       */
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