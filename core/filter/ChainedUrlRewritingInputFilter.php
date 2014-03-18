<?php
namespace APF\core\filter;

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
use APF\core\benchmark\BenchmarkTimer;
use APF\core\singleton\Singleton;

/**
 * @package APF\core\filter
 * @class ChainedUrlRewritingInputFilter
 *
 * Implements an input filter that resolves the URL layout of the front and page
 * controller with respect to an active url rewriting setup.
 * <p/>
 * The APF url layout includes generic parameter mapping and any number of front
 * controller actions encoded into the url. Rewritten urls can define any number
 * of slash-separated params (e.g. foo/bar) that is translated to the $_REQUEST
 * and $_GET super-global. Further, front controller actions separated from normal
 * parameters are analyzed and applied to the front controller as action to execute.
 * <p/>
 * Since the front controller url layout resolving mechanism includes the page
 * controller behaviour, release 1.14 ships only one filter.
 * <p/>
 * In order to create your own url layout resolver, implement the
 * <em>ChainedContentFilter</em> interface and add it to the reset filter
 * chain/prepend it. Details and examples can be found within the manual.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.03.2011 (Initial migration from 1.13 concept)<br />
 * Version 0.2, 09.04.2013 (Split original class into two input filter that either resolve normal or a rewrite url format (this one))<br />
 */
class ChainedUrlRewritingInputFilter extends ChainedStandardInputFilter implements ChainedContentFilter {

   /**
    * @var string Defines the url parameter that is passed the
    *             current request url by an apache rewrite rule.
    */
   protected static $REWRITE_QUERY_PARAM = 'apf-rewritten-query';

   /**
    * @var string Defines the global URL rewriting delimiter.
    */
   protected static $REWRITE_URL_DELIMITER = '/';

   /**
    * @var string Delimiter between params and action strings.
    */
   protected static $ACTION_TO_PARAM_DELIMITER = '/~/';

   public function filter(FilterChain &$chain, $input = null) {

      /* @var $t BenchmarkTimer */
      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');

      $id = get_class($this);
      $t->start($id);

      // extract the session id from $_REQUEST if existent
      $sessionId = (string) '';
      $sessionName = ini_get('session.name');

      if (isset($_REQUEST[$sessionName])) {
         $sessionId = $_REQUEST[$sessionName];
      }

      // initialize param to analyze
      $query = (string) '';
      if (isset($_REQUEST[self::$REWRITE_QUERY_PARAM])) {
         $query = $_REQUEST[self::$REWRITE_QUERY_PARAM];
      }

      // delete the rewrite param indicator
      unset($_REQUEST[self::$REWRITE_QUERY_PARAM]);
      unset($_GET[self::$REWRITE_QUERY_PARAM]);

      // ID#63: re-map action instructions according to registered aliases
      $fC = $this->getFrontcontroller();
      $tokens = $fC->getActionUrlMappingTokens();

      // re-map action urls
      foreach ($tokens as $token) {
         if (strpos($query, '/' . $token . '/') !== false) {
            $mapping = $fC->getActionUrlMapping($token);
            $query = str_replace(
                  '/' . $token . '/',
                  '/' . str_replace('\\', '_', $mapping->getNamespace()) . '-action/' . $mapping->getName() . '/',
                  $query
            );
         }
      }

      // extract actions from the request url, in case the action keyword or the action
      // delimiter is present in url.
      if (substr_count($query, self::$ACTION_TO_PARAM_DELIMITER) > 0 || substr_count($query, self::$FC_ACTION_KEYWORD . '/') > 0) {

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

      // re-initialize GET params to support e.g. form submission
      $_GET = $_REQUEST;

      // re-add POST params
      $_REQUEST = array_merge($_REQUEST, $_POST);

      // add session id to the request again
      if (!empty($sessionId)) {
         $_REQUEST[$sessionName] = $sessionId;
      }

      $t->stop($id);

      // delegate further filtering to the applied chain
      $chain->filter($input);
   }

   /**
    * @protected
    *
    * Creates a request array out of a slash-separated url string.
    *
    * @param string $url URL string.
    *
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
    *
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
