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

use APF\core\http\Request;
use APF\core\http\ResponseImpl;

class SocketAdapter implements HttpAdapter {

   protected $response;

   public function executeRequest(Request $request) {

      // create request
      // 80 is the port of the webserver
      $connection = fsockopen($request->getHost(), 80);
      $requestString = $request->toString();
      fwrite($connection, $requestString);

      // get response
      // first line == status line
      $result = '';
      $statusLine = '';
      while ($line = fgets($connection)) {
         if (empty($statusLine)) {
            $statusLine = $line;
            continue;
         }
         $result .= $line;
      }

      // close connection
      fclose($connection);

      // split result into header and content
      // the header ends with \r\n\r\n
      $header = substr($result, 0, strpos($result, "\r\n\r\n"));
      $content = substr($result, (strpos($result, "\r\n\r\n") + 4));

      $this->response = new ResponseImpl();
      $this->response->setHeadersFromString($header);
      $this->response->setContent($content);

      // parse status line
      // status-line-pattern: <HTTP-Version> <status-code> <reason-phrase>
      $status = explode(' ', $statusLine);
      $this->response->setStatusCode($status[1]);

      return $this->response;
   }

}
