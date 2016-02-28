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
namespace APF\core\filter;

use APF\core\benchmark\BenchmarkTimer;
use APF\core\singleton\Singleton;

/**
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
    * Defines the url parameter that is passed the current request url by an apache rewrite rule.
    *
    * @var string $REWRITE_QUERY_PARAM
    */
   protected static $REWRITE_QUERY_PARAM = 'apf-rewritten-query';

   /**
    * Defines the global URL rewriting delimiter.
    *
    * @var string $REWRITE_URL_DELIMITER
    */
   protected static $REWRITE_URL_DELIMITER = '/';

   /**
    * Delimiter between params and action strings.
    *
    * @var string $ACTION_TO_PARAM_DELIMITER
    */
   protected static $ACTION_TO_PARAM_DELIMITER = '/~/';

   public function filter(FilterChain &$chain, $input = null) {

      /* @var $t BenchmarkTimer */
      $t = Singleton::getInstance(BenchmarkTimer::class);

      $id = get_class($this);
      $t->start($id);

      $request = $this->getRequest();

      // extract the session id from $_REQUEST if existent to re-add it after filtering
      $sessionId = $request->getSessionId();

      // initialize param to analyze
      $query = $request->getParameter(self::$REWRITE_QUERY_PARAM, '');

      // delete the rewrite param indicator
      $request->deleteParameter(self::$REWRITE_QUERY_PARAM);

      // reset request but save POST data
      $postData = $request->getPostParameters();
      $request->resetParameters();

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
         } else if (substr($query, -(strlen($token) + 1)) == '/' . $token) {
            // URL mapping appears at the end of the query and/or is the only part of it
            $mapping = $fC->getActionUrlMapping($token);
            $query = str_replace(
                  '/' . $token,
                  '/' . str_replace('\\', '_', $mapping->getNamespace()) . '-action/' . $mapping->getName(),
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

                  $actionParamsArray = [];

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
               $request->setParameters(array_merge($request->getParameters(), $paramArray));
            }
         }

      } else {
         // do page controller-style rewriting!
         $paramArray = $this->createRequestArray($query);
         $request->setParameters(array_merge($request->getParameters(), $paramArray));
      }

      // re-initialize GET params to support e.g. form submission
      $request->setGetParameters($request->getParameters());

      // re-add POST params
      $request->setParameters(array_merge($request->getParameters(), $postData));
      $request->setPostParameters($postData);

      // add session id to the request again
      if (!empty($sessionId)) {
         $request->setParameter($request->getSessionName(), $sessionId);
      }

      $t->stop($id);

      // delegate further filtering to the applied chain
      $chain->filter($input);
   }

   /**
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
      $returnArray = [];
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
