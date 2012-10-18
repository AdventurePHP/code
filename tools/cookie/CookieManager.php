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

/**
 * @package tools::cookie
 * @class CookieManager
 *
 * The CookieManager is a tool, that provides sophisticated cookie handling. It features
 * namespaces for usage in multi-application environments. The methods included allow you to
 * create, update and delete cookies within different namespaces. Usage:
 * <pre>$cM = new CookieManager('my::namespace');
 * $cM->createCookie('my_param','my_value');
 * $cM->updateCookie('my_param','my_value_2');
 * $cM->deleteCookie('my_param');</pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.11.2008<br />
 * Version 0.2, 10.01.2009 (Finished implementation and testing)<br />
 */
class CookieManager {

   /**
    * @var string Namespace of the current instance.
    */
   protected $namespace = 'apf_cookies_default';

   /**
    * @var int Defines the default expiration time in seconds (1 day).
    */
   protected $expireTime = 86400;

   /**
    * @public
    *
    * Constructor of the CookieManager. Allows to set the namespace.
    *
    * @author Christian Achatz
    * @version
    *  Version 0.1, 08.11.2008<br />
    */
   public function __construct($namespace = null) {
      if ($namespace !== null) {
         $this->setNamespace($namespace);
      }
   }

   /**
    * @public
    *
    * Sets the namespace of the current CookieManager instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.11.2008<br />
    */
   public function setNamespace($namespace) {
      $this->namespace = str_replace('::', '_', $namespace);
   }

   /**
    * @public
    *
    * Returns the namespace of the current CookieManager instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.01.2009<br />
    */
   public function getNamespace() {
      return str_replace('_', '::', $this->namespace);
   }

   /**
    * @public
    *
    * Creates a cookie within the current namespace.
    *
    * @param string $key desired cookie key
    * @param string $value the value of the cookie
    * @param int $expire the expiration time delta (=from now) in seconds
    * @param string $domain the domain, the cookie is valid for
    * @param string $path the path, the cookie is valid for
    * @return bool True, if cookie was set correctly, false, if something was wrong
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.11.2008<br />
    */
   public function createCookie($key, $value, $expire = null, $domain = null, $path = null) {

      // set default expiration if not given as an argument
      if ($expire === null) {
         $expire = $this->getDefaultExpireTime();
      }

      // set default domain (=current) if not given as an argument
      if ($domain === null) {
         $domain = $this->getDefaultDomain();
      }

      // set default path if not given as an argument
      if ($path === null) {
         $path = $this->getDefaultPath();
      }

      return setcookie($this->namespace . '__' . $key, $value, $expire, $path, $domain);
   }

   /**
    * @public
    *
    * Updates an existing cookie within the current namespace.
    *
    * @param string $key desired cookie key
    * @param string $value the value of the cookie
    * @param int $expire the expiration time delta (=from now) in seconds
    * @param string $domain the domain, the cookie is valid for
    * @param string $path the path, the cookie is valid for
    * @return bool $success true, if cookie was set correctly, false, if something was wrong
    *
    * @author Christian Achatz
    * @version
    *  Version 0.1, 10.01.2009<br />
    */
   public function updateCookie($key, $value, $expire = null, $domain = null, $path = null) {
      return $this->createCookie($key, $value, $expire, $domain, $path);
   }

   /**
    * @public
    *
    *  Returns the value of the desired key within the current namespace.
    *
    * @param string $key desired cookie key
    * @return string $value cookie value or null
    *
    * @author Christian Achatz
    * @version
    *  Version 0.1, 08.11.2008<br />
    *  Version 0.2, 10.01.2009 (Added namespace support)<br />
    */
   public function readCookie($key) {

      if (isset($_COOKIE[$this->namespace . '__' . $key])) {
         return $_COOKIE[$this->namespace . '__' . $key];
      } else {
         return null;
      }
   }

   /**
    * @public
    *
    * Returns the value of the desired key within the current namespace
    *
    * @param string $key Desired cookie key.
    * @param string $domain The domain of the cookie to delete.
    * @param string $path The path of the cookie to delete.
    * @return bool True in case the operation has been successful, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.11.2008<br />
    * Version 0.2, 10.01.2009 (Added namespace support)<br />
    */
   public function deleteCookie($key, $domain = null, $path = null) {

      // set default domain if not given as an argument
      if ($domain === null) {
         $domain = $this->getDefaultDomain();
      }

      // set default path if not given as an argument
      if ($path === null) {
         $path = $this->getDefaultPath();
      }

      return setcookie($this->namespace . '__' . $key, false, time() - 3600, $path, $domain);
   }

   /**
    * @protected
    *
    * Returns the default domain
    *
    * @return string $defaultDomain the default domain
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.01.2009<br />
    */
   protected function getDefaultDomain() {
      return $_SERVER['HTTP_HOST'];
   }

   /**
    * @protected
    *
    * Returns the default path.
    *
    * @return string $defaultPath the default path
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.01.2009<br />
    */
   protected function getDefaultPath() {
      return str_replace(basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['PHP_SELF']);
   }

   /**
    * @protected
    *
    * Returns the default expire timestamp
    *
    * @return string $defaultExpireTime the default expire time
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.01.2009<br />
    */
   protected function getDefaultExpireTime() {
      return time() + $this->expireTime;
   }

}
