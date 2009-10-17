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

   import('tools::link','FrontcontrollerLinkHandler');
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
       * @protected
       *
       * Returns a link including the desired params and some standard parts.
       *
       * @param string[] $linkParams the desired link params.
       * @param string $baseURL the desired base url.
       * @return string The generated link.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.12.2008<br />
       */
      protected function __generateLink($linkParams,$baseURL = null) {

         if($baseURL === null){
            $baseURL = $_SERVER['REQUEST_URI'];
          // end if
         }

         return FrontcontrollerLinkHandler::generateLink($baseURL,$linkParams);

       // end function
      }

    // end class
   }

?>