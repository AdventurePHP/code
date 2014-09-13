<?php
/**
 *  <!--
 *  This file is part of the adventure php framework (APF) published under
 *  http://adventure-php-framework.org.
 *
 *  The APF is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The APF is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 *  -->
 */

import('tools::http', 'HttpTransaction');
import('tools::link', 'LinkGenerator');

class HttpRequest extends HttpTransaction {

   /**
    * @var string
    */
   protected $url = null;
   protected $method;
   protected $parameters = array();

   const METHOD_GET = 'GET';
   const METHOD_POST = 'POST';

   const HEADER_USER_AGENT = 'User-Agent';

   public function setUrl($url) {
      $this->url = Url::fromString($url);
      return $this;
   }

   public function getUrl() {
      return LinkGenerator::GenerateUrl($this->url);
   }

   public function setHost($host) {
      $this->url->setHost($host);
      return $this;
   }

   public function getHost() {
      return $this->url->getHost();
   }

   public function setUrlPath($urlPath) {
      $this->url->setPath($urlPath);
      return $this;
   }

   public function getUrlPath() {
      return $this->url->getPath();
   }

   public function setMethod($method) {
      $this->method = $method;
      return $this;
   }

   public function getMethod() {
      return $this->method;
   }

   public function setUserAgent($userAgent) {
      $this->addHeader(self::HEADER_USER_AGENT, $userAgent);
      return $this;
   }

   public function getUserAgent() {
      return $this->getHeader(self::HEADER_USER_AGENT);
   }

   public function addParameter($name, $value) {
      $this->parameters[$name] = $value;
      return $this;
   }

   public function addParameters(array $parameters) {
      foreach ($parameters as $key => $value) {
         $this->addParameter($key, $value);
      }
      return $this;
   }

   public function getParameter($name, $defaultValue) {
      return isset($this->parameters[$name]) ? $this->parameters[$name] : $defaultValue;
   }

   public function getParameters() {
      return $this->parameters;
   }

   public function removeParameter($name) {
      if (isset($this->parameters[$name])) {
         unset($this->parameters[$name]);
      }
      return $this;
   }

   public function removeParameters() {
      $this->parameters = array();
      return $this;
   }

   public function toString() {

      // create query string
      $queryString = http_build_query($this->url->getQuery());
      if (!empty($queryString)) {
         $queryString = '?' . $queryString;
      }

      $urlPath = ($this->getUrlPath() == '' || $this->getUrlPath() == null) ? '/' : $this->getUrlPath();

      // create request string presentation
      $request = $this->getMethod() . ' ' . $urlPath . $queryString . " HTTP/1.1\r\n";
      $request .= 'Host: ' . $this->getHost() . "\r\n";
      foreach ($this->getHeaders() as $key => $value) {
         $request .= $key . ': ' . $value . "\r\n";
      }

      if ($this->getMethod() === self::METHOD_POST) {
         $query = http_build_query($this->getParameters());
         $contentLength = strlen($query);
         $request .= self::HEADER_CONTENT_TYPE . ": application/x-www-form-urlencoded\r\n";
         $request .= self::HEADER_CONTENT_LENGTH . ": {$contentLength}\r\n\r\n";
         $request .= $query;
         return $request;
      }

      $request .= "\r\n";
      return $request;
   }

   public function __toString() {
      return $this->toString();
   }

}
