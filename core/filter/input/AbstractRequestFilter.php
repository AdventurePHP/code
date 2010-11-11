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
    * @package core::filter::input
    * @class AbstractRequestFilter
    * @abstract
    *
    * Implements some basic filter methods used by the derived classes.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 03.06.2007<br />
    * Version 0.2, 08.06.2007 (Now inherits from the abstract filter definition class)<br />
    */
   abstract class AbstractRequestFilter extends AbstractFilter {

      protected static $REWRITE_QUERY_PARAM = 'apf-rewrited-query';

      /**
       * @protected
       *
       * Creates a request array out of a slash-separated url string.
       *
       * @param string $url URL string.
       * @return string[] List of URL params with their corresponding value.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 03.06.2007<br />
       */
      protected function __createRequestArray($url){

         // remove slashes at the beginning
         $url = $this->__deleteTrailingSlash($url);

         // create first version of the array
         $requestArray = explode($this->__RewriteURLDelimiter,strip_tags($url));

         // initialize some vars
         $returnArray = array();
         $x = 0;

         // walk throug the new request array and combine the key (offset x) and
         // the value (offset x + 1)
         while($x <= (count($requestArray) - 1)){

            if(isset($requestArray[$x + 1])){
               $returnArray[$requestArray[$x]] = $requestArray[$x + 1];
            }

            // increment offset with two, because the next offset is the key
            $x = $x + 2;

         }

         return $returnArray;

      }

      /**
       * @protected
       *
       * Removes trailing slashes from URL strings.
       *
       * @param string $url URL string.
       * @return string URL string without trailing slashes.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 03.06.2007<br />
       */
      protected function __deleteTrailingSlash($url){

         if(substr($url,0,1) == $this->__RewriteURLDelimiter){
            $url = substr($url,1);
         }
         return $url;

      }

      /**
       * @protected
       *
       * Filters the request array. Removed escape sequences and replaces special characters with
       * their HTML entities to ensure, that form content is displayed correctly.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 17.06.2007<br />
       * Version 0.2, 26.08.2007 (Added array handling)<br />
       */
      protected function __filterRequestArray(){

         // get the current 'magic_quotes_gpc' config value
         $magicQuotesGPC = ini_get('magic_quotes_gpc');

         foreach($_REQUEST as $key => $value){

            // remove slashes added before, if 'magic_quotes_gpc' is active
            if(!is_array($value)){

               if($magicQuotesGPC == '1'){
                  $_REQUEST[$key] = htmlspecialchars(stripcslashes($value));
               }
               else{
                  $_REQUEST[$key] = htmlspecialchars($value);
               }

            }

         }

      }

    // end class
   }
?>