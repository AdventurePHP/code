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
 * @package tools::cache::provider
 * @class MemCacheProvider
 *
 * Implements the cache reader for serialized php objects stored in the memcached server.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 31.10.2008<br />
 */
class MemCacheProvider extends CacheBase implements CacheProvider {

   /**
    * @protected
    *  Remembers the cache keys within a certain namespace to be able to clear a whole
    *  namespace.
    */
   protected $cacheKeyStore = array();

   public function read(CacheKey $cacheKey) {

      // get connection
      $mem = $this->getMemCacheConnection();

      if ($mem === false) {
         return null;
      } else {

         $namespace = $this->getConfigAttribute('Cache.Namespace');
         $cacheContent = $mem->get($namespace . '_' . $cacheKey->getKey());
         $mem->close();

         if ($cacheContent !== false) {

            $unserialized = @unserialize($cacheContent);

            if ($unserialized !== false) {
               return $unserialized;
            } else {
               return null;
            }

         } else {
            return null;
         }

      }

   }

   public function write(CacheKey $cacheKey, $object) {

      // get memcache connection
      $mem = $this->getMemCacheConnection();

      if ($mem === false) {
         return null;
      } else {

         // write to cache (try to replace all the time)
         $namespace = $this->getConfigAttribute('Cache.Namespace');
         $namespace = $namespace . '_' . $cacheKey->getKey();
         $serialized = @serialize($object);

         if ($serialized !== false) {

            // remember current namespace and key
            $this->cacheKeyStore[$namespace][] = $cacheKey->getKey();

            // try to replace
            $replace_result = $mem->replace($namespace, $serialized);

            if ($replace_result !== true) {
               $store_result = $mem->set($namespace, $serialized);
               $mem->close();
               return $store_result;
            } else {
               $mem->close();
               return true;
            }

         } else {
            return false;
         }

      }

   }

   public function clear(CacheKey $cacheKey = null) {

      // get memcache connection
      $mem = $this->getMemCacheConnection();

      if ($mem === false) {
         return false;
      } else {

         // clear cache
         $namespace = $this->getConfigAttribute('Cache.Namespace');
         if ($cacheKey === null) {

            if (isset($this->cacheKeyStore[$namespace])) {

               foreach ($this->cacheKeyStore[$namespace] as $key) {
                  $mem->delete($namespace . '_' . $key);
               }

            }

            $mem->close();
            return true;

         } else {
            $status = $mem->delete($namespace . '_' . $cacheKey->getKey());
            $mem->close();
            return $status;
         }

      }

   }

   /**
    * @protected
    *
    * Returns the memcache connection (instance of PHP's Memcache class).
    *
    * @return Memcache The desired memcache connection.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.11.2008 (Refactored due to provider introduction.)<br />
    */
   protected function getMemCacheConnection() {

      // get configuration params
      $host = $this->getConfigAttribute('Cache.Host');
      $port = $this->getConfigAttribute('Cache.Port');
      $pconn = $this->getConfigAttribute('Cache.PersistentConnect');

      // initialize memcache connection
      $mem = new Memcache();
      if ($pconn == 'true') {
         $type = 'pconnect';
      } else {
         $type = 'connect';
      }

      $status = $mem->$type($host, $port);

      if ($status === false) {
         return false;
      } else {
         return $mem;
      }

   }

}
