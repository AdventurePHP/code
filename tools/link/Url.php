<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\tools\link;

use APF\core\http\mixins\GetRequestResponse;

/**
 * This class represents a url designed to generate related urls using
 * the APF's link scheme implementations.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2011<br />
 * Version 0.2, 05.09.2015 (ID#258: added support for array URL params)<br />
 */
final class Url {

   use GetRequestResponse;

   const DEFAULT_HTTP_PORT = '80';
   const DEFAULT_HTTPS_PORT = '443';

   private $scheme;
   private $host;
   private $port;
   private $path;
   private $query = [];
   private $anchor;

   /**
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
   public function __construct(string $scheme = null, string $host = null, int $port = null, string $path = null, array $query = [], string $anchor = null) {
      $this->scheme = $scheme;
      $this->host = $host;
      $this->port = $port;
      $this->path = $path;
      $this->query = $query;
      $this->anchor = $anchor;
   }

   /**
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
   public static function fromString(string $url) {

      // the ugly "@" is only introduced to convert the E_WARNING into an exception
      $parts = @parse_url($url);
      if ($parts === false || !is_array($parts)) {
         throw new UrlFormatException('The given url "' . $url . '" cannot be parsed due to semantic errors!');
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
    * Generates a query param array from a given query string.
    *
    * @param string $query The query params string.
    *
    * @return array The query params array.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2011<br />
    * Version 0.2, 05.09.2015 (ID#258: support nested query parameters)<br />
    */
   private static function getQueryParams(string $query = null) {

      // reverse resolve encoded ampersands
      $query = str_replace('&amp;', '&', $query);

      // in case of empty query strings, return empty param list
      if (empty($query)) {
         return [];
      }

      parse_str($query, $output);

      return $output;

   }

   /**
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
   public static function fromCurrent(bool $absolute = false) {
      return self::getRequestStatic()->getUrl($absolute);
   }

   /**
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
   public static function fromReferrer(bool $absolute = false) {
      return self::getRequestStatic()->getReferrerUrl($absolute);
   }

   public function getScheme() {
      return $this->scheme;
   }

   /**
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
   public function setScheme(string $scheme = null) {
      $this->scheme = $scheme;

      return $this;
   }

   public function getHost() {
      return $this->host;
   }

   /**
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
   public function setHost(string $host = null) {
      $this->host = $host;

      return $this;
   }

   public function getPort() {
      return $this->port;
   }

   /**
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
   public function setPort(int $port = null) {
      $this->port = $port;

      return $this;
   }

   public function getPath() {
      return $this->path;
   }

   /**
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
   public function setPath(string $path = null) {
      $this->path = $path;

      return $this;
   }

   /**
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
   public function setQuery(array $query = null) {
      $this->query = $query;

      return $this;
   }

   public function getAnchor() {
      return $this->anchor;
   }

   /**
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
   public function setAnchor(string $anchor = null) {
      $this->anchor = $anchor;

      return $this;
   }

   /**
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
   public function getQueryParameter(string $name, $default = null) {
      return isset($this->query[$name]) ? $this->query[$name] : $default;
   }

   /**
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
   public function mergeQuery(array $query) {
      foreach ($query as $name => $value) {
         $this->query[$name] = $value;
      }

      return $this;
   }

   /**
    * This method resets the list of parameters.
    *
    * @return Url This object for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.06.2011<br />
    */
   public function resetQuery() {
      $this->query = [];

      return $this;
   }

   /**
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
   public function setQueryParameter(string $name, $value) {
      $this->query[$name] = $value;

      return $this;
   }

}
