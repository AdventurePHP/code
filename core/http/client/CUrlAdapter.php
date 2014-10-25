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

class CUrlAdapter extends BaseAdapter implements HttpAdapter {

   /**
    * @var ResponseImpl
    */
   protected $response;

   public function executeRequest(Request $request) {

      $cH = curl_init($request->getUrl());
      curl_setopt($cH, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($cH, CURLOPT_HEADER, true);
      if ($request->getMethod() === Request::METHOD_POST) {
         curl_setopt($cH, CURLOPT_POST, true);
         curl_setopt($cH, CURLOPT_POSTFIELDS, http_build_query($request->getParameters()));
      }

      $result = curl_exec($cH);
      $this->response = new ResponseImpl();
      $this->response->setStatusCode(curl_getinfo($cH, CURLINFO_HTTP_CODE));
      $this->setHeadersFromString($this->response, $this->getHeadersString($result));
      $this->response->setBody($this->getBodyString($result));
      curl_close($cH);

      return $this->response;
   }

}
