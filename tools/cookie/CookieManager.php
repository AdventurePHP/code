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

   /**
   *  @namespace tools::cookie
   *  @class CookieManager
   *
   *  The CookieManager is a tool, that provides sophisticated cookie handling. It features
   *  namespaces for usage in multi-application environments. The methods included allow you to
   *  create, update and delete cookies within different namespaces. Usage:
   *  <pre>$cM = new CookieManager('my::namespace');
   *  $cM->createCookie('my_param','my_value');
   *  $cM->updateCookie('my_param','my_value_2');
   *  $cM->deleteCookie('my_param');</pre>
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 08.11.2008<br />
   *  Version 0.2, 10.01.2009 (Finished implementation and testing)<br />
   */
   class CookieManager
   {

      /**
      *  @protected
      *  Namespace of the current instance.
      */
      protected $__Namespace = 'apf_cookies_default';


      /**
      *  @protected
      *  Defines the default expiration time in seconds (1 day).
      */
      protected $__ExpireTime = 86400;


      /**
      *  @public
      *
      *  Constructor of the CookieManager. Allows to set the namespace.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.11.2008<br />
      */
      function CookieManager($namespace = null){

         if($namespace !== null){
            $this->setNamespace($namespace);
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Sets the namespace of the current CookieManager instance.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.11.2008<br />
      */
      function setNamespace($namespace){
         $this->__Namespace = str_replace('::','_',$namespace);
       // end function
      }


      /**
      *  @public
      *
      *  Returns the namespace of the current CookieManager instance.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.01.2009<br />
      */
      function getNamespace(){
         return str_replace('_','::',$this->__Namespace);
       // end function
      }


      /**
      *  @public
      *
      *  Creates a cookie within the current namespace.
      *
      *  @param string $key desired cookie key
      *  @param string $value the value of the cookie
      *  @param int $expire the expiration time delta (=from now) in seconds
      *  @param string $domain the domain, the cookie is valid for
      *  @param string $path the path, the cookie is valid for
      *  @return bool $success true, if cookie was set correctly, false, if something was wrong
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.11.2008<br />
      */
      function createCookie($key,$value,$expire = null,$domain = null,$path = null){

         // set default expiration if not given as an argument
         if($expire === null){
            $expire = $this->__getDefaultExpireTime();
          // end if
         }

         // set default domain (=current) if not given as an argument
         if($domain === null){
            $domain = $this->__getDefaultDomain();
          // end if
         }

         // set default path if not given as an argument
         if($path === null){
            $path = $this->__getDefaultPath();
          // end if
         }

         // call setcookie and return the result
         return setcookie($this->__Namespace.'__'.$key,$value,$expire,$path,$domain);

       // end function
      }


      /**
      *  @public
      *
      *  Updates an existing cookie within the current namespace.
      *
      *  @param string $key desired cookie key
      *  @param string $value the value of the cookie
      *  @param int $expire the expiration time delta (=from now) in seconds
      *  @param string $domain the domain, the cookie is valid for
      *  @param string $path the path, the cookie is valid for
      *  @return bool $success true, if cookie was set correctly, false, if something was wrong
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.01.2009<br />
      */
      function updateCookie($key,$value,$expire = null,$domain = null,$path = null){
         return $this->createCookie($key,$value,$expire,$domain,$path);
       // end function
      }


      /**
      *  @public
      *
      *  Returns the value of the desired key within the current namespace.
      *
      *  @param string $key desired cookie key
      *  @return string $value cookie value or null
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.11.2008<br />
      *  Version 0.2, 10.01.2009 (Added namespace support)<br />
      */
      function readCookie($key){

         if(isset($_COOKIE[$this->__Namespace.'__'.$key])){
            return $_COOKIE[$this->__Namespace.'__'.$key];
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Returns the value of the desired key within the current namespace
      *
      *  @param string $key desired cookie key
      *  @return string $value cookie value or null
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.11.2008<br />
      *  Version 0.2, 10.01.2009 (Added namespace support)<br />
      */
      function deleteCookie($key,$domain = null,$path = null){

         // set default domain if not given as an argument
         if($domain === null){
            $domain = $this->__getDefaultDomain();
          // end if
         }

         // set default path if not given as an argument
         if($path === null){
            $path = $this->__getDefaultPath();
          // end if
         }

         // delete the cookie
         return setcookie($this->__Namespace.'__'.$key,false,time() - 3600,$path,$domain);

       // end function
      }


      /**
      *  @protected
      *
      *  Returns the default domain
      *
      *  @return string $defaultDomain the default domain
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.01.2009<br />
      */
      protected function __getDefaultDomain(){
         return $_SERVER['HTTP_HOST'];
       // end function
      }


      /**
      *  @protected
      *
      *  Returns the default path
      *
      *  @return string $defaultPath the default path
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.01.2009<br />
      */
      protected function __getDefaultPath(){
         return str_replace(basename($_SERVER['SCRIPT_FILENAME']),'',$_SERVER['PHP_SELF']);
       // end function
      }


      /**
      *  @protected
      *
      *  Returns the default expire timestamp
      *
      *  @return string $defaultExpireTime the default expire time
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.01.2009<br />
      */
      protected function __getDefaultExpireTime(){
         return time() + $this->__ExpireTime;
       // end function
      }

    // end class
   }
?>