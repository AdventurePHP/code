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

class HttpResponse extends HttpTransaction {

   protected $statusLine;
   protected $content;

   public function setStatusCode($statusCode) {
      $this->statusCode = $statusCode;

      return $this;
   }

   public function getStatusCode() {
      return $this->statusCode;
   }

   public function setContentType($value) {
      $this->addHeader(self::HEADER_CONTENT_TYPE, $value);

      return $this;
   }

   public function getContentType() {
      return $this->getHeader(self::HEADER_CONTENT_TYPE);
   }

   public function setContent($content) {
      $this->content = trim($content);

      return $this;
   }

   public function getContent() {
      return $this->content;
   }

   public function toString() {
      $response = 'HTTP/1.1 ' . $this->getStatusCode() . "\r\n";
      foreach ($this->getHeaders() as $key => $value) {
         $response .= $key . ': ' . $value . "\r\n";
      }

      $response .= "\r\n" . $this->getContent();

      return $response;
   }

   public function __toString() {
      return $this->toString();
   }

}
