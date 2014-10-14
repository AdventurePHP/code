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

/**
 * ...
 * <p/>
 * All <em>CODE_*</em> constants define the HTTP status codes as documented in RFC 2616 (see
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html for details).
 * <p/>
 * Constants <em>REASON_*</em> include the response reason phrase according to section 6.1.1.
 * <p/>
 * Constants <em>VERSION_*</em> define the available HTTP protocol versions.
 *
 * @author Christian Achatz
 */
interface Response {

   const VERSION_1_0 = '1.0';
   const VERSION_1_1 = '1.1';

   // response codes as per RFC 2616
   const CODE_CONTINUE = 100;
   const CODE_SWITCHING_PROTOCOLS = 101;
   const CODE_OK = 200;
   const CODE_CREATED = 201;
   const CODE_ACCEPTED = 202;
   const CODE_NON_AUTHORITATIVE_INFORMATION = 203;
   const CODE_NO_CONTENT = 204;
   const CODE_RESET_CONTENT = 205;
   const CODE_PARTIAL_CONTENT = 206;
   const CODE_MULTIPLE_CHOICE = 300;
   const CODE_MOVED_PERMANENTLY = 301;
   const CODE_FOUND = 302;
   const CODE_SEE_OTHER = 303;
   const CODE_NOT_MODIFIED = 304;
   const CODE_USE_PROXY = 305;
   const CODE_TEMPORARY_REDIRECT = 307;
   const CODE_PERMANENT_REDIRECT = 308;
   const CODE_BAD_REQUEST = 400;
   const CODE_UNAUTHORIZED = 401;
   const CODE_PAYMENT_REQUIRED = 402;
   const CODE_FORBIDDEN = 403;
   const CODE_NOT_FOUND = 404;
   const CODE_METHOD_NOT_ALLOWED = 405;
   const CODE_NOT_ACCEPTABLE = 406;
   const CODE_PROXY_AUTHENTICATION_REQUIRED = 407;
   const CODE_REQUEST_TIMEOUT = 408;
   const CODE_CONFLICT = 409;
   const CODE_GONE = 410;
   const CODE_LENGTH_REQUIRED = 411;
   const CODE_PRECONDITION_FAILED = 412;
   const CODE_REQUEST_ENTITY_TOO_LARGE = 413;
   const CODE_REQUEST_URI_TOO_LONG = 414;
   const CODE_UNSUPPORTED_MEDIA_TYPE = 415;
   const CODE_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
   const CODE_EXPECTATION_FAILED = 417;
   const CODE_INTERNAL_SERVER_ERROR = 500;
   const CODE_NOT_IMPLEMENTED = 501;
   const CODE_BAD_GATEWAY = 502;
   const CODE_SERVICE_UNAVAILABLE = 503;
   const CODE_GATEWAY_TIMEOUT = 504;
   const CODE_HTTP_VERSION_NOT_SUPPORTED = 505;

   /**
    * @return string The HTTP version to use for conversation.
    */
   public function getVersion();

   /**
    * @param string $version The HTTP version to use for conversation (see <em>VERSION_*</em> constants).
    *
    * @return Response This instance for further usage.
    */
   public function setVersion($version);

   /**
    * @return int The current HTTP status code.
    */
   public function getStatusCode();

   /**
    * @param int $code The desired HTTP status code to send.
    *
    * @return Response This instance for further usage.
    */
   public function setStatusCode($code);

   /**
    * @return string The current reason phrase.
    */
   public function getReasonPhrase();

   /**
    * @param string $phrase The reason phrase to send back to the client.
    *
    * @return Response This instance for further usage.
    */
   public function setReasonPhrase($phrase);

   /**
    * @return string The HTTP response body to send back to the client.
    */
   public function getBody();

   /**
    * @param string $body The body to send back to the client.
    * @param bool $append Set to <em>true</em> the given body content is appended, in case of <em>false</em> existing content is overwritten.
    *
    * @return Response This instance for further usage.
    */
   public function setBody($body, $append = false);

   /**
    * Allows to (explicitly) send a response <em>manually</em> within document controllers and front
    * controller actions that generate specific output.
    * <p/>
    * Terminates the request processing (except logging and session handling which is realized with
    * shutdown functions) in case <em>$exit</em> is set to <em>true</em>.
    *
    * @param bool $exit Stops request processing if set to <em>true</em> and continues with <em>false</em>.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.09.2014<br />
    */
   public function send($exit = true);

   /**
    * @see http://www.faqs.org/rfcs/rfc2616 (section 10.3.4 303 See Other)
    *
    * Forwards to a given target.
    *
    * @param string $url The target URL.
    * @param bool $exitAfterForward True in case code execution is stopped after this action, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.10.2008<br />
    * Version 0.2, 11.03.2014 (ID#173: corrected wrong header() parameter usage)<br />
    */
   public function forward($url, $exitAfterForward = true);

   /**
    * Redirects to a given target.
    *
    * @param string $url The target URL.
    * @param bool $permanent indicates, if the redirect is permanent (true) or not (false)
    * @param bool $exitAfterForward True in case code execution is stopped after this action, false otherwise.
    *
    * @see http://www.faqs.org/rfcs/rfc2616 (sections 10.3.2 301 Moved Permanently and 10.3.3 302 Found)
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.10.2008<br />
    */
   public function redirect($url, $permanent = false, $exitAfterForward = true);

   /**
    * Sends a 404 answer back to the client.
    *
    * @param bool $exitAfterForward True in case code execution is stopped after this action, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.05.2014
    */
   public function sendNotFound($exitAfterForward = true);

   /**
    * Sends an 500 answer back to the client.
    *
    * @param bool $exitAfterForward True in case code execution is stopped after this action, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.05.2014
    */
   public function sendServerError($exitAfterForward = true);

   /**
    * @return bool <em>True</em> in case this instance has already been sent to the client, <em>false</em> otherwise.
    */
   public function isSent();

   /**
    * Allows you to directly send the response via
    * <code>
    * echo $response;
    * </code>
    * where <em>$response</em> is an instance of <em>APF\core\http\Response</em>.
    *
    * @return string The content of the current response.
    */
   public function __toString();

   /**
    * @return Header[] The list of registered response headers.
    */
   public function getHeaders();

   /**
    * Adds - and if existing replaces - an HTTP header.
    *
    * @param Header $header The HTTP response header to set/add.
    *
    * @return Response This instance for further usage.
    */
   public function setHeader(Header $header);

   /**
    * Removes an existing HTTP header. If not set, does nothing.
    *
    * @param Header $header The HTTP response header to delete from the response.
    *
    * @return Response This instance for further usage.
    */
   public function deleteHeader(Header $header);

   /**
    * @return Cookie[] The list of cookies to be sent to the client.
    */
   public function getCookies();

   /**
    * Sets a cookie for the current response.
    *
    * @param Cookie $cookie The cookie to set.
    *
    * @return Response This instance for further usage.
    */
   public function setCookie(Cookie $cookie);

   /**
    * Deletes a cookie from the current response.
    *
    * @param Cookie $cookie The cookie to delete.
    *
    * @return Response This instance for further usage.
    */
   public function deleteCookie(Cookie $cookie);

}