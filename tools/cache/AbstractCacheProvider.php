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

   import('tools::cache','CacheCoreObject');

   /**
    * @abstract
    * @namespace tools::cache
    * @class AbstractCacheProvider
    *
    * Interface for concrete provider implementations. To access the configuration, the provider
    * is injected the current configuration params as the attributes array.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 31.10.2008<br />
    * Version 0.2, 24.11.2008 (Refactoring due to discussions on the fusion of writers and readers.)<br />
    */
   abstract class AbstractCacheProvider extends CacheCoreObject {

      function AbstractCacheProvider(){
      }

      /**
      *  @abstract
      *  @public
      *
      *  Interface definition of the provider's write() method. Must return true on write success,
      *  otherwise false.
      *
      *  @param string $cacheFile fully qualified cache file name
      *  @param object $object desired object to serialize
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      */
      function write($cacheKey,$object){
         return true;
       // end function
      }

      /**
      *  @abstract
      *  @public
      *
      *  Interface definition of the provider's read() method. Must return the desired cache
      *  content on success, otherwise null.
      *
      *  @param string $cacheFile fully qualified cache file name
      *  @return string $content desired cache content
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      */
      function read($cacheKey){
         return null;
       // end function
      }

      /**
      *  @abstract
      *  @public
      *  @since 0.2
      *
      *  Interface definition of the provider's clear() method. Delete the dedicated cache item
      *  specified by the cache key, or the whole namespace, if the cache key is null. Returns true
      *  on success and false otherwise.
      *
      *  @param string $cacheKey the cache key or null
      *  @return string $result true|false
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.11.2008<br />
      */
      function clear($cacheKey = null){
         return true;
       // end function
      }

    // end class
   }
?>