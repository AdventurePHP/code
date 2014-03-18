<?php
namespace APF\tools\link;

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
use APF\core\frontcontroller\AbstractFrontcontrollerAction;
use APF\core\frontcontroller\ActionUrlMapping;
use APF\core\frontcontroller\Frontcontroller;
use APF\core\singleton\Singleton;

/**
 * @package APF\tools\link
 * @class UrlFormatException
 *
 * Represents an exception that indicated illegal urls.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.04.2011<br />
 */
class UrlFormatException extends \Exception {

}

/**
 * @package APF\tools\link
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
   private $anchor;

   /**
    * @public
    *
    * Constructs a url for link generation purposes.
    *
    * @param string $scheme The url's scheme (e.g. http, ftp).
    * @param string $host The url's host (e.g. example.com).
    * @param int|null $port The url's port (e.g. 80, 443).
    * @param string $path The url's path (e.g. /foo/bar).
    * @param array $query An associative array of query parameters.
    * @param string $anchor An optional anchor (e.g. #top).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2011<br />
    */
   public function __construct($scheme, $host, $port, $path, array $query = array(), $anchor = null) {
      $this->scheme = $scheme;
      $this->host = $host;
      $this->port = $port;
      $this->path = $path;
      $this->query = $query;
      $this->anchor = $anchor;
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

   public function getAnchor() {
      return $this->anchor;
   }

   /**
    * @public
    *
    * Let's you query a request parameter.
    *
    * @param string $name The name of the desired parameter.
    * @param string $default The default value to return in case the parameter is not existing.
    *
    * @return string The value of the parameter or null if it doesn't exist.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function getQueryParameter($name, $default = null) {
      return isset($this->query[$name]) ? $this->query[$name] : $default;
   }

   /**
    * @public
    *
    * Let's you inject the scheme of the url.
    *
    * @param string $scheme The url scheme (e.g. http, ftp).
    *
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
    *
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
    * @param int|null $port The url's port (e.g. 80, 443).
    *
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
    *
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
    *
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
    * @param array $query An associative array of the query params to merge.
    *
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
    * Let's you inject the anchor of the url.
    *
    * @param string $anchor The anchor (e.g. #top).
    *
    * @return Url This object for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function &setAnchor($anchor) {
      $this->anchor = $anchor;

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
    *
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
    *
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
               . '" cannot be parsed due to semantic errors!');
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
      if (!isset($parts['fragment'])) {
         $parts['fragment'] = null;
      }

      return new Url($parts['scheme'], $parts['host'], $parts['port'], $parts['path'], self::getQueryParams($parts['query']), $parts['fragment']);
   }

   /**
    * @public
    * @static
    *
    * Creates a url representation from the current request url.
    *
    * @param boolean $absolute True, in case the url should be absolute, false otherwise.
    *
    * @return Url The current url representation.
    * @throws UrlFormatException In case the given string is not a valid url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2011<br />
    * Version 0.2, 09.03.2013 (Now uses standard PHP variables in stead of a Registry value to allow better url input filter manipulation)<br />
    */
   public static function fromCurrent($absolute = false) {

      // construct url from standard PHP variables
      $protocol = ($_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
      $currentUrlString = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

      $url = self::fromString($currentUrlString);
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
    * Creates a url representation from the referring url.
    *
    * @param boolean $absolute True, in case the url should be absolute, false otherwise.
    *
    * @return Url The current url representation.
    * @throws UrlFormatException In case the given referrer is not a valid url.
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
      throw new UrlFormatException('Empty referrer url cannot be used to create a url representation.');
   }

   /**
    * @private
    *
    * Generates a query param array from a given query string.
    *
    * @param string $query The query params string.
    *
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
         // include only param couples
         if (isset($tmp[1])) {
            $params[$tmp[0]] = $tmp[1];
         }
      }

      return $params;
   }

}

/**
 * @package APF\tools\link
 * @class LinkScheme
 *
 * Defines the structure of the APF link scheme implementations. A link scheme
 * represents a kind of url formatter that is used by the <em>LinkGenerator</em>.
 * <p/>
 * Normally, link schemes are implemented together with input filters, that can
 * resolve the link formatting. The APF therefore ships two link scheme implementations
 * that follow the url structure of the <em>ChainedUrlRewritingInputFilter</em>.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2011<br />
 */
interface LinkScheme {

   /**
    * @param Url $url The url to generate.
    *
    * @return string The result url.
    */
   public function formatLink(Url $url);

   /**
    * @param Url $url The url representation.
    * @param string $namespace The action's namespace.
    * @param string $name The action's name
    * @param array $params The action's parameters.
    *
    * @return string The result url.
    */
   public function formatActionLink(Url $url, $namespace, $name, array $params = array());

   public function setEncodeAmpersands($encode);

   public function getEncodeAmpersands();
}

/**
 * @package APF\tools\link
 * @class LinkGenerator
 *
 * The <em>LinkGenerator</em> is a generic link generation tool that can be configured
 * by different link scheme implementations. Normally, configuration is done globally
 * within the bootstrap file, but can be overwritten on each single link generation
 * statement by applying the desired scheme.
 * <p/>
 * By default, the APF ships two link scheme implementations: <em>DefaultLinkScheme</em>
 * for normal urls and the <em>RewriteLinkScheme</em> for rewritten urls following
 * the layout that is resolved by the <em>ChainedStandardInputFilter</em> (<em>DefaultLinkScheme</em>
 * pendant) and the <em>ChainedUrlRewritingInputFilter</em> (<em>RewriteLinkScheme</em> pendant).
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
    *
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
    *
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
 * @package APF\tools\link
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
    *
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
    * Retrieves the current action stack from the front controller.
    *
    * @return AbstractFrontcontrollerAction[] The list of registered actions.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2011<br />
    */
   protected function &getFrontcontrollerActions() {
      return $this->getFrontController()->getActions();
   }

   /**
    * @return Frontcontroller
    */
   protected function &getFrontController() {
      return Singleton::getInstance('APF\core\frontcontroller\Frontcontroller');
   }

   /**
    * @protected
    *
    * Creates a url sub-string that contains all action's encoded information that
    * have the <em>keepInUrl</em> flag set to true.
    * <p/>
    * In case an action has the <em>keepInUrl</em> flag defined with <em>true</em> and
    * you decide to manually add the same action again using <em>LinkScheme::formatActionUrl()</em>
    * the action definition added manually overrides the automatically generated action
    * instruction.
    * <p/>
    * This means: in case an action with namespace <em>APF\tools\media</em> and name
    * <em>streamMedia</em> is on the front controller action stack including parameters
    * <em>foo</em> (=1) and <em>bar</em> (=2) and is added manually by
    * <code>
    * $url = Url::fromCurrent();
    * $link = LinkGenerator::generateActionUrl($url, 'APF\tools\media', 'streamMedia', array('baz' => 1));
    * </code>
    * the resulting url will be <em>...?APF_tools_media-action:streamMedia=baz:1</em> instead of
    * <em>...?APF_tools_media-action:streamMedia=foo:1|bar:2</em>.
    *
    * @param array $query The current list of parameters.
    * @param boolean $urlRewriting True in case url rewriting is activated, false otherwise.
    *
    * @return string All actions' url representations.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.04.2011<br />
    * Version 0.2, 20.08.2013 (Added check for duplicate action generation caused by manual generateActionUrl() calls)<br />
    */
   protected function getActionsUrlRepresentation(array $query, $urlRewriting) {

      // retrieve actions from internal method (to enable testing)
      $actions = & $this->getFrontcontrollerActions();

      $actionUrlRepresentation = array();
      foreach ($actions as $action) {
         /* @var $action AbstractFrontcontrollerAction */
         if ($action->getKeepInUrl() === true) {
            // Only add actions in case they are not yet added within LinkGenerator::generateActionUrl().
            // This is done to avoid duplicate action definition within the generated url. Please note,
            // that this means "last definition wins" - manual definition overrides automatic generation.
            // We can use this mechanism/logic here, because actions are always appended to the regular
            // query. Hence, the given query will contain all manual action definitions before automatic
            // appending takes place.
            $key = $this->formatActionIdentifier($action->getActionNamespace(), $action->getActionName(), $urlRewriting);
            if (!isset($query[$key])) {
               $actionUrlRepresentation[] = $this->getActionUrlRepresentation($action, $urlRewriting);
            }
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
    *
    * @return string The action's url representations.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.04.2011<br />
    */
   protected function getActionUrlRepresentation(AbstractFrontcontrollerAction $action, $urlRewriting) {
      $value = $this->formatActionParameters($action->getInput()->getAttributes(), $urlRewriting);
      $key = $this->formatActionIdentifier($action->getActionNamespace(), $action->getActionName(), $urlRewriting);

      // avoid "=" sign with empty value list
      $actionParamsDelimiter = $urlRewriting === true ? '/' : '=';

      return empty($value) ? $key : $key . $actionParamsDelimiter . $value;
   }

   /**
    * @protected
    *
    * Returns an url identifier that addresses the action described by the applied parameters.
    *
    * @param string $namespace The namespace of the action.
    * @param string $name The name of the action.
    * @param boolean $urlRewriting True for activated url rewriting, false instead.
    *
    * @return string The formatted action identifier.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.04.2011<br />
    */
   protected function formatActionIdentifier($namespace, $name, $urlRewriting) {
      // ID#63: get action URL mapping from front controller and format if appropriate
      $mapping = $this->getActionUrlMapping($namespace, $name);
      if ($mapping !== null) {
         return $mapping->getUrlToken();
      }

      return str_replace('\\', '_', $namespace) . '-action' . ($urlRewriting === true ? '/' : ':') . $name;
   }

   /**
    * @param string $namespace The namespace of the action.
    * @param string $name The name of the action.
    *
    * @return ActionUrlMapping|null The desired action URL mapping or <em>null</em>.
    */
   protected function getActionUrlMapping($namespace, $name) {
      return $this->getFrontController()->getActionUrlMapping($namespace, $name);
   }

   /**
    * @return string[] The list of action URL mapping tokens.
    */
   protected function getActionUrMappingTokens() {
      return $this->getFrontController()->getActionUrlMappingTokens();
   }

   /**
    * @protected
    *
    * Returns all input parameters as a url formatted string.
    *
    * @param array $params The action parameters.
    * @param boolean $urlRewriting True for activated url rewriting, false instead.
    *
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

   /**
    * @protected
    *
    * Safely tests whether the applied value is considered empty or not.
    *
    * @param string $value The value to check.
    *
    * @return bool True in case the value is empty, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.10.2012 (Bug-fix: special empty check now respects "0" values)<br />
    */
   protected function isValueEmpty($value) {
      return empty($value) && strval($value) !== '0';
   }

   /**
    * @protected
    *
    * Appends an anchor if present within the url representation applied to a LinkScheme
    * implementation.
    *
    * @param string $urlString The url constructed by the given url representation.
    * @param Url $url The current url representation potentially including the anchor.
    *
    * @return string The final url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.11.2013<br />
    */
   protected function appendAnchor($urlString, Url $url) {
      // append anchor only if desired
      $anchor = $url->getAnchor();
      if (!empty($anchor)) {
         $urlString .= '#' . $anchor;
      }

      return $urlString;
   }

   /**
    * @protected
    *
    * Removes action instructions from the query of a Url instance. Only works for
    * normal URLs.
    *
    * @param Url $url The url to clean up.
    *
    * @return Url The url instance without action instructions-.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 14.03.2014<br />
    */
   protected function removeActionInstructions(Url $url) {

      $mappings = $this->getActionUrMappingTokens();

      $query = $url->getQuery();

      // remove actions from query (either explicitly expressed by the action keyword or by url token)
      foreach ($query as $name => $value) {
         if (strpos($name, '-action') !== false || in_array($name, $mappings)) {
            unset($query[$name]);
         }
      }

      return $url->setQuery($query);
   }

}

/**
 * @package APF\tools\link
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
      // remove existing action instructions since they should only appear with keepInUrl=true
      return $this->formatLinkInternal($this->removeActionInstructions($url));
   }

   /**
    * @param Url $url The URL instance to create a formatted link from.
    *
    * @return string The formatted URL.
    */
   protected function formatLinkInternal(Url $url) {

      $resultUrl = $this->getFormattedBaseUrl($url);

      $path = $url->getPath();
      if (!empty($path)) {
         $resultUrl .= $path;
      }

      // get URL mappings and try to resolve mapped actions
      $mappings = $this->getActionUrMappingTokens();

      $query = $url->getQuery();
      $queryString = '';

      foreach ($query as $name => $value) {
         if ($this->isValueEmpty($value)) {
            // include actions that may have empty values
            if (strpos($name, '-action') !== false || in_array($name, $mappings)) {
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

      // apply query to detect duplicate action definitions
      $actions = $this->getActionsUrlRepresentation($query, false);
      if (!empty($actions)) {
         $resultUrl .= strpos($resultUrl, '?') === false ? '?' : '&';
         $resultUrl .= $actions;
      }

      $resultUrl = $this->appendAnchor($resultUrl, $url);

      // encode ampersands if desired
      if ($this->getEncodeAmpersands()) {
         return str_replace('&', '&amp;', $resultUrl);
      }

      return $resultUrl;
   }

   public function formatActionLink(Url $url, $namespace, $name, array $params = array()) {
      $url = $this->removeActionInstructions($url);

      return $this->formatLinkInternal(
            $url->setQueryParameter(
                  $this->formatActionIdentifier($namespace, $name, false),
                  $this->formatActionParameters($params, false)
            ));
   }

}

/**
 * @package APF\tools\link
 * @class RewriteLinkScheme
 *
 * This link scheme implementation is intended to handle rewritten urls
 * that have a generic path structure encoding params and their values.
 * <p/>
 * Ths link scheme is aware of front controller actions to be included into the
 * url by action configuration.
 * <p/>
 * Please note, that this link scheme ignores the ampersand encoding option,
 * since it makes no sense here.
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

      $query = $url->getQuery();
      if (count($query) > 0) {

         // get URL mappings and try to resolve mapped actions
         $mappings = $this->getActionUrMappingTokens();

         foreach ($query as $name => $value) {
            // allow empty params that are action definitions to not
            // exclude actions with no params!
            if (!$this->isValueEmpty($value)
                  || ($this->isValueEmpty($value) && strpos($name, '-action') !== false)
                  || ($this->isValueEmpty($value) && in_array($name, $mappings) !== false)
            ) {
               if (strpos($name, '-action') === false && in_array($name, $mappings) === false) {
                  $resultUrl .= '/' . $name . '/' . $value;
               } else {
                  // action blocks must be separated with group indicator
                  // to be able to parse the parameters
                  $resultUrl .= self::REWRITE_PARAM_TO_ACTION_DELIMITER . $name;

                  // check whether value is empty and append action only
                  if (!$this->isValueEmpty($value)) {
                     $resultUrl .= '/' . $value;
                  }
               }
            }
         }
      }

      // apply query to detect duplicate action definitions
      $actions = $this->getActionsUrlRepresentation($query, true);
      if (!empty($actions)) {
         $resultUrl .= self::REWRITE_PARAM_TO_ACTION_DELIMITER . $actions;
      }

      $resultUrl = $this->appendAnchor($resultUrl, $url);

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
