<?php
namespace APF\tools\cache;

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
use APF\tools\cache\CacheKey;

/**
 * @package tools::cache
 * @class CacheProvider
 *
 * Interface for concrete provider implementations. To access the configuration, the provider
 * is injected the current configuration params as the attributes array.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 31.10.2008<br />
 * Version 0.2, 24.11.2008 (Refactoring due to discussions on the fusion of writers and readers.)<br />
 * Version 0.3, 05.08.2010 (Refactoring due to cache manager extension; now real interface)<br />
 */
interface CacheProvider {

   /**
    * @public
    *
    * Interface definition of the provider's write() method. Must return true on write success,
    * otherwise false.
    *
    * @param CacheKey $cacheKey the application's cache key.
    * @param mixed $object The content to cache.
    * @return bool True in case of success, otherwise false.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 31.10.2008<br />
    */
   public function write(CacheKey $cacheKey, $object);

   /**
    * @public
    *
    * Interface definition of the provider's read() method. Must return the desired cache
    * content on success, otherwise null.
    *
    * @param CacheKey $cacheKey The application's cache key.
    * @return mixed The cache content concerning the provider implementation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 31.10.2008<br />
    */
   public function read(CacheKey $cacheKey);

   /**
    * @public
    * @since 0.2
    *
    * Interface definition of the provider's clear() method. Delete the dedicated cache item
    * specified by the cache key, or the whole namespace, if the cache key is null. Returns true
    * on success and false otherwise.
    *
    * @param CacheKey $cacheKey the application's cache key.
    * @return bool True in case of success, otherwise false.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.11.2008<br />
    */
   public function clear(CacheKey $cacheKey = null);

}
