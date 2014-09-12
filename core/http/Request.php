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

interface Request {

   const METHOD_OPTIONS = 'OPTIONS';
   const METHOD_GET = 'GET';
   const METHOD_HEAD = 'HEAD';
   const METHOD_POST = 'POST';
   const METHOD_PUT = 'PUT';
   const METHOD_DELETE = 'DELETE';
   const METHOD_TRACE = 'TRACE';
   const METHOD_CONNECT = 'CONNECT';
   const METHOD_PATCH = 'PATCH';
   const METHOD_PROP_FIND = 'PROPFIND';

   const DEFAULT_PROTOCOL_IDENTIFIER = 'http';
   const SECURE_PROTOCOL_IDENTIFIER = 'https';

   /**
    * Defines to use only content from the $_GET super-global.
    *
    * @var string USE_GET_PARAMS
    */
   const USE_GET_PARAMS = 'GET';

   /**
    * Defines to use only content from the $_POST super-global.
    *
    * @var string USE_POST_PARAMS
    */
   const USE_POST_PARAMS = 'POST';

   /**
    * Defines to use only content from the $_REQUEST super-global (default behaviour).
    *
    * @var string USE_REQUEST_PARAMS
    */
   const USE_REQUEST_PARAMS = 'REQUEST';

   /**
    * @param string $name The name of the URL parameter to get.
    * @param string $default The default value to return in case the parameter is not present (default: <em>null</em>).
    * @param string $type The type of parameter set to request.
    *
    * @return string|array The value of the requested parameter.
    */
   public function getParameter($name, $default = null, $type = self::USE_REQUEST_PARAMS);

   /**
    * @return string Value of the <em>return $_SERVER['REQUEST_URI']</em> offset.
    */
   public function getRequestUri();

   /**
    * @return string The host (a.k.a. server/VHOST) targeted in the request.
    */
   public function getHost();

   /**
    * @return string The URL path starting after the domain/host.
    */
   public function getPath();

   /**
    * @return bool <em>True</em> in case we have an HTTPS/SSL request, <em>false</em> otherwise.
    */
   public function isSecure();

   /**
    * @param bool $absolute Returns an absolute URL if set to <em>true</em>. Default is <em>false</em>.
    *
    * @return Url A url representation of the current request.
    */
   public function getUrl($absolute = false);

   /**
    * @param bool $absolute Returns an absolute URL if set to <em>true</em>. Default is <em>false</em>.
    *
    * @return Url The url representation of the referrer of the current request.
    *
    * @throws UrlFormatException In case no referrer is present.
    */
   public function getReferrerUrl($absolute = false);

   /**
    * @return Cookie[] The list of cookies sent along with the request.
    */
   public function getCookies();

   /**
    * @param string $name The name of the desired cookie.
    *
    * @return Cookie|null The desired cookie or <em>null</em> in case it doesn't exist within the current request.
    */
   public function getCookie($name);

   /**
    * @return string The request method name (e.g. GET).
    */
   public function getMethod();

}