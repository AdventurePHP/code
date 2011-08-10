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
 * @package core::filter
 * @class ChainedGenericInputFilter
 *
 * Implements the default APF filter that resolves the URL layout of the
 * front and page controller with respect to to the url rewriting configuration
 * settings.
 * <p/>
 * The APF url layout includes generic parameter mapping and any number of front
 * controller actions encoded into the url. Rewritten urls can define any number
 * of slash-separated params (e.g. foo/bar) that is translated to the $_REQUEST
 * and $_GET superglobal. Further, front controller actions separated from normal
 * parameters are analyzed and applied to the front controller as action to execute.
 * <p/>
 * Since the front controller url layout resolving mechanism includes the page
 * controller behaviour, release 1.14 shipps only one filter.
 * <p/>
 * In order to create your own url layout resolver, implement the
 * <em>ChainedContentFilter</em> interface and add it to the resetted filter
 * chain. Details and examples can be found within the manual.
 *
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.03.2011 (Initial migration from 1.13 concept)<br />
 */
class ChainedGenericInputFilter implements ChainedContentFilter {

   /**
    * @var string Defines the url parameter that is passed the
    *             current request url by an apache rewrite rule.
    */
   protected static $REWRITE_QUERY_PARAM = 'apf-rewrited-query';

   /**
    * @protected
    * Defines the global URL rewriting delimiter.
    */
   protected static $REWRITE_URL_DELIMITER = '/';

   /**
    * @protected
    * Delimiter between params and action strings.
    */
   protected static $ACTION_TO_PARAM_DELIMITER = '/~/';

   /**
    * @protected
    * Defines the action keyword.
    */
   protected static $FC_ACTION_KEYWORD = '-action';

   public function filter(FilterChain &$chain, $input = null) {

      $t = &Singleton::getInstance('BenchmarkTimer');
      /* @var $t BenchmarkTimer */
      $t->start('ChainedGenericInputFilter');

      // apply desired filter method
      $urlRewriting = Registry::retrieve('apf::core', 'URLRewriting');
      $filter = null;
      if ($urlRewriting === true) {
         $this->filterRewriteUrl();
      } else {
         $this->filterStandardUrl();
      }

      $t->stop('ChainedGenericInputFilter');

      // delegate further filtering to the applied chain
      $chain->filter($input);
   }

   /**
    * @private
    *
    * Resolves the url layout for APF rewrite urls.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.03.2011 (Initial migration from various filter classes into one)<br />
    */
   private function filterRewriteUrl() {

      // extract the PHPSESSID from $_REQUEST if existent
      $PHPSESSID = (string)'';
      $sessionName = ini_get('session.name');

      if (isset($_REQUEST[$sessionName])) {
         $PHPSESSID = $_REQUEST[$sessionName];
      }

      // initialize param to analyze
      $query = (string)'';
      if (isset($_REQUEST[self::$REWRITE_QUERY_PARAM])) {
         $query = $_REQUEST[self::$REWRITE_QUERY_PARAM];
      }

      // delete the rewite param indicator
      unset($_REQUEST[self::$REWRITE_QUERY_PARAM]);
      unset($_GET[self::$REWRITE_QUERY_PARAM]);

      // extract actions from the request url, in case the action keyword or the action
      // delimiter is present in url.
      if (substr_count($query, self::$ACTION_TO_PARAM_DELIMITER) > 0 || substr_count($query, self::$FC_ACTION_KEYWORD . '/') > 0) {

         $fC = &Singleton::getInstance('Frontcontroller');
         /* @var $fC Frontcontroller */

         // split url by delimiter
         $requestURLParts = explode(self::$ACTION_TO_PARAM_DELIMITER, $query);

         $count = count($requestURLParts);
         for ($i = 0; $i < $count; $i++) {

            // remove leading slash
            $requestURLParts[$i] = $this->deleteTrailingSlash($requestURLParts[$i]);

            if (substr_count($requestURLParts[$i], self::$FC_ACTION_KEYWORD) > 0) {

               $requestArray = explode(self::$REWRITE_URL_DELIMITER, $requestURLParts[$i]);

               if (isset($requestArray[1])) {

                  // create action params
                  $actionNamespace = str_replace(self::$FC_ACTION_KEYWORD, '', $requestArray[0]);
                  $actionName = $requestArray[1];
                  $actionParams = array_slice($requestArray, 2);

                  $actionParamsArray = array();

                  $actionParamCount = count($actionParams);
                  if ($actionParamCount > 0) {
                     $x = 0;
                     while ($x <= ($actionParamCount - 1)) {
                        if (isset($actionParams[$x + 1])) {
                           $actionParamsArray[$actionParams[$x]] = $actionParams[$x + 1];
                        }
                        $x = $x + 2; // increase by two, because next offset is the value!
                     }
                  }

                  $fC->addAction($actionNamespace, $actionName, $actionParamsArray);
               }
            } else {
               $paramArray = $this->createRequestArray($requestURLParts[$i]);
               $_REQUEST = array_merge($_REQUEST, $paramArray);
            }
         }
      } else {

         // do page controller rewriting!
         $paramArray = $this->createRequestArray($query);
         $_REQUEST = array_merge($_REQUEST, $paramArray);
      }

      // re-initialize GET params to support e.g. form submition
      $_GET = $_REQUEST;

      // re-add POST params
      $_REQUEST = array_merge($_REQUEST, $_POST);

      // add PHPSESSID to the request again
      if (!empty($PHPSESSID)) {
         $_REQUEST[$sessionName] = $PHPSESSID;
      }
   }

   /**
    * @private
    *
    * Resolves the url layout for APF standard urls.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.03.2011 (Initial migration from various filter classes into one)<br />
    */
   private function filterStandardUrl() {

      $namespaceKeywordDelimiter = '-';
      $actionKeyword = 'action';
      $keywordClassDelimiter = ':';
      $inputDelimiter = '|';
      $keyValueDelimiter = ':';

      $fC = &Singleton::getInstance('Frontcontroller');
      /* @var $fC Frontcontroller */

      foreach ($_REQUEST as $key => $value) {

         if (substr_count($key, $namespaceKeywordDelimiter . $actionKeyword . $keywordClassDelimiter) > 0) {

            // get namespace and class from the REQUEST key
            $actionName = substr($key, strpos($key, $keywordClassDelimiter) + strlen($keywordClassDelimiter));
            $actionNamespace = substr($key, 0, strpos($key, $namespaceKeywordDelimiter));

            // initialize the input params
            $inputParams = array();

            // create param array
            $paramsArray = explode($inputDelimiter, $value);

            $count = count($paramsArray);
            for ($i = 0; $i < $count; $i++) {

               $tmpArray = explode($keyValueDelimiter, $paramsArray[$i]);

               if (isset($tmpArray[0]) && isset($tmpArray[1])
                   && !empty($tmpArray[0]) && !empty($tmpArray[1])
               ) {
                  $inputParams[$tmpArray[0]] = $tmpArray[1];
               }
            }

            // add action to the front controller
            $fC->addAction($actionNamespace, $actionName, $inputParams);
         }
      }
   }

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
   protected function createRequestArray($url) {

      // remove slashes at the beginning
      $url = $this->deleteTrailingSlash($url);

      // create first version of the array
      $requestArray = explode(self::$REWRITE_URL_DELIMITER, strip_tags($url));

      // initialize some vars
      $returnArray = array();
      $x = 0;

      // walk through the new request array and combine the key (offset x) and
      // the value (offset x + 1)
      while ($x <= (count($requestArray) - 1)) {

         if (isset($requestArray[$x + 1])) {
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
   protected function deleteTrailingSlash($url) {
      if (substr($url, 0, 1) == self::$REWRITE_URL_DELIMITER) {
         $url = substr($url, 1);
      }
      return $url;
   }

}
?>