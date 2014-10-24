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
 * Defines the structure of an HTTP request that is handled by APF's Front Controller
 * while processing the incoming request.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.09.2014<br />
 */
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
    * Returns the value of a request parameter (both GET and POST).
    *
    * @param string $name The name of the URL parameter to get.
    * @param string $default The default value to return in case the parameter is not present (default: <em>null</em>).
    *
    * @return string|array The value of the requested parameter.
    */
   public function getParameter($name, $default = null);

   /**
    * Returns the value of the desired parameter if included in a GET request variable.
    * <p/>
    * In case it is instead included in a POST request this method will return the default
    * value.
    * <p/>
    * If you intend to use the value either in GET or POST mode use getParameter() instead.
    *
    * @param string $name The name of the URL parameter to get.
    * @param string $default The default value to return in case the parameter is not present (default: <em>null</em>).
    *
    * @return string|array The value of the requested parameter.
    */
   public function getGetParameter($name, $default = null);

   /**
    * Returns the value of the desired parameter if included in a POST request variable.
    * <p/>
    * In case it is instead included in a GET request this method will return the default
    * value.
    * <p/>
    * If you intend to use the value either in GET or POST mode use getParameter() instead.
    *
    * @param string $name The name of the URL parameter to get.
    * @param string $default The default value to return in case the parameter is not present (default: <em>null</em>).
    *
    * @return string|array The value of the requested parameter.
    */
   public function getPostParameter($name, $default = null);

   /**
    * Returns the current session id.
    *
    * @return string The current session id.
    */
   public function getSessionId();

   /**
    * Let's you retrieve the list of request parameters defined for this instance.
    *
    * @return array The list of request parameters.
    */
   public function getParameters();

   /**
    * Let's you retrieve the list of GET parameters defined for this instance.
    *
    * @return array The list of request parameters.
    */
   public function getGetParameters();

   /**
    * Let's you retrieve the list of POST parameters defined for this instance.
    *
    * @return array The list of request parameters.
    */
   public function getPostParameters();

   /**
    * Allows you to set a request parameter (both GET and POST).
    *
    * @param string $name The name of the URL parameter to get.
    * @param string $value The default value to return in case the parameter is not present (default: <em>null</em>).
    *
    * @return $this This instance for further usage.
    */
   public function setParameter($name, $value);

   /**
    * Allows you to set a GET parameter.
    * <p/>
    * Please note that calling getPostParameter() will not return the desired value. Thus,
    * please be sure to use the correct method.
    *
    * @param string $name The name of the URL parameter to get.
    * @param string $value The default value to return in case the parameter is not present (default: <em>null</em>).
    *
    * @return $this This instance for further usage.
    */
   public function setGetParameter($name, $value);

   /**
    * Allows you to set a POST parameter.
    * <p/>
    * Please note that calling getGetParameter() will not return the desired value. Thus,
    * please be sure to use the correct method.
    *
    * @param string $name The name of the URL parameter to get.
    * @param string $value The default value to return in case the parameter is not present (default: <em>null</em>).
    *
    * @return $this This instance for further usage.
    */
   public function setPostParameter($name, $value);

   /**
    * Let's you delete a request parameter.
    *
    * @param string $name The name of the request parameter to delete.
    *
    * @return $this This instance for further usage.
    */
   public function deleteParameter($name);

   /**
    * Let's you delete a GET parameter. If contained in the POST part of the request,
    * it will still be available.
    *
    * @param string $name The name of the request parameter to delete.
    *
    * @return $this This instance for further usage.
    */
   public function deleteGetParameter($name);

   /**
    * Let's you delete a GET parameter. If contained in the POST part of the request,
    * it will still be available.
    *
    * @param string $name The name of the request parameter to delete.
    *
    * @return $this This instance for further usage.
    */
   public function deletePostParameter($name);

   /**
    * Allows to reset the entire set of request parameters.
    *
    * @return $this This instance for further usage.
    */
   public function resetParameters();

   /**
    * Allows to reset the GET parameters of this request instance.
    *
    * @return $this This instance for further usage.
    */
   public function resetGetParameters();

   /**
    * Allows to reset the POST parameters of this request instance.
    *
    * @return $this This instance for further usage.
    */
   public function resetPostParameters();

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
    * Returns the list of Cookies sent along with the request.
    * <p/>
    * Please note that the Cookie instances will only contain their name and respective
    * value as the underlying HTTP request does not propagate e.g. life time settings.
    * <p/>
    * For details, please refer to e.g. http://stackoverflow.com/questions/23446989/get-the-raw-request-using-php.
    *
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