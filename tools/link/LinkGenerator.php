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
 * @package tools::link
 * @class UrlFormatException
 *
 * Represents an exception that indicated illegal urls.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.04.2011<br />
 */
class UrlFormatException extends Exception {
}

/**
 * @package tools::link
 * @class Url
 *
 * This class represents a url designed to generate related urls using
 * the APF's link scheme implementations.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2011<br />
 */
final class Url {
   const DEFAULT_HTTP_PORT = '80';
   const DEFAULT_HTTPS_PORT = '443';

   private $scheme;
   private $host;
   private $port;
   private $path;
   private $query = array();

   /**
    * @public
    *
    * Constructs a url for link generation purposes.
    *
    * @param string $scheme The url's scheme (e.g. http, ftp).
    * @param string $host The url's host (e.g. example.com).
    * @param int $port The url's port (e.g. 80, 443).
    * @param string $path The url's path (e.g. /foo/bar).
    * @param array $query An associative array of query parameters.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2011<br />
    */
   public function __construct($scheme, $host, $port, $path, array $query = array()) {
      $this->scheme = $scheme;
      $this->host = $host;
      $this->port = $port;
      $this->path = $path;
      $this->query = $query;
   }

   public function getScheme() {
      return $this->scheme;
   }

   public function getHost() {
      return $this->host;
   }

   public function getPort() {
      return $this->port;
   }

   public function getPath() {
      return $this->path;
   }

   /**
    * @public
    *
    * Returns the list of registered query parameters.
    *
    * @return array The query parameters of the url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function getQuery() {
      return $this->query;
   }

   /**
    * @public
    *
    * Let's you query a request parameter.
    *
    * @param string $name The name of the desired parameter.
    * @return string The value of the parameter or null if it doesn't exist.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function getQueryParameter($name) {
      return isset($this->query[$name]) ? $this->query[$name] : null;
   }

   /**
    * @public
    *
    * Let's you inject the scheme of the url.
    *
    * @param string $scheme The url scheme (e.g. http, ftp).
    * @return Url This object for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function &setScheme($scheme) {
      $this->scheme = $scheme;
      return $this;
   }

   /**
    * @public
    *
    * Let's you inject the host of the url.
    *
    * @param string $host The url' host (e.g. example.com).
    * @return Url This object for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function &setHost($host) {
      $this->host = $host;
      return $this;
   }

   /**
    * @public
    *
    * Let's you inject the port of the url.
    *
    * @param int $port The url's port (e.g. 80, 443).
    * @return Url This object for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function &setPort($port) {
      $this->port = $port;
      return $this;
   }

   /**
    * @public
    *
    * Let's you inject the path of the url.
    *
    * @param string $path The url's path (e.g. /foo/bar).
    * @return Url This object for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function &setPath($path) {
      $this->path = $path;
      return $this;
   }

   /**
    * @public
    *
    * Let's you inject the desired amount of request parameters.
    *
    * @param array $query The query parameters to inject.
    * @return Url This object for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function &setQuery(array $query) {
      $this->query = $query;
      return $this;
   }

   /**
    * @public
    *
    * This method let's you merge a list of parameters into the current url's
    * list. Setting a query parameter's value to <em>null</em> indicates to
    * delete the parameter within the LinkScheme implementation.
    *
    * @param array $query An assoziative array of the query params to merge.
    * @return Url This object for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function &mergeQuery(array $query) {
      foreach ($query as $name => $value) {
         $this->query[$name] = $value;
      }
      return $this;
   }

   /**
    * @public
    *
    * This method resets the list of parameters.
    *
    * @return Url This object for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.06.2011<br />
    */
   public function resetQuery() {
      $this->query = array();
      return $this;
   }

   /**
    * @public
    *
    * This method can be used to set a query parameter. Setting it's value
    * to <em>null</em> indicates to delete the parameter within the
    * LinkScheme implementation.
    *
    * @param string $name The name of the parameter.
    * @param string $value The value of the parameter.
    * @return Url This object for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function &setQueryParameter($name, $value) {
      $this->query[$name] = $value;
      return $this;
   }

   /**
    * @public
    * @static
    *
    * Let's you construct a url applying a string.
    *
    * @param string $url The url to parse.
    * @return Url The resulting url.
    * @throws UrlFormatException In case the given string is not a valid url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2011<br />
    */
   public static function fromString($url) {

      // the ugly "@" is only introduced to convert the E_WARNING into an exception
      $parts = @parse_url($url);
      if ($parts === false || !is_array($parts)) {
         throw new UrlFormatException('The given url "' . $url
                                      . '" cannot be parsed due to semantical errors!');
      }

      // resolve missing parameters
      if (!isset($parts['scheme'])) {
         $parts['scheme'] = null;
      }
      if (!isset($parts['host'])) {
         $parts['host'] = null;
      }
      if (!isset($parts['port'])) {
         $parts['port'] = null;
      }
      if (!isset($parts['path'])) {
         $parts['path'] = null;
      }
      if (!isset($parts['query'])) {
         $parts['query'] = null;
      }

      return new Url($parts['scheme'], $parts['host'], $parts['port'], $parts['path'], self::getQueryParams($parts['query']));
   }

   /**
    * @public
    * @static
    *
    * Creates a url representation from the current request url.
    *
    * @param boolean $absolute True, in case the url should be absolute, false otherwise.
    * @return Url The current url representation.
    * @throws UrlFormatException In case the given string is not a valid url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2011<br />
    */
   public static function fromCurrent($absolute = false) {
      $url = self::fromString(Registry::retrieve('apf::core', 'CurrentRequestURL'));
      if ($absolute === false) {
         $url->setScheme(null);
         $url->setHost(null);
         $url->setPort(null);
      }
      return $url;
   }

   /**
    * @public
    * @static
    *
    * Creates a url representation from the refering url.
    *
    * @param boolean $absolute True, in case the url should be absolute, false otherwise.
    * @return Url The current url representation.
    * @throws UrlFormatException In case the given referer is not a valid url.
    *
    * @author dave
    * @version
    * Version 0.1, 07.09.2011<br />
    */
   public static function fromReferer($absolute = false) {
      if (isset($_SERVER['HTTP_REFERER'])) {
         $url = self::fromString($_SERVER['HTTP_REFERER']);
         if ($absolute === false) {
            $url->setScheme(null);
            $url->setHost(null);
            $url->setPort(null);
         }
         return $url;
      }
      throw new UrlFormatException('Empty referer url cannot be used to create a url representation.');
   }

   /**
    * @private
    *
    * Generates a query param array from a given query string.
    *
    * @param string $query The query params string.
    * @return array The query params array.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2011<br />
    */
   private static function getQueryParams($query) {

      // reverse resolve encoded ampersands
      $query = str_replace('&amp;', '&', $query);

      // in case of empty query strings, return empty param list
      if (empty($query)) {
         return array();
      }

      $parts = explode('&', $query);
      $params = array();
      foreach ($parts as $part) {
         $tmp = explode('=', $part);
         // include only param couples and ensure to exclude action definitions
         if (isset($tmp[1]) && strpos($tmp[0], '-action') === false) {
            $params[$tmp[0]] = $tmp[1];
         }
      }
      return $params;
   }

}

/**
 * @package tools::link
 * @class LinkScheme
 *
 * Defines the structure of the APF link scheme implementations. A link scheme
 * represents a kind of url formatter that is used by the <em>LinkGenerator</em>.
 * <p/>
 * Normally, link schemes are implemented together with input filters, that can
 * resolve the link formatting. The APF therefore ships two link scheme implementations
 * that follow the url structure of the <em>ChainedGenericInputFilter</em>.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2011<br />
 */
interface LinkScheme {

   /**
    * @param Url $url The url to generate.
    * @return string The result url.
    */
   function formatLink(Url $url);

   /**
    * @param Url $url The url representation.
    * @param string $namespace The action's namespace.
    * @param string $name The action's name
    * @param array $params The action's parameters.
    * @return string The result url.
    */
   function formatActionLink(Url $url, $namespace, $name, array $params = array());

   function setEncodeAmpersands($encode);

   function getEncodeAmpersands();
}

/**
 * @package tools::link
 * @class LinkGenerator
 *
 * The <em>LinkGenerator</em> is a generic link generation tool that can be configured
 * by different link scheme implementations. Normally, configuration is done globally
 * within the bootstrap file, but can be overwritten on each single link generation
 * statement by applying the desired scheme.
 * <p/>
 * By default, the APF shipps two link scheme implementations: <em>DefaultLinkScheme</em>
 * for normal urls and the <em>RewriteLinkScheme</em> for rewritten urls following
 * the layout that is resolved by the <em>ChainedGenericInputFilter</em>.
 * <p/>
 * This component provides facilities to generate &quot;normal&quot; urls as well as
 * front controller action urls that directly address such actions. The latter ones
 * are often used for resource integration (e.g. dynamic images). In case you need
 * actions to be contained within the url, use the <em>keepInUrl</em> flag within
 * your action implementation instead. This property is respected by the shipped
 * link generation schemes mentioned above.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2011<br />
 */
final class LinkGenerator {

   /**
    * @var LinkScheme The link scheme to generate the links with.
    */
   private static $LINK_SCHEME;

   private function __construct() {
   }

   public static function setLinkScheme(LinkScheme $linkScheme) {
      self::$LINK_SCHEME = $linkScheme;
   }

   /**
    * @public
    *
    * Let's you retrieve the current link scheme, that has been configured
    * for global usage within the bootstrap file.
    *
    * @return LinkScheme The current global link scheme.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public static function getLinkScheme() {
      return self::$LINK_SCHEME;
   }

   /**
    * @public
    *
    * Let's you retrieve a clone of the current link scheme for further
    * configuration and explicit use.
    *
    * @return LinkScheme A clone of the current link scheme.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.04.2011<br />
    */
   public static function cloneLinkScheme() {
      return clone self::$LINK_SCHEME;
   }

   /**
    * @param Url $url The url representation.
    * @param LinkScheme $scheme An optional link scheme to overwrite the global scheme.
    * @return string The formatted url.
    */
   public static function generateUrl(Url $url, LinkScheme $scheme = null) {
      if ($scheme === null) {
         return self::$LINK_SCHEME->formatLink($url);
      }
      return $scheme->formatLink($url);
   }

   /**
    * @param Url $url The url representation.
    * @param string $namespace The action's namespace.
    * @param string $name The action's name
    * @param array $params The action's parameters.
    * @param LinkScheme $scheme An optional link scheme to overwrite the global scheme.
    * @return string the formatted url.
    */
   public static function generateActionUrl(Url $url, $namespace, $name, array $params = array(), LinkScheme $scheme = null) {
      if ($scheme === null) {
         return self::$LINK_SCHEME->formatActionLink($url, $namespace, $name, $params);
      }
      return $scheme->formatActionLink($url, $namespace, $name, $params);
   }

}

/**
 * @package tools::link
 * @class BasicLinkScheme
 *
 * Implements basic functionality used by different link scheme implementations.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 07.04.2011<br />
 */
abstract class BasicLinkScheme {
   const DEFAULT_PARAM_TO_ACTION_DELIMITER = '&';
   const REWRITE_PARAM_TO_ACTION_DELIMITER = '/~/';

   /**
    * @var boolean Indicates whether to encode ampersands or not.
    */
   private $encodeAmpersands;

   public function __construct($encodeAmpersands = true) {
      $this->setEncodeAmpersands($encodeAmpersands);
   }

   public function setEncodeAmpersands($encodeAmpersands) {
      $this->encodeAmpersands = $encodeAmpersands;
   }

   public function getEncodeAmpersands() {
      return $this->encodeAmpersands;
   }

   /**
    * @protected
    *
    * Creates a base url string including scheme, host and port in case
    * it differs from the default ports.
    *
    * @param Url $url The current url representation.
    * @return string The formatted base url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.04.2011<br />
    */
   protected function getFormattedBaseUrl(Url $url) {

      $baseUrl = '';

      $scheme = $url->getScheme();
      if (!empty($scheme)) {
         $baseUrl .= $scheme . '://';
      }

      $host = $url->getHost();
      if (!empty($host)) {
         $baseUrl .= $host;
      }

      $port = $url->getPort();
      if (!empty($port) && $port !== Url::DEFAULT_HTTP_PORT && $port !== Url::DEFAULT_HTTPS_PORT) {
         $baseUrl .= ':' . $port;
      }

      return $baseUrl;
   }

   /**
    * @protected
    *
    * Creates a url sub-string that contains all action's encoded information that
    * have the <em>keepInUrl</em> flag set to true.
    *
    * @param boolean $urlRewriting True in case url rewriting is activated, false otherwise.
    * @return string All actions' url representations.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.04.2011<br />
    */
   protected function getActionsUrlRepresentation($urlRewriting) {

      $fC = &Singleton::getInstance('Frontcontroller');
      /* @var $fC Frontcontroller */
      $actions = &$fC->getActions();

      $actionUrlRepresentation = array();
      foreach ($actions as $action) {
         /* @var $action AbstractFrontcontrollerAction */
         if ($action->getKeepInUrl() === true) {
            $actionUrlRepresentation[] = $this->getActionUrlRepresentation($action, $urlRewriting);
         }
      }

      if (count($actionUrlRepresentation) > 0) {
         $delimiter = $urlRewriting === true ? self::REWRITE_PARAM_TO_ACTION_DELIMITER
               : self::DEFAULT_PARAM_TO_ACTION_DELIMITER;
         return implode($delimiter, $actionUrlRepresentation);
      }
      return '';
   }

   /**
    * @protected
    *
    * Creates a url sub-string that contains one action's encoded information.
    *
    * @param AbstractFrontcontrollerAction $action The front controller action.
    * @param boolean $urlRewriting True in case url rewriting is activated, false otherwise.
    * @return string The action's url representations.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.04.2011<br />
    */
   protected function getActionUrlRepresentation(AbstractFrontcontrollerAction $action, $urlRewriting) {
      $actionParamsDelimiter = $urlRewriting === true ? '/' : '=';
      $value = $this->formatActionParameters($action->getInput()->getAttributes(), $urlRewriting);
      $key = $this->formatActionIdentifier($action->getActionNamespace(), $action->getActionName(), $urlRewriting);
      return $key . $actionParamsDelimiter . $value;
   }

   /**
    * @protected
    *
    * Returns an url identifier that adresses the action described by the applied parameters.
    *
    * @param string $namespace The namespace of the action.
    * @param string $name The name of the action.
    * @param boolean $urlRewriting True for activated url rewriting, false instead.
    * @return string The formatted action identifier.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.04.2011<br />
    */
   protected function formatActionIdentifier($namespace, $name, $urlRewriting) {
      return str_replace('::', '_', $namespace) . '-action' . ($urlRewriting === true ? '/' : ':') . $name;
   }

   /**
    * @protected
    *
    * Returns all input parameters as a url formatted string.
    *
    * @param array $params The action parameters.
    * @param boolean $urlRewriting True for activated url rewriting, false instead.
    * @return string The url formatted attributes string.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.02.2007<br />
    * Version 0.2, 08.11.2007 (Corrected error with empty input object)<br />
    * Version 0.3, 21.06.2008 (Removed APPS__URL_REWRITING and introduced the Registry instead)<br />
    * Version 0.4, 25.03.2011 (Renamed to <em>getParameterURLRepresentation()</em> due to page controller refactoring)<br />
    * Version 0.5, 08.04.2011 (Moved from FrontcontrollerInput to the link scheme implementation for consistency)<br />
    */
   protected function formatActionParameters(array $params, $urlRewriting) {

      $groupDelimiter = $urlRewriting === true ? '/' : '|';
      $keyValueDelimiter = $urlRewriting === true ? '/' : ':';

      // fill consolidated attributes array
      $groups = array();

      if (count($params) > 0) {
         foreach ($params as $key => $value) {
            $groups[] = $key . $keyValueDelimiter . $value;
         }
      }

      return implode($groupDelimiter, $groups);
   }

}

/**
 * @package tools::link
 * @class DefaultLinkScheme
 *
 * This link scheme implementation is intended to handle &quot;normal&quot; urls
 * that have a simple path followed by various request parameters.
 * <p/>
 * This link scheme is aware of front controller actions to be included into the
 * url by action configuration.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.04.2011<br />
 */
class DefaultLinkScheme extends BasicLinkScheme implements LinkScheme {

   public function formatLink(Url $url) {

      $resultUrl = $this->getFormattedBaseUrl($url);

      $path = $url->getPath();
      if (!empty($path)) {
         $resultUrl .= $path;
      }

      $query = $url->getQuery();
      $queryString = '';
      foreach ($query as $name => $value) {
         if (empty($value)) {
            // include actions that may have empty values
            if (strpos($name, '-action') !== false) {
               if (!empty($queryString)) {
                  $queryString .= '&';
               }
               $queryString .= $name;
            }
         } else {
            if (!empty($queryString)) {
               $queryString .= '&';
            }
            $queryString .= $name . '=' . $value;
         }
      }

      if (!empty($queryString)) {
         $resultUrl .= '?' . $queryString;
      }

      // add fc actions
      $actions = $this->getActionsUrlRepresentation(false);
      if (!empty($actions)) {
         $resultUrl .= strpos($resultUrl, '?') === false ? '?' : '&';
         $resultUrl .= $actions;
      }

      // encode ampersands if desired
      if ($this->getEncodeAmpersands()) {
         return str_replace('&', '&amp;', $resultUrl);
      }

      return $resultUrl;
   }

   public function formatActionLink(Url $url, $namespace, $name, array $params = array()) {
      return $this->formatLink(
         $url->setQueryParameter(
            $this->formatActionIdentifier($namespace, $name, false),
            $this->formatActionParameters($params, false)
         ));
   }

}

/**
 * @package tools::link
 * @class RewriteLinkScheme
 *
 * This link scheme implementation is intended to handle rewritten urls
 * that have a generic path structure encoding params and their values.
 * <p/>
 * Ths link scheme is aware of front controller actions to be included into the
 * url by action configuration.
 * <p/>
 * Please note, that this link scheme ignores the ampersand encoding option,
 * since it makes no sence here.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.04.2011<br />
 */
class RewriteLinkScheme extends BasicLinkScheme implements LinkScheme {

   public function formatLink(Url $url) {

      // save and reset query to save variable order
      $query = $url->getQuery();
      $url->setQuery(array());

      // extract path to separate params and remove actions
      $parts = explode(self::REWRITE_PARAM_TO_ACTION_DELIMITER, $url->getPath());

      foreach ($parts as $part) {

         // only extract "normal" parameters, avoid actions
         if (strpos($part, '-action') === false) {

            $paths = explode('/', strip_tags($part));
            array_shift($paths);

            // create key => value pairs from the current request
            $x = 0;
            while ($x <= (count($paths) - 1)) {

               if (isset($paths[$x + 1])) {
                  $url->setQueryParameter($paths[$x], $paths[$x + 1]);
               }

               // increment by 2, because the next offset is the key!
               $x = $x + 2;
            }
         }
      }

      // reset the path to not have duplicate path due to generic param generation
      $url->setPath(null);

      // merge query now to overwrite values already contained in the url
      $url->mergeQuery($query);

      $resultUrl = $this->getFormattedBaseUrl($url);

      $path = $url->getPath();
      if (!empty($path)) {
         $resultUrl .= $path;
      }

      $query = $url->getQuery();
      if (count($query) > 0) {
         foreach ($query as $name => $value) {
            // allow empty params that are action definitions to not
            // exclude actions with no params!
            if (!empty($value) || (empty($value) && strpos($name, '-action') !== false)) {
               if (strpos($name, '-action') === false) {
                  $resultUrl .= '/' . $name . '/' . $value;
               } else {
                  // action blocks must be separated with group indicator
                  // to be able to parse the parameters
                  $resultUrl .= self::REWRITE_PARAM_TO_ACTION_DELIMITER . $name . '/' . $value;
               }
            }
         }
      }

      // add fc actions
      $actions = $this->getActionsUrlRepresentation(true);
      if (!empty($actions)) {
         $resultUrl .= self::REWRITE_PARAM_TO_ACTION_DELIMITER . $actions;
      }

      return $resultUrl;
   }

   public function formatActionLink(Url $url, $namespace, $name, array $params = array()) {
      return $this->formatLink(
         $url->setQueryParameter(
            $this->formatActionIdentifier($namespace, $name, true),
            $this->formatActionParameters($params, true)
         ));
   }

}

// set up link scheme concerning the url rewriting configuration.
// this can be done here, since import() ensures, that this file 
// is only included once!
if (Registry::retrieve('apf::core', 'URLRewriting', false)) {
   LinkGenerator::setLinkScheme(new RewriteLinkScheme());
} else {
   LinkGenerator::setLinkScheme(new DefaultLinkScheme());
}
?>