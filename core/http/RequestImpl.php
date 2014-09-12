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

class RequestImpl implements Request {

   public function getParameter($name, $default = null, $type = self::USE_REQUEST_PARAMS) {
      $lookupTable = $GLOBALS['_' . $type];

      return isset($lookupTable[$name])
      // avoid issues with "0" values being skipped due to empty() check
      && (!empty($lookupTable[$name]) || (string) $lookupTable[$name] === '0')
            ? $lookupTable[$name]
            : $default;
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

         // TODO Read extended values (e.g. domain, ...) from $_SERVER and inject
         // TODO --> Will not work. Client sends only key=>value. Thus, add to docs.
         // http://stackoverflow.com/questions/23446989/get-the-raw-request-using-php
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

   public function getMethod() {
      return $_SERVER['REQUEST_METHOD'];
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
      return stripos($_SERVER['HTTP_ACCEPT'], 'image/') !== false;
   }

}