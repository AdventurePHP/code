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

   import('core::frontcontroller','Frontcontroller');

   /**
    * @namespace tools::link
    * @class FrontcontrollerLinkHandler
    *
    * Implements a LinkHandler for front controller purposes.
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 10.02.2007<br />
    * Version 0.2, 24.02.2007 (Added new method generateActionLink())<br />
    * Version 0.3, 08.07.2007 (Complete redesign due to changes of the request filter)<br />
    * Version 0.4, 29.10.2007 (Added new method generateURLParams())<br />
    */
   class FrontcontrollerLinkHandler {

      private function FrontcontrollerLinkHandler(){
      }

      /**
       * @public
       * @static
       * @since 0.4
       *
       * Creates a param array, that contains an action definition and can be applied to the
       * generateLink() method. Please note, that generating an action url param set is
       * slower than generating it manually.
       *
       * @param string $actionNamespace Namespace of the action param to generate.
       * @param string $actionName Name of the action.
       * @param array $actionParams A list of action params to include in the definition.
       * @param bool $urlRewriting Defines, whether the url parts should be generated in url rewrite
       *                           style (true) or not (false).
       * @return string[] List of params to manipulate an front controller link.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.10.2007<br />
       * Version 0.2, 21.06.2008 (Introduced the Registry to retrieve the URLRewriting information)<br />
       */
      public static function generateURLParams($actionNamespace,$actionName,$actionParams = array(),$urlRewriting = null){

         $t = &Singleton::getInstance('BenchmarkTimer');
         $id = 'FrontcontrollerLinkHandler::generateURLParamsByAction('.xmlParser::generateUniqID().')';
         $t->start($id);

         // gather the delimiters used to define an action's url representation
         $fC = &Singleton::getInstance('Frontcontroller');
         $actionKeyword = $fC->get('ActionKeyword');
         $namespaceKeywordDelimiter = $fC->get('NamespaceKeywordDelimiter');
         $namespaceURLDelimiter = $fC->get('NamespaceURLDelimiter');

         // set URLRewrite
         if($urlRewriting === null){
            $reg = &Singleton::getInstance('Registry');
            $urlRewriting = $reg->retrieve('apf::core','URLRewriting');
          // end if
         }

         // initialize the keyword-class delimiter
         if($urlRewriting == true){
            $keywordClassDelimiter = $fC->get('URLRewritingKeywordClassDelimiter');
            $keyValueDelimiter = $fC->get('URLRewritingKeyValueDelimiter');
            $inputDelimiter = $fC->get('URLRewritingInputDelimiter');
          // end if
         }
         else{
            $keywordClassDelimiter = $fC->get('KeywordClassDelimiter');
            $keyValueDelimiter = $fC->get('KeyValueDelimiter');
            $inputDelimiter = $fC->get('InputDelimiter');
          // end else
         }

         $normalKeywordClassDelimiter = $fC->get('KeywordClassDelimiter');
         $rewriteURLDelimiter = '/~/';

         // generate the action identifier
         $offset = str_replace('::','_',$actionNamespace)
                        .$namespaceKeywordDelimiter.$actionKeyword
                        .$keywordClassDelimiter.$actionName;

         // generate the param list
         $params = array();
         if(count($actionParams) > 0){
            foreach($actionParams as $key => $value){
               $params[] = $key.$keyValueDelimiter.$value;
            }
         }

         $t->stop($id);
         return array($offset => implode($inputDelimiter,$params));

       // end function
      }
      
      /**
       * @public
       * @static
       *
       * Implements a link creation tool for front controller based application. Generates links as
       * you know of the LinkHandler facility, but additionally includes actions, that define the
       * class member <code>$__KeepInURL=true</code>. This means, that these actions are automatically
       * included in the url, that is returned by this method.
       * <p/>
       * The first param applies a basic url that is manipulated using the <em>$newParams</em>
       * argument. The third - optional - param defines, whether the url should be generated in
       * rewrite style (true) or not (false).
       * <p/>
       * Example:
       * Applying the url
       * <pre>/Page/ChangeLog/param1/value1/param2/value2</pre>
       * along with the param array
       * <pre>
       * array(
       *       'modules_guestbook_biz-action:LoadEntryList' => 'pagesize:20|pager:false|adminview:true',
       *       'Page' => 'Guestbook'
       *      );
       * </pre>
       * the resulting url with url rewriting on is
       * <pre>/Page/Guestbook/param1/value1/param2/value2/~/modules_guestbook_biz-action/LoadEntryList/pagesize/20/pager/false/adminview/true</pre>
       * In normal url mode, you get
       * <pre>?Page=Guestbook&param1=value1&param2=value2&modules_guestbook_biz-action:LoadEntryList=pagesize:20|pager:false|adminview:true.</pre>
       *
       * @param string $url The base url to generate the link with.
       * @param array $newParams A list of url params for manipulation.
       * @param bool $urlRewriting Indicates, whether the url should be generated in url rewrite
       *                           style (true) or not (false).
       * @return string The desired url.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 24.02.2007<br />
       * Version 0.2, 08.07.2007 (Complete redesign due to redesign of the request filter)<br />
       * Version 0.3, 26.08.2007 (URL is now checked to be a string. URL params do no like multi dimensional arrays!)<br />
       * Version 0.4, 09.11.2007 (Fix for problem with DUMMY actions and filtering for actions with KeepInURL=false)<br />
       * Version 0.5, 10.01.2008 (Fix for problem with DUMMY actions with URL_REWRITING = false)<br />
       * Version 0.6, 21.06.2008 (Introduced the Registry to retrieve the URLRewriting information)<br />
       */
      public static function generateLink($url,$newParams = array(),$urlRewriting = null){

         $t = &Singleton::getInstance('BenchmarkTimer');
         $id = 'FrontcontrollerLinkHandler::generateLink('.md5($url).')';
         $t->start($id);

         // check, if given url is a string. if not print warning and convert to string
         // if we do not convert to string parse_url() will fail!
         if(!is_string($url)){
            $paramStringParts = array();
            foreach($newParams as $paramKey => $paramValue){
               $paramStringParts[] = $paramKey.'='.$paramValue;
            }
            trigger_error('[FrontcontrollerLinkHandler::generateLink()] Given url ('.$url.') is not a string! Given '
               .'parameters are ['.implode(',',$paramStringParts).']',E_USER_WARNING);
            $url = strval($url);
          // end if
         }

         // decode ampersands to get correct url analyze results
         $url = str_replace('&amp;','&',$url);

         // configure params
         $fC = &Singleton::getInstance('Frontcontroller');
         $actionKeyword = $fC->get('ActionKeyword');
         $namespaceKeywordDelimiter = $fC->get('NamespaceKeywordDelimiter');
         $namespaceURLDelimiter = $fC->get('NamespaceURLDelimiter');

         // set URLRewrite
         if($urlRewriting === null){
            $reg = &Singleton::getInstance('Registry');
            $urlRewriting = $reg->retrieve('apf::core','URLRewriting');
          // end if
         }

         if($urlRewriting == true){
            $keywordClassDelimiter = $fC->get('URLRewritingKeywordClassDelimiter');
          // end if
         }
         else{
            $keywordClassDelimiter = $fC->get('KeywordClassDelimiter');
          // end else
         }

         $normalKeywordClassDelimiter = $fC->get('KeywordClassDelimiter');
         $rewriteURLDelimiter = '/~/';

         $params = array();
         $parsedURL = parse_url($url);

         // resolve missing query string
         if(!isset($parsedURL['query'])){
            $parsedURL['query'] = (string)'';
          // end if
         }

         // resolve missing path
         if(!isset($parsedURL['path'])){
            $parsedURL['path'] = (string)'';
          // end if
         }

         // analyze url in rewrite style
         if($urlRewriting == true){

            // check, if action directives are contained
            if(substr_count($parsedURL['path'],$namespaceKeywordDelimiter.$actionKeyword.$keywordClassDelimiter) > 0){

               // check, whether more url parts are contained
               if(substr_count($parsedURL['path'],$rewriteURLDelimiter) > 0){

                  // separate URL by /~/
                  $urlPathParts = explode($rewriteURLDelimiter,$parsedURL['path']);

                  for($i = 0; $i < count($urlPathParts); $i++){

                     // only add pared params, when no action keyword is presend
                     if(substr_count($urlPathParts[$i],$namespaceKeywordDelimiter.$actionKeyword.$keywordClassDelimiter) < 1){
                        // analyze and merge params of the current part
                        $params = array_merge($params,FrontcontrollerLinkHandler::createArrayFromRequestString($urlPathParts[$i]));
                      // end if
                     }
                     else{
                        // register action directive and mark as dummy action. this is 
                        // important for the param merge!
                        $actionURLParts = explode('/',$urlPathParts[$i]);
                        $params = array_merge($params,array(trim($actionURLParts[0].$normalKeywordClassDelimiter.$actionURLParts[1]) => ''));
                      // end else
                     }

                   // end for
                  }

                // end if
               }

             // end if
            }
            else{
               // analyze url path
               $params = array_merge($params,FrontcontrollerLinkHandler::createArrayFromRequestString($parsedURL['path']));
             // end else
            }

          // end if
         }
         else{

            // plsit url by & and =
            $splitURL = explode('&',$parsedURL['query']);
            $splitParameters = array();

            for($i = 0; $i < count($splitURL); $i++){

               // only accepp params with more than 3 characters
               if(strlen($splitURL[$i]) > 3){

                  $equalSign = strpos($splitURL[$i],'=');

                  // create array with key => value couples, in case the url part does not contrain
                  // an action keyword
                  if(substr_count($splitURL[$i],$namespaceKeywordDelimiter.$actionKeyword.$keywordClassDelimiter) < 1){
                     $params[substr($splitURL[$i],0,$equalSign)] = substr($splitURL[$i],$equalSign+1,strlen($splitURL[$i]));
                   // end if
                  }
                  else{
                     // save action instruction as dummy (removed DUMMY in version > 0.4)
                     $params[substr($splitURL[$i],0,$equalSign)] = '';
                   // end else
                  }

                // end if
               }

             // end for
            }

          // end else
         }

         // add actions to the params
         $actions = &$fC->getActions();
         $actionParams = array();

         foreach($actions as $key => $DUMMY){

            $input = &$actions[$key]->getInput();
            $input->getAttribute('lang');
            $input->getAttributesAsString(false);

            // remove conventional sub path from action namespace
            $actionNamespace = str_replace('::actions','',$actions[$key]->get('ActionNamespace'));

            // create param offset
            $arrayKey = str_replace('::',$namespaceURLDelimiter,$actionNamespace)
                           .$namespaceKeywordDelimiter.$actionKeyword
                           .str_replace($keywordClassDelimiter,$normalKeywordClassDelimiter,$keywordClassDelimiter)
                           .($actions[$key]->get('ActionName'));

            // check, whether the action should be kept in url
            if($actions[$key]->get('KeepInURL') == true){

               // create input string
               $input = &$actions[$key]->getInput();
               $Array_Value = $input->getAttributesAsString(false);

               // merge params
               $actionParams = array_merge_recursive($actionParams,array($arrayKey => $Array_Value));

             // end if
            }
            else{
               // delete place holders (aka DUMMY)
               unset($params[$arrayKey]);
             // end else
            }

          // end foreach
         }

         // merge actions along with the params
         $params = array_merge($params,$actionParams);

         // create the final param set (this allows deletions with offsets that are empty or null!)
         $finalParams = array_merge($params,$newParams);

         // create query string
         $query = (string)'';

         if($urlRewriting == true){

            $finalParamsCount = count($finalParams);
            $currentOffset = 1;

            foreach($finalParams as $key => $value){

               // only allow keys with more than 1 character and a minimum length of 0.
               // this enables the developer to delete params by applying an empty string
               // or null as the param's value. in case the value is an array, deny it!
               if(!is_array($value)){

                  if(strlen($key) > 1 && strlen($value) > 0){

                     if(substr_count($key,$namespaceKeywordDelimiter.$actionKeyword) > 0){

                        if($currentOffset < $finalParamsCount){
                           $query .= $rewriteURLDelimiter.trim($key).'/'.trim($value).$rewriteURLDelimiter;
                         // end if
                        }
                        else{
                           $query .= $rewriteURLDelimiter.trim($key).'/'.trim($value);
                         // end else
                        }

                      // end if
                     }
                     else{
                        $query .= '/'.trim($key).'/'.trim($value);
                      // end else
                     }

                   // end if
                  }

                // end if
               }

               $currentOffset++;

             // end foreach
            }

            // rewrite query and replace "/~//"
            $replace = array(
                             $rewriteURLDelimiter.$rewriteURLDelimiter => $rewriteURLDelimiter,
                             $rewriteURLDelimiter.'/' => $rewriteURLDelimiter,
                             ':' => '/',
                             '|' => '/'
                            );
            $query = strtr($query,$replace);

          // end if
         }
         else{

            foreach($finalParams as $key => $value){

               // only allow keys with more than 1 character and a minimum length of 0.
               // this enables the developer to delete params by applying an empty string
               // or null as the param's value. in case the value is an array, deny it!
               if(!is_array($value)){
                  if(strlen($key) > 1 && strlen($value) > 0){

                     // add '?' as first delimiter
                     if(strlen($query) == 0){
                        $query .= '?';
                      // end if
                     }
                     else{
                        $query .= '&';
                      // end else
                     }

                     $query .= trim($key).'='.trim($value);

                   // end if
                  }

                // end if
               }

             // end foreach
            }


            // encode ampersands
            $query = str_replace('&','&amp;',$query);

          // end else
         }

         $hostPart = (string)'';

         // in case schema and host is given add it!
         if(isset($parsedURL['scheme']) && isset($parsedURL['host'])){
            $hostPart .= $parsedURL['scheme'].'://'.$parsedURL['host'];
          // end if
         }

         // if only the host is present, apply it either
         if(!isset($parsedURL['scheme']) && isset($parsedURL['host'])){
            $hostPart .= '/'.$parsedURL['host'];
          // end if
         }

         // assemble final url
         if($urlRewriting == true){

            // remove trailing slashes
            if(substr($query,0,1) == '/'){
               $query = substr($query,1);
             // end if
            }

            $finishedURL = $hostPart.'/'.$query;

          // end if
         }
         else{
            $finishedURL = $hostPart.$parsedURL['path'].$query;
          // end else
         }


         $t->stop($id);
         return $finishedURL;

       // end function
      }


      /**
       * @public
       * @static
       *
       * Creates an array from a given rewrite url string.
       *
       * @param string $requestString The url substring
       * @return string[] An associative array with params an values.
       *
       * @author Christian W. Sch�fer
       * @version
       * Version 0.1, 07.07.2007<br />
       */
      private static function createArrayFromRequestString($requestString){

         $urlParams = array();

         // remove trailing slashes
         if(substr($requestString,0,1) == '/'){
            $requestString = substr($requestString,1);
          // end if
         }

         $paramsArray = explode('/',strip_tags($requestString));
         if(count($paramsArray) > 0){

            $x = 0;

            while($x <= (count($paramsArray) - 1)){

               if(isset($paramsArray[$x + 1])){
                  $urlParams[$paramsArray[$x]] = $paramsArray[$x + 1];
                // end if
               }

               // increment by 2, because the next offset is the key!
               $x = $x + 2;

             // end while
            }

          // end if
         }

         return $urlParams;

       // end function
      }

    // end class
   }
?>