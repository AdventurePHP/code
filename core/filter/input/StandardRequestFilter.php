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
   import('core::filter::input','AbstractRequestFilter');

   /**
    * @package core::filter::input
    * @class StandardRequestFilter
    *
    * Implements the URL filter for the page controller in standard mode.
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 02.06.2007<br />
    */
   class StandardRequestFilter extends AbstractRequestFilter {

      public function StandardRequestFilter(){
      }

      /**
       * @public
       *
       * Checks the request array for malicious code.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 17.06.2007<br />
       * Version 0.2, 11.12.2008 (Added the benchmarker)<br />
       * Version 0.3, 13.12.2008 (Removed the benchmarker)<br />
       */
      public function filter($input){
         $this->__filterRequestArray();
       // end function
      }

    // end class
   }
?>