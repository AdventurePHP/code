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

   /**
    * @namespace tools::form::taglib
    * @class form_taglib_marker
    *
    * Represents the <form:marker /> tag, that can be used to dynamically create forms. Please
    * have a look at the API documentation of the html_taglib_form class for details.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.09.2008<br />
    */
   class form_taglib_marker extends form_control {

      function form_taglib_marker(){
      }

      /**
       * @public
       *
       * Overwrites the onParseTime() method from the Document class, because here's nothing to do.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 03.09.2008<br />
       */
      function onParseTime(){
      }

      /**
       * @public
       *
       * Overwrites the onAfterAppend() method from the Document class, because here's nothing to do.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 03.09.2008<br />
       */
      function onAfterAppend(){
      }

      /**
       * @public
       *
       * Implements the transform() method. Returns an empty string.
       *
       * @return string $Content an empty string
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 03.09.2008<br />
       */
      function transform(){
         return (string)'';
       // end function
      }

    // end class
   }
?>