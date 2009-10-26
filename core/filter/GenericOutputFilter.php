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
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   /**
    * @namespace core::filter
    * @class GenericOutputFilter
    *
    * Implements the an output filter, that rewrites links within a HTML code.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.12.2008<br />
    */
   class GenericOutputFilter extends AbstractFilter {

      public function GenericOutputFilter(){
      }

      /**
       * @public
       *
       * Reimplements the filter() method. Rewrites normals links, if url rewriting is active.
       *
       * @param string $instruction the filter instruction
       * @param string $input the content to filter
       * @return string $filteredContent the filtered content
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.12.2008<br />
       */
      public function filter($input){

         $reg = &Singleton::getInstance('Registry');
         $urlRewriting = $reg->retrieve('apf::core','URLRewriting');
         if($urlRewriting === true){
            import('core::filter::output','HtmlLinkRewriteFilter');
            $filter = new HtmlLinkRewriteFilter();
            $input = $filter->filter($input);
          // end if
         }
         
         return $input;

       // end function
      }

    // end class
   }
?>