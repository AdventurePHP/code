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
namespace APF\core\http;

use Exception;

/**
 * Generic implementation of a HTTP Response to return the appropriate answer
 * of the current application to the user.
 * <p/>
 * To allow easy Unit Test execution exit()'s used within the forward() and
 * redirect() methods can be switched of globally. In case you intend to not
 * stop script execution within a certain controller, you may pass an optional
 * argument to forward() or redirect().
 * <p />
 * Configuration of the code execution stopping feature can be influenced as
 * follows:
 * <pre>
 * self::$EXIT_AFTER_FORWARD | $exitAfterForward | result
 * true                      | true              | exit()
 * true                      | false             | -
 * false                     | true              | -
 * false                     | false             | -
 * </pre>
 *
 * @see https://adventure-php-framework.org/forum/viewtopic.php?p=243#p243
 * @see https://adventure-php-framework.org/tracker/view.php?id=72
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 09.10.2008<br />
 * Version 0.2, 27.08.2013 (Introduced exit after forward to allow easy Unit Test execution)<br />
 * Version 0.3, 14.10.2014 (Final implementation of the Request/Response abstraction and take-over from HeaderManager)<br />
 */
class ResponseImpl implements Response {

   const HEADER_SEPARATOR = "\r\n";

   /**
    * @var string The HTTP version to use for conversation.
    */
   protected $version = self::VERSION_1_1;

   /**
    * @var string[] Defines the default reason phrases to send along with the HTTP response as per RFC 2616
    */
   protected $reasonPhrases = [
         100 => 'Continue',
         101 => 'Switching Protocols',
         200 => 'OK',
         201 => 'Created',
         202 => 'Accepted',
         203 => 'Non-Authoritative Information',
         204 => 'No Content',
         205 => 'Reset Content',
         206 => 'Partial Content',
         300 => 'Multiple Choices',
         301 => 'Moved Permanently',
         302 => 'Found',
         303 => 'See Other',
         304 => 'Not Modified',
         305 => 'Use Proxy',
         307 => 'Temporary Redirect',
         400 => 'Bad Request',
         401 => 'Unauthorized',
         402 => 'Payment Required',
         403 => 'Forbidden',
         404 => 'Not Found',
         405 => 'Method Not Allowed',
         406 => 'Not Acceptable',
         407 => 'Proxy Authentication Required',
         408 => 'Request Time-out',
         409 => 'Conflict',
         410 => 'Gone',
         411 => 'Length Required',
         412 => 'Precondition Failed',
         413 => 'Request Entity Too Large',
         414 => 'Request-URI Too Large',
         415 => 'Unsupported Media Type',
         416 => 'Requested range not satisfiable',
         417 => 'Expectation Failed',
         500 => 'Internal Server Error',
         501 => 'Not Implemented',
         502 => 'Bad Gateway',
         503 => 'Service Unavailable',
         504 => 'Gateway Time-out',
         505 => 'HTTP Version not supported'
   ];

   /**
    * @var int The HTTP status code.
    */
   protected $statusCode = self::CODE_OK;

   /**
    * @var string The reason phrase sent along with the HTTP response.
    */
   protected $reasonPhrase = null;

   /**
    * @var string The response body.
    */
   protected $body = '';

   /**
    * @var Header[] The list of HTTP headers to send along with the response.
    */
   protected $headers = [];

   /**
    * @var Cookie[] The list of cookies ("special" headers) to send along with the response.
    */
   protected $cookies = [];

   /**
    * @var bool Marks this response instance as sent (<em>true</em>) or not (<em>false</em>).
    */
   protected $isSent = false;

   /**
    * True in case code execution is stopped after forward() or redirect(), false otherwise.
    *
    * @var bool $exitAfterForward
    */
   private $exitAfterForward = true;

   /**
    * Activates behaviour to stop code execution after forward() or redirect().
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2013<br />
    */
   public function activateExitAfterForward() {
      $this->exitAfterForward = true;
   }

   /**
    * Deactivates behaviour to stop code execution after forward() or redirect().
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2013<br />
    */
   public function deactivateExitAfterForward() {
      $this->exitAfterForward = false;
   }

   public function getVersion() {
      return $this->version;
   }

   public function setVersion($version) {
      $this->version = $version;

      return $this;
   }

   public function getStatusCode() {
      return $this->statusCode;
   }

   public function setStatusCode($code) {
      $this->statusCode = $code;

      return $this;
   }

   public function getReasonPhrase() {
      // default mapping of status code -> reason phrase
      if ($this->reasonPhrase === null) {
         $code = $this->getStatusCode();
         if (isset($this->reasonPhrases[$code])) {
            return $this->reasonPhrases[$code];
         } else {
            return 'Unknown response status';
         }
      }

      return $this->reasonPhrase;
   }

   public function setReasonPhrase($phrase) {
      $this->reasonPhrase = $phrase;

      return $this;
   }

   public function getBody() {
      return $this->body;
   }

   public function setBody($body, $append = false) {
      $this->body = $append ? $this->body . $body : $body;

      return $this;
   }

   public function getHeaders() {
      // don't expose internal structure...
      return array_values($this->headers);
   }

   public function setHeader(Header $header) {
      $this->headers[$header->getName()] = $header;

      return $this;
   }

   public function deleteHeader(Header $header) {
      unset($this->headers[$header->getName()]);

      return $this;
   }

   /**
    * Removes all registered HTTP headers.
    *
    * @return ResponseImpl This instance for further usage.
    */
   public function resetHeaders() {
      $this->headers = [];

      return $this;
   }

   public function getCookies() {
      return array_values($this->cookies);
   }

   public function setCookie(Cookie $cookie) {
      $this->cookies[$cookie->getName()] = $cookie;

      return $this;
   }

   public function deleteCookie(Cookie $cookie) {
      $this->cookies[$cookie->getName()] = $cookie->delete();

      return $this;
   }

   /**
    * Convenience method for setting the <em>Content-Type</em> HTTP header.
    *
    * @param string $type The desired content type (e.g. text/html; charset=utf-8).
    *
    * @return Response This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.09.2014<br />
    */
   public function setContentType($type) {
      return $this->setHeader(new HeaderImpl(HeaderImpl::CONTENT_TYPE, $type));
   }

   /**
    * Convenience method for getting the <em>Content-Type</em> HTTP header.
    *
    * @return string The content type set for this instance (e.g. text/html; charset=utf-8).
    *
    * @throws Exception In case the HTTP response header <em>Content-Type</em> is not set.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.09.2014<br />
    */
   public function getContentType() {
      if (isset($this->headers[HeaderImpl::CONTENT_TYPE])) {
         return $this->headers[HeaderImpl::CONTENT_TYPE]->getValue();
      }
      throw new Exception('Header "' . HeaderImpl::CONTENT_TYPE . '" is not set for this response.');
   }

   public function send($exit = true) {
      // rely on __toString() sending the actual response.
      echo $this;

      if ($exit === true) {
         exit(0);
      }
   }

   /**
    * @param string $url The URL possibly containing encoded ampersands.
    *
    * @return string The decoded url.
    */
   protected static function decodeUrl($url) {
      return str_replace('&amp;', '&', $url);
   }

   public function forward($url, $exitAfterForward = true) {
      self::setStatusCode(self::CODE_SEE_OTHER);
      self::setHeader(new HeaderImpl('Location', self::decodeUrl($url)));
      self::send($this->exitAfterForward === true && $exitAfterForward === true);
   }

   public function redirect($url, $permanent = false, $exitAfterForward = true) {
      self::setStatusCode($permanent === true ? self::CODE_PERMANENT_REDIRECT : self::CODE_TEMPORARY_REDIRECT);
      self::setHeader(new HeaderImpl('Location', self::decodeUrl($url)));
      self::send($this->exitAfterForward === true && $exitAfterForward === true);
   }

   public function sendNotFound($exitAfterForward = true) {
      self::setStatusCode(self::CODE_NOT_FOUND);
      self::setReasonPhrase($this->reasonPhrases[self::CODE_NOT_FOUND]);
      self::send($this->exitAfterForward === true && $exitAfterForward === true);
   }

   public function sendServerError($exitAfterForward = true) {
      self::setStatusCode(self::CODE_INTERNAL_SERVER_ERROR);
      self::setReasonPhrase($this->reasonPhrases[self::CODE_INTERNAL_SERVER_ERROR]);
      self::send($this->exitAfterForward === true && $exitAfterForward === true);
   }

   public function __toString() {

      if ($this->isSent) {
         throw new HttpResponseException('Response is already sent!');
      }

      header(
            'HTTP/' . $this->getVersion() . ' ' . $this->getStatusCode() . ' ' . $this->getReasonPhrase(),
            true,
            $this->getStatusCode()
      );

      // "normal" headers
      foreach ($this->headers as $header) {
         // rely on Header::__toString() for string construction
         header($header, true);
      }

      // cookies
      foreach ($this->cookies as $cookie) {
         if ($cookie->isDeleted()) {
            setcookie($cookie->getName(), false, time() - Cookie::DEFAULT_EXPIRATION_TIME);
         } else {
            setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpireTime(), $cookie->getPath(),
                  $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
         }
      }

      // mark as sent to avoid duplicate responses
      $this->isSent = true;

      return $this->body;
   }

   public function isSent() {
      return $this->isSent;
   }

   /**
    * Convenience method to dump the current response.
    *
    * @return string The string representation of the current response.
    */
   public function dump() {
      $response = 'HTTP/ ' . $this->version . $this->getStatusCode() . self::HEADER_SEPARATOR;
      foreach ($this->headers as $header) {
         $response .= $header . self::HEADER_SEPARATOR;
      }

      return $response . self::HEADER_SEPARATOR . self::HEADER_SEPARATOR . $this->body;
   }

}
