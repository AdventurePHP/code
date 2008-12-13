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
   *  @namespace core::filter::input
   *  @class AbstractRequestFilter
   *  @abstract
   *
   *  Implements some basic filter methods used by the derived classes.
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 03.06.2007<br />
   *  Version 0.2, 08.06.2007 (Now inherits from the abstract filter definition class)<br />
   */
   class AbstractRequestFilter extends AbstractFilter
   {

      function AbstractRequestFilter(){
      }


      /**
      *  @private
      *
      *  Creates a request array out of a slash-separated url string.
      *
      *  @param string $URLString URL string
      *  @return array $ReturnArray list of URL params with their corresponding valued
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 03.06.2007<br />
      */
      function __createRequestArray($URLString){

         // remove slashes at the beginning
         $URLString = $this->__deleteTrailingSlash($URLString);

         // create first version of the array
         $requestArray = explode($this->__RewriteURLDelimiter,strip_tags($URLString));

         // initialize some vars
         $returnArray = array();
         $x = 0;


         // walk throug the new request array and combine the key (offset x) and
         // the value (offset x + 1)
         while($x <= (count($requestArray) - 1)){

            if(isset($requestArray[$x + 1])){
               $returnArray[$requestArray[$x]] = $requestArray[$x + 1];
             // end if
            }

            // increment offset with two, because the next offset is the key
            $x = $x + 2;

          // end while
         }

         return $returnArray;

       // end function
      }


      /**
      *  @private
      *
      *  Removes trailing slashes from URL strings.
      *
      *  @param string $URLString URL string
      *  @return string $URLString URL string without trailing slashes
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 03.06.2007<br />
      */
      function __deleteTrailingSlash($URLString){

         if(substr($URLString,0,1) == $this->__RewriteURLDelimiter){
            $URLString = substr($URLString,1);
          // end if
         }

         return $URLString;

       // end function
      }


      /**
      *  @private
      *
      *  Filters the request array. Removed escape sequences and replaces special characters with
      *  their HTML entities to ensure, that form content is displayed correctly.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 17.06.2007<br />
      *  Version 0.2, 26.08.2007 (Added array handling)<br />
      */
      function __filterRequestArray(){

         // get the current 'magic_quotes_gpc' config value
         $MagicQuotesGPC = ini_get('magic_quotes_gpc');

         foreach($_REQUEST as $Key => $Value){

            // remove slashes added before, if 'magic_quotes_gpc' is active
            if(!is_array($Value)){

               if($MagicQuotesGPC == '1'){
                  $_REQUEST[$Key] = htmlspecialchars(stripcslashes($Value));
                // end if
               }
               else{
                  $_REQUEST[$Key] = htmlspecialchars($Value);
                // end
               }

             // end if
            }

          // end foreach
         }

       // end function
      }

    // end class
   }
?>