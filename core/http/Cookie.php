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

use InvalidArgumentException;

/**
 * The Cookie class is an abstraction of a HTTP cookie. It allows you to create, configure and set/delete
 * it using APF's request handling mechanism.
 * <p/>
 * Usage:
 * <pre>
 * $cookie = new Cookie('my_cookie');
 * self::getResponse()->setCookie($cookie);
 *
 * self::getResponse()->deleteCookie($cookie);
 * </pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.11.2008<br />
 * Version 0.2, 10.01.2009 (Finished implementation and testing)<br />
 */
class Cookie {

   /**
    * Default path of the Cookie.
    *
    * @var string DEFAULT_PATH
    */
   const DEFAULT_PATH = '/';

   /**
    * Default expiration time of the cookie (=1 day).
    *
    * @var int DEFAULT_EXPIRATION_TIME
    */
   const DEFAULT_EXPIRATION_TIME = 86400;

   /**
    * The name of the cookie.
    *
    * @var string $name
    */
   protected $name;

   /**
    * The value of the cookie.
    *
    * @var string $value
    */
   protected $value;

   /**
    * The domain the cookie is valid for.
    *
    * @var string $domain
    */
   protected $domain;

   /**
    * $path The path the cookie s valid for.
    *
    * @var string $path
    */
   protected $path;

   /**
    * True in case the cookie is only valid for HTTPS transmission, false otherwise.
    *
    * @var bool $secure
    */
   protected $secure = false;

   /**
    * True in case the cookie can only be modified via HTTP, false otherwise.
    *
    * @var bool $httpOnly
    */
   protected $httpOnly = false;

   /**
    * Defines the default expiration time in seconds.
    *
    * @var int $expireTime
    */
   protected $expireTime;

   /**
    * Indicates whether the cookie should be deleted or not.
    *
    * @var bool $isDeleted
    */
   protected $isDeleted = false;

   /**
    * Let's you create a Cookie.
    *
    * @param string $name The name of the cookie.
    * @param int $expireTime The life time in seconds (default: 1 day).
    * @param string $domain The domain the cookie is valid for.
    * @param string $path The path the cookie s valid for.
    * @param bool $secure True in case the cookie is only valid for HTTPS transmission, false otherwise.
    * @param bool $httpOnly True in case the cookie can only be modified via HTTP, false otherwise.
    *
    * @throws InvalidArgumentException In case of an empty cookie name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.11.2008<br />
    * Version 0.2, 13.06.2013 (Refactoring to real domain object representation)<br />
    */
   public function __construct($name, $expireTime = null, $domain = null, $path = null, $secure = null, $httpOnly = null) {

      if (empty($name)) {
         throw new InvalidArgumentException('Cookie cannot be created with an empty name!');
      }

      $this->name = $name;
      $this->path = $path === null ? self::DEFAULT_PATH : $path;
      $this->domain = $domain === null ? $_SERVER['HTTP_HOST'] : $domain;
      $this->secure = $secure;
      $this->httpOnly = $httpOnly;

      // set default expire time in case nothing specified
      if ($expireTime === null) {
         $this->expireTime = time() + self::DEFAULT_EXPIRATION_TIME;
      } else {
         $this->expireTime = $expireTime;
      }
   }

   /**
    * @return string The name of the cookie.
    */
   public function getName() {
      return $this->name;
   }

   /**
    * Defines the value of the cookie.
    *
    * @param string $value The value of the cookie.
    *
    * @return Cookie This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.11.2008<br />
    * Version 0.2, 13.06.2013 (Refactoring to real domain object representation)<br />
    */
   public function setValue($value) {
      $this->value = $value;

      return $this;
   }

   /**
    * Returns the value of the desired key within the current namespace.
    *
    * @param string $default The default value in case the cookie is not existing.
    *
    * @return string Cookie value or default value.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.11.2008<br />
    * Version 0.2, 10.01.2009 (Added namespace support)<br />
    * Version 0.3, 13.06.2013 (Refactoring to real domain object representation)<br />
    */
   public function getValue($default = null) {
      return isset($_COOKIE[$this->name]) ? $_COOKIE[$this->name] : $default;
   }

   /**
    * Deletes the Cookie.
    *
    * @return Cookie This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.11.2008<br />
    * Version 0.2, 10.01.2009 (Added namespace support)<br />
    * Version 0.3, 13.06.2013 (Refactoring to real domain object representation)<br />
    * Version 0.4, 27.05.2014 (Removed path and domain for delete as this causes issues with some browsers)<br />
    */
   public function delete() {
      $this->isDeleted = true;

      return $this;
   }

   public function isDeleted() {
      return $this->isDeleted;
   }

   /**
    * @return string The name of the domain the Cookie is valid for.
    */
   public function getDomain() {
      return $this->domain;
   }

   /**
    * @param string $domain The name of the domain the Cookie is valid for.
    *
    * @return Cookie This instance for further usage.
    */
   public function setDomain($domain) {
      $this->domain = $domain;

      return $this;
   }

   /**
    * @return int
    */
   public function getExpireTime() {
      return $this->expireTime;
   }

   /**
    * @param int $expireTime
    *
    * @return Cookie This instance for further usage.
    */
   public function setExpireTime($expireTime) {
      $this->expireTime = $expireTime;

      return $this;
   }

   /**
    * @return boolean
    */
   public function isHttpOnly() {
      return $this->httpOnly;
   }

   /**
    * @param boolean $httpOnly True in case the cookie should be restricted to HTTP protocol chances, false otherwise
    * (e.g. for Java Script manipulation).
    *
    * @return Cookie This instance for further usage.
    */
   public function setHttpOnly($httpOnly) {
      $this->httpOnly = $httpOnly;

      return $this;
   }

   /**
    * @return string The URL path the cookie is valid for.
    */
   public function getPath() {
      return $this->path;
   }

   /**
    * @param string $path The URL path the cookie is valid for.
    *
    * @return Cookie This instance for further usage.
    */
   public function setPath($path) {
      $this->path = $path;

      return $this;
   }

   /**
    * @return boolean True in case the cookie should be sent over secure connection only.
    */
   public function isSecure() {
      return $this->secure;
   }

   /**
    * @param boolean $secure True in case the cookie is only sent via a secure connection, false otherwise.
    *
    * @return Cookie This instance for further usage.
    */
   public function setSecure($secure) {
      $this->secure = $secure;

      return $this;
   }

}
