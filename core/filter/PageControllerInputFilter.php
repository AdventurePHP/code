<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
   *  @namespace core::filter
   *  @class PageControllerInputFilter
   *
   *  Implements the an input filter, that extracts the front controller actions and applies the,
   *  to the front controller.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 11.12.2008<br />
   */
   class PageControllerInputFilter extends AbstractFilter
   {

      function PageControllerInputFilter(){
      }


      /**
      *  @public core::filter
      *
      *  Reimplements the filter() method. Checks the params present in the url.
      *
      *  @param string $instruction the filter instruction
      *  @param string $content the content to filter
      *  @return string $filteredContent the filtered content
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.12.2008<br />
      */
      function filter($instruction,$content){

         // setup filter
         if($instruction === 'URLRewriting'){
            import('core::filter::input','PagecontrollerRewriteRequestFilter');
            $filter = new PagecontrollerRewriteRequestFilter();
          // end if
         }
         else{
            import('core::filter::input','StandardRequestFilter');
            $filter = new StandardRequestFilter();
          // end else
         }

         // apply filter
         $filter->filter();

       // end function
      }

    // end class
   }
?>