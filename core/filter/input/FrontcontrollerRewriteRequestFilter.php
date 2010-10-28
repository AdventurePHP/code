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
    * @class FrontcontrollerRewriteRequestFilter
    *
    * Input filter for the front controller in combination with rewritten URLs.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 03.06.2007<br />
    */
   class FrontcontrollerRewriteRequestFilter extends AbstractRequestFilter {

      /**
       * @protected
       * Defines the global URL rewriting delimiter.
       */
      protected $__RewriteURLDelimiter = '/';

      /**
       * @protected
       * Delimiter between params and action strings.
       */
      protected $__ActionDelimiter = '/~/';

      /**
       * @protected
       * Defines the action keyword.
       */
      protected $__FrontcontrollerActionKeyword;

      /**
       * @public
       *
       * Filters a rewritten url for the front controller. Apply action definitions to the front
       * controller to be executed.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 02.06.2007<br />
       * Version 0.2, 08.06.2007 (Renamed to "filter()")<br />
       * Version 0.3, 17.06.2007 (Added stripslashes and htmlentities filter)<br />
       * Version 0.4, 08.09.2007 (Now, the existance of the action keyword indicates, that an action is included. Before, only the action keyword in combination with the action delimiter was used as an action indicator)<br />
       * Version 0.5, 29.09.2007 (Now, $_REQUEST['query'] is cleared)<br />
       * Version 0.6, 13.12.2008 (Removed the benchmarker)<br />
       * Version 0.7, 28.01.2010 (Switched url analyzing from $_SERVER['REQUEST_URL'] to $_REQUEST['query'] to make custom Apache rewrite rules possible)<br />
       */
      public function filter($input){

         // get the front controller and initialize the action keyword
         $fC = &Singleton::getInstance('Frontcontroller');
         $this->__FrontcontrollerActionKeyword = $fC->get('NamespaceKeywordDelimiter').$fC->get('ActionKeyword');

         // extract the PHPSESSID from $_REQUEST if existent
         $PHPSESSID = (string)'';
         $sessionName = ini_get('session.name');

         if(isset($_REQUEST[$sessionName])){
            $PHPSESSID = $_REQUEST[$sessionName];
         }

         // initialize param to analyze
         $query = (string)'';
         if(isset($_REQUEST[self::$REWRITE_QUERY_PARAM])){
            $query = $_REQUEST[self::$REWRITE_QUERY_PARAM];
         }

         // delete the rewite param indicator
         unset($_REQUEST[self::$REWRITE_QUERY_PARAM]);

         // extract actions from the request url, in case the action keyword or the action
         // delimiter is present in url.
         if(substr_count($query,$this->__ActionDelimiter) > 0 || substr_count($query,$this->__FrontcontrollerActionKeyword.'/') > 0){

            // split url by delimiter
            $requestURLParts = explode($this->__ActionDelimiter,$query);

            $count = count($requestURLParts);
            for($i = 0; $i < $count; $i++){

               // remove leading slash
               $requestURLParts[$i] = $this->__deleteTrailingSlash($requestURLParts[$i]);

               if(substr_count($requestURLParts[$i],$this->__FrontcontrollerActionKeyword) > 0){

                  $requestArray = explode($this->__RewriteURLDelimiter,$requestURLParts[$i]);

                  if(isset($requestArray[1])){

                     // create action params
                     $actionNamespace = str_replace($this->__FrontcontrollerActionKeyword,'',$requestArray[0]);
                     $actionName = $requestArray[1];
                     $actionParams = array_slice($requestArray,2);

                     $actionParamsArray = array();

                     $actionParamCount = count($actionParams);
                     if($actionParamCount > 0){
                        $x = 0;
                        while($x <= ($actionParamCount - 1)){
                           if(isset($actionParams[$x + 1])){
                              $actionParamsArray[$actionParams[$x]] = $actionParams[$x + 1];
                           }
                           $x = $x + 2; // increase by two, because next offset is the value!

                        }

                     }

                     $fC->addAction($actionNamespace,$actionName,$actionParamsArray);

                  }

               } else {
                  $paramArray = $this->__createRequestArray($requestURLParts[$i]);
                  $_REQUEST = array_merge($_REQUEST,$paramArray);
               }

            }

         } else {

            // do page controller rewriting!
            $paramArray = $this->__createRequestArray($query);
            $_REQUEST = array_merge($_REQUEST,$paramArray);

         }

         // re-add POST params
         $_REQUEST = array_merge($_REQUEST,$_POST);

         // add PHPSESSID to the request again
         if(!empty($PHPSESSID)){
            $_REQUEST[$sessionName] = $PHPSESSID;
          // end if
         }

         // filter request array
         $this->__filterRequestArray();

       // end function
      }

    // end class
   }
?>