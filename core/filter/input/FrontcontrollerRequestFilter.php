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

   import('core::filter::input','AbstractRequestFilter');


   /**
   *  @namespace core::filter::input
   *  @class FrontcontrollerRequestFilter
   *
   *  Implements the input filter for front controller usage and rewite urls.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 03.06.2007<br />
   */
   class FrontcontrollerRequestFilter extends AbstractRequestFilter
   {

      function FrontcontrollerRequestFilter(){
      }


      /**
      *  @public
      *
      *  Filters the url params for front controller usage with normal urls.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 02.06.2007<br />
      *  Version 0.2, 08.06.2007 (Renamed to "filter()")<br />
      *  Version 0.3, 17.06.2007 (Added stripslashes and htmlentities filter)<br />
      *  Version 0.4, 09.10.2008 (Fixed bug, that an action call without params leads to an error)<br />
      *  Version 0.5, 12.12.2008 (Refactored some code and added english documentation)<br />
      *  Version 0.6, 13.12.2008 (Removed the benchmarker)<br />
      */
      function filter(){

         // get some front controller configuration params
         $fC = &Singleton::getInstance('Frontcontroller');
         $namespaceKeywordDelimiter = $fC->get('NamespaceKeywordDelimiter');
         $actionKeyword = $fC->get('ActionKeyword');
         $keywordClassDelimiter = $fC->get('KeywordClassDelimiter');
         $inputDelimiter = $fC->get('InputDelimiter');
         $keyValueDelimiter = $fC->get('KeyValueDelimiter');

         foreach($_REQUEST as $Key => $Value){

            if(substr_count($Key,$namespaceKeywordDelimiter.$actionKeyword.$keywordClassDelimiter) > 0){

               // get namespace and class from the REQUEST key
               $ActionName = substr($Key,strpos($Key,$keywordClassDelimiter) + strlen($keywordClassDelimiter));
               $ActionNamespace = substr($Key,0,strpos($Key,$namespaceKeywordDelimiter));

               // initialize the input params
               $InputParams = array();

               // create param array
               $ParamsArray = explode($inputDelimiter,$Value);

               for($i = 0; $i < count($ParamsArray); $i++){

                  $TmpAry = explode($keyValueDelimiter,$ParamsArray[$i]);

                  if(isset($TmpAry[0]) && isset($TmpAry[1]) && !empty($TmpAry[0]) && !empty($TmpAry[1])){
                     $InputParams[$TmpAry[0]] = $TmpAry[1];
                   // end if
                  }

                // end foreach
               }

               // add action to the front controller
               $fC->addAction($ActionNamespace,$ActionName,$InputParams);

             // end if
            }

          // end foreach
         }

         // filter the request array
         $this->__filterRequestArray();

       // end function
      }

    // end class
   }
?>