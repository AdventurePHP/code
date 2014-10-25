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
namespace APF\core\http\client;

use APF\core\http\Header;
use APF\core\http\HeaderImpl;
use APF\core\http\Request;
use APF\core\http\Response;
use APF\core\http\ResponseImpl;

/**
 * Provides basic functionality to initiate a HTTP request within your application
 * towards external systems.
 */
class BaseAdapter {

   protected function getHeadersString($responseString) {
      return substr($responseString, 0, strpos($responseString, "\r\n\r\n"));
   }

   protected function getBodyString($responseString) {
      return substr($responseString, strpos($responseString, "\r\n\r\n") + 4);
   }

   protected function setHeadersFromString(Response &$response, $headerString) {

      // remove existing headers to have a real and clean curl response
      /* @var $response ResponseImpl */
      $response->resetHeaders();

      $headerLines = explode("\r\n", $headerString);

      // skip first line...
      unset($headerLines[0]);

      foreach ($headerLines as $line) {
         $parts = explode(':', $line);
         $response->setHeader(new HeaderImpl($parts[0], $parts[1]));
      }

   }

   /**
    * @param Request $request The request to serialize.
    *
    * @return string The string representation of the given request.
    */
   public function getRequestStringRepresentation(Request $request) {

      // create query string
      $url = $request->getUrl();
      $queryString = http_build_query($url->getQuery());
      if (!empty($queryString)) {
         $queryString = '?' . $queryString;
      }

      $rawUrlPath = $url->getPath();
      $urlPath = ($rawUrlPath == '' || $rawUrlPath == null) ? '/' : $rawUrlPath;

      // create request string presentation
      $requestString = $request->getMethod() . ' ' . $urlPath . $queryString . ' HTTP/' . $request->getVersion() . "\r\n";

      $requestString .= 'Host: ' . $request->getHost() . "\r\n";
      foreach ($request->getHeaders() as $header) {
         $requestString .= $header->getName() . ': ' . $header->getValue() . "\r\n";
      }

      if ($request->getMethod() === Request::METHOD_POST) {
         $query = http_build_query($request->getParameters());
         $contentLength = strlen($query);
         $requestString .= Header::CONTENT_TYPE . ': application/x-www-form-urlencoded' . "\r\n";
         $requestString .= Header::CONTENT_LENGTH . ': ' . $contentLength . "\r\n\r\n";
         $requestString .= $query;

         return $requestString;
      }

      return $requestString . "\r\n";
   }

}
