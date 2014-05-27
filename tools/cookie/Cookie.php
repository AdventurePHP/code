<?php
namespace APF\tools\cookie;

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

/**
 * @package APF\tools\cookie
 * @class Cookie
 *
 * The Cookie is a tool, that provides sophisticated cookie handling. The methods included allow you to
 * create, update and delete cookies using a clean API. Usage:
 * <pre>$c = new Cookie('my_cookie');
 * $c->setValue('my_value');
 * $c->delete();</pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.11.2008<br />
 * Version 0.2, 10.01.2009 (Finished implementation and testing)<br />
 */
class Cookie {

   /**
    * @const string Default path of the Cookie.
    */
   const DEFAULT_PATH = '/';

   /**
    * @const int Default expiration time of the cookie (=1 day).
    */
   const DEFAULT_EXPIRATION_TIME = 86400;

   /**
    * @var string The name of the cookie.
    */
   protected $name;

   /**
    * @var string The domain the cookie is valid for.
    */
   protected $domain;

   /**
    * @var string $path The path the cookie s valid for.
    */
   protected $path;

   /**
    * @var bool True in case the cookie is only valid for HTTPS transmission, false otherwise.
    */
   protected $secure = false;

   /**
    * @var bool True in case the cookie can only be modified via HTTP, false otherwise.
    */
   protected $httpOnly = false;

   /**
    * @var int Defines the default expiration time in seconds.
    */
   protected $expireTime;

   /**
    * @public
    *
    * Let's you create a Cookie.
    *
    * @param string $name The name of the cookie.
    * @param int $expireTime The life time in seconds (default: 1 day).
    * @param string $domain The domain the cookie is valid for.
    * @param string $path The path the cookie s valid for.
    * @param bool $secure True in case the cookie is only valid for HTTPS transmission, false otherwise.
    * @param bool $httpOnly True in case the cookie can only be modified via HTTP, false otherwise.
    *
    * @throws \InvalidArgumentException In case of an empty cookie name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.11.2008<br />
    * Version 0.2, 13.06.2013 (Refactoring to real domain object representation)<br />
    */
   public function __construct($name, $expireTime = self::DEFAULT_EXPIRATION_TIME, $domain = null, $path = null, $secure = null, $httpOnly = null) {

      if (empty($name)) {
         throw new \InvalidArgumentException('Cookie cannot be created with an empty name!');
      }

      $this->name = $name;
      $this->path = $path === null ? self::DEFAULT_PATH : $path;
      $this->domain = $domain === null ? $_SERVER['HTTP_HOST'] : $domain;
      $this->secure = $secure;
      $this->httpOnly = $httpOnly;
      $this->expireTime = $expireTime;
   }

   /**
    * @public
    *
    * Defines the value of the cookie.
    *
    * @param string $value The value of the cookie,
    *
    * @return bool True, if cookie was set correctly, false, if something was wrong
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.11.2008<br />
    * Version 0.2, 13.06.2013 (Refactoring to real domain object representation)<br />
    */
   public function setValue($value) {
      return setcookie($this->name, $value, $this->expireTime, $this->path, $this->domain, $this->secure, $this->httpOnly);
   }

   /**
    * @public
    *
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
    * @public
    *
    * Deletes the Cookie.
    *
    * @return bool True in case the operation has been successful, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.11.2008<br />
    * Version 0.2, 10.01.2009 (Added namespace support)<br />
    * Version 0.3, 13.06.2013 (Refactoring to real domain object representation)<br />
    * Version 0.4, 27.05.2014 (Removed path and domain for delete as this causes issues with some browsers)<br />
    */
   public function delete() {
      return setcookie($this->name, false, time() - self::DEFAULT_EXPIRATION_TIME);
   }

}
