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
namespace APF\core\http;

use APF\tools\link\Url;
use APF\tools\link\UrlFormatException;

/**
 * Implementation of the Request interface.
 * <p/>
 * Includes several convenience methods to ease application implementation.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.09.2014<br />
 */
class RequestImpl implements Request {

   public function getVersion() {
      if (!isset($_SERVER['SERVER_PROTOCOL'])) {
         return null;
      }
      $parts = explode('/', $_SERVER['SERVER_PROTOCOL']);

      return isset($parts[1]) ? trim($parts[1]) : null;
   }

   /**
    * @param string $name The name of the URL parameter to get.
    * @param string $default The default value to return in case the parameter is not present (default: <em>null</em>).
    * @param string $type The request parameter type.
    *
    * @return string The value of the requested parameter.
    */
   protected function getGenericParameter($name, $default, $type) {
      $lookupTable = $GLOBALS['_' . $type];

      return isset($lookupTable[$name])
      // avoid issues with "0" values being skipped due to empty() check
      // TODO eventually switch to array_key_exists() instead of empty() as an existing offset with value "null" will be considered empty (UnitTests necessary).
      && (!empty($lookupTable[$name]) || (string) $lookupTable[$name] === '0')
            ? $lookupTable[$name]
            : $default;
   }

   public function getParameter($name, $default = null) {
      return $this->getGenericParameter($name, $default, self::USE_REQUEST_PARAMS);
   }

   /**
    * Checks whether or not the applied parameter is contained in the current request.
    *
    * @param string $name The name of the parameter to check.
    *
    * @return bool <em>True</em> in case the given parameter is contained in the current request, <em>false</em> otherwise.
    */
   public function hasParameter($name) {
      return $this->getParameter($name) !== null;
   }

   public function getGetParameter($name, $default = null) {
      return $this->getGenericParameter($name, $default, self::USE_GET_PARAMS);
   }

   /**
    * Checks whether or not the applied parameter is contained in the current GET request.
    *
    * @param string $name The name of the parameter to check.
    *
    * @return bool <em>True</em> in case the given parameter is contained in the current GET request, <em>false</em> otherwise.
    */
   public function hasGetParameter($name) {
      return $this->getGetParameter($name) !== null;
   }

   public function getPostParameter($name, $default = null) {
      return $this->getGenericParameter($name, $default, self::USE_POST_PARAMS);
   }

   /**
    * Checks whether or not the applied parameter is contained in the current POST request.
    *
    * @param string $name The name of the parameter to check.
    *
    * @return bool <em>True</em> in case the given parameter is contained in the current POST request, <em>false</em> otherwise.
    */
   public function hasPostParameter($name) {
      return $this->getPostParameter($name) !== null;
   }

   public function getSession($name) {
      return new Session($name);
   }

   public function getSessionId() {
      return $this->getParameter($this->getSessionName());
   }

   /**
    * Returns the name of the session configured for the current server instance.
    *
    * @return string The name of the current session.
    */
   public function getSessionName() {
      return ini_get('session.name');
   }

   /**
    * @param string $type The request parameter type.
    *
    * @return array The request parameters of the given type.
    */
   protected function getGenericParameters($type) {
      return is_array($GLOBALS['_' . $type]) ? $GLOBALS['_' . $type] : array();
   }

   public function getParameters() {
      return $this->getGenericParameters(self::USE_REQUEST_PARAMS);
   }

   public function getGetParameters() {
      return $this->getGenericParameters(self::USE_GET_PARAMS);
   }

   public function getPostParameters() {
      return $this->getGenericParameters(self::USE_POST_PARAMS);
   }

   public function setParameter($name, $value) {
      $_REQUEST[$name] = $value;

      return $this;
   }

   public function setGetParameter($name, $value) {
      $_GET[$name] = $value;
      $_REQUEST[$name] = $value;

      return $this;
   }

   public function setPostParameter($name, $value) {
      $_POST[$name] = $value;
      $_REQUEST[$name] = $value;

      return $this;
   }

   /**
    * Convenience method to set a list of request parameters. Overwrites existing values without further notice.
    *
    * @param array $parameters An associative array of names with their corresponding values to set.
    *
    * @return $this This instance for further usage.
    */
   public function setParameters(array $parameters) {
      foreach ($parameters as $name => $value) {
         $this->setParameter($name, $value);
      }

      return $this;
   }

   /**
    * Convenience method to set a list of GET parameters. Overwrites existing values without further notice.
    *
    * @param array $parameters An associative array of names with their corresponding values to set.
    *
    * @return $this This instance for further usage.
    */
   public function setGetParameters(array $parameters) {
      foreach ($parameters as $name => $value) {
         $this->setGetParameter($name, $value);
      }

      return $this;
   }

   /**
    * Convenience method to set a list of POST parameters. Overwrites existing values without further notice.
    *
    * @param array $parameters An associative array of names with their corresponding values to set.
    *
    * @return $this This instance for further usage.
    */
   public function setPostParameters(array $parameters) {
      foreach ($parameters as $name => $value) {
         $this->setPostParameter($name, $value);
      }

      return $this;
   }

   public function deleteParameter($name) {
      unset($_GET[$name]);
      unset($_POST[$name]);
      unset($_REQUEST[$name]);

      return $this;
   }

   public function deleteGetParameter($name) {
      if (isset($_GET[$name])) {
         unset($_GET[$name]);
         unset($_REQUEST[$name]);
      }

      return $this;
   }

   public function deletePostParameter($name) {
      if (isset($_POST[$name])) {
         unset($_POST[$name]);
         unset($_REQUEST[$name]);
      }

      return $this;
   }

   /**
    * Convenience method to delete a list of request parameters.
    *
    * @param array $names A list of parameter names to delete.
    *
    * @return $this This instance for further usage.
    */
   public function deleteParameters(array $names) {
      foreach ($names as $name) {
         $this->deleteParameter($name);
      }

      return $this;
   }

   /**
    * Convenience method to delete a list of GET parameters.
    * <p/>
    * POST parameters will be preserved.
    *
    * @param array $names A list of parameter names to delete.
    *
    * @return $this This instance for further usage.
    */
   public function deleteGetParameters(array $names) {
      foreach ($names as $name) {
         $this->deleteGetParameter($name);
      }

      return $this;
   }

   /**
    * Convenience method to delete a list of POST parameters.
    * <p/>
    * GET parameters will be preserved.
    *
    * @param array $names A list of parameter names to delete.
    *
    * @return $this This instance for further usage.
    */
   public function deletePostParameters(array $names) {
      foreach ($names as $name) {
         $this->deletePostParameter($name);
      }

      return $this;
   }

   public function resetParameters() {
      $_REQUEST = array();
      $_GET = array();
      $_POST = array();

      return $this;
   }

   public function resetGetParameters() {

      foreach (array_keys($_GET) as $key) {
         unset($_REQUEST[$key]);
      }

      $_GET = array();

      return $this;
   }

   public function resetPostParameters() {

      foreach (array_keys($_POST) as $key) {
         unset($_REQUEST[$key]);
      }

      $_POST = array();

      return $this;
   }

   public function isSecure() {
      if ($_SERVER['SERVER_PORT'] === 443) {
         return true;
      }

      return isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == 1);
   }

   public function getUrl($absolute = false) {

      // construct url from standard PHP variables
      $protocol = $this->isSecure() ? self::SECURE_PROTOCOL_IDENTIFIER . '://' : self::DEFAULT_PROTOCOL_IDENTIFIER . '://';
      $currentUrlString = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

      $url = Url::fromString($currentUrlString);
      if ($absolute === false) {
         $url->setScheme(null);
         $url->setHost(null);
         $url->setPort(null);
      }

      return $url;
   }

   public function getReferrerUrl($absolute = false) {

      if (isset($_SERVER['HTTP_REFERER'])) {
         $url = Url::fromString($_SERVER['HTTP_REFERER']);
         if ($absolute === false) {
            $url->setScheme(null);
            $url->setHost(null);
            $url->setPort(null);
         }

         return $url;
      }
      throw new UrlFormatException('Empty referrer url cannot be used to create a url representation.');
   }

   public function getCookies() {
      $cookies = array();
      foreach ($_COOKIE as $key => $value) {
         $cookie = new Cookie($key);
         $cookie->setValue($value);
         $cookies[] = $cookie;
      }

      return $cookies;
   }

   public function getCookie($name) {
      if (isset($_COOKIE[$name])) {
         $cookie = new Cookie($name);
         $cookie->setValue($_COOKIE[$name]);

         return $cookie;
      }

      return null;
   }

   public function getRequestUri() {
      return $_SERVER['REQUEST_URI'];
   }

   public function getHost() {
      return $_SERVER['HTTP_HOST'];
   }

   public function getPath() {
      // TODO Find a nicer way of implementing this!
      return parse_url($this->getRequestUri())['path'];
   }

   // we are not using getallheaders() as we would limit the APF to be used with Apache only.
   public function getHeaders() {
      $headers = array();

      foreach ($_SERVER as $name => $value) {
         if (substr($name, 0, 5) == 'HTTP_') {
            $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
            $headers[] = new HeaderImpl($name, $value);
         } else if ($name == 'CONTENT_TYPE') {
            $headers[] = new HeaderImpl('Content-Type', $value);
         } else if ($name == 'CONTENT_LENGTH') {
            $headers[] = new HeaderImpl('Content-Length', $value);
         }
      }

      return $headers;
   }

   /**
    * Returns a HTTP header instance by a given name. In case the header has not been sent
    * along with the request, <em>null</em> is returned instead.
    *
    * @param string $name The name of the header to return.
    *
    * @return HeaderImpl|null The desired header instance or null.
    */
   public function getHeader($name) {
      /* @var $header HeaderImpl */
      foreach ($this->getHeaders() as $header) {
         if ($header->getName() == $name) {
            return $header;
         }
      }

      return null;
   }

   public function getMethod() {
      return $_SERVER['REQUEST_METHOD'];
   }

   /**
    * Returns the raw content of the <em>Referer</em> header.
    *
    * @return string|null The content of the <em>Referer</em> header of <em>null</em> in case no referrer is given.
    */
   public function getReferrer() {
      return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
   }

   /**
    * Convenience Method to determine whether we have a GET request.
    *
    * @return bool <em>True</em> in case the current request is a GET request, <em>false</em> otherwise.
    */
   public function isGet() {
      return $this->getMethod() === self::METHOD_GET;
   }

   /**
    * Convenience Method to determine whether we have a POST request.
    *
    * @return bool <em>True</em> in case the current request is a POST request, <em>false</em> otherwise.
    */
   public function isPost() {
      return $this->getMethod() === self::METHOD_POST;
   }

   /**
    * Convenience Method to determine whether we have a PUT request.
    *
    * @return bool <em>True</em> in case the current request is a PUT request, <em>false</em> otherwise.
    */
   public function isPut() {
      return $this->getMethod() === self::METHOD_PUT;
   }

   /**
    * Convenience Method to determine whether we have a DELETE request.
    *
    * @return bool <em>True</em> in case the current request is a DELETE request, <em>false</em> otherwise.
    */
   public function isDelete() {
      return $this->getMethod() === self::METHOD_DELETE;
   }

   /**
    * Convenience Method to determine whether we have an AJAX request.
    *
    * @see http://stackoverflow.com/questions/4301150/how-do-i-check-if-the-request-is-made-via-ajax-with-php
    *
    * @return bool <em>True</em> in case the current request is an AJAX request, <em>false</em> otherwise.
    */
   public function isAjax() {
      if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
      ) {
         return true;
      }

      return false;
   }

   /**
    * Convenience Method to determine whether we have an IMAGE request (e.g. useful for front controller
    * actions delivering dynamic imagery).
    *
    * @return bool <em>True</em> in case the current request is an IMAGE request, <em>false</em> otherwise.
    */
   public function isImage() {
      return stripos($this->getAcceptHeaderContent(), 'image/') !== false;
   }

   /**
    * Convenience Method to determine whether we have an HTML request (e.g. useful for front controller
    * actions).
    *
    * @return bool <em>True</em> in case the current request is an HTML request, <em>false</em> otherwise.
    */
   public function isHtml() {
      return stripos($this->getAcceptHeaderContent(), 'text/html') !== false;
   }

   /**
    * Convenience method to determine whether the client supports GZIP content encoding or not.
    *
    * @return bool <em>True</em> in case the client supports GZIP encoding, <em>false</em> otherwise.
    */
   public function isGzipSupported() {
      return stripos($this->getAcceptedEncodingHeader(), 'gzip') !== false;
   }

   /**
    * Convenience method to determine whether the client supports DEFLATE content encoding or not.
    *
    * @return bool <em>True</em> in case the client supports GZIP encoding, <em>false</em> otherwise.
    */
   public function isDeflateSupported() {
      return stripos($this->getAcceptedEncodingHeader(), 'deflate') !== false;
   }

   /**
    * @return string The Accept header content.
    */
   protected function getAcceptHeaderContent() {
      return isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
   }

   /**
    * @return string The Accept-Encoding header content.
    */
   protected function getAcceptedEncodingHeader() {
      return isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
   }

}