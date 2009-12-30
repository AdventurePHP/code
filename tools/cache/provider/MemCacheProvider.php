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
   *  @package tools::cache::provider
   *  @class MemCacheProvider
   *
   *  Implements the cache reader for serialized php objects stored in the memcached server.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   */
   class MemCacheProvider extends AbstractCacheProvider
   {

      /**
      *  @protected
      *  Remembers the cache keys within a certain namespace to be able to clear a whole
      *  namespace.
      */
      protected $__CacheKeyStore = array();


      function MemCacheProvider(){
      }


      /**
      *  @public
      *
      *  Returns the desired cache content or null in case of failure.
      *
      *  @param string $cacheKey the application's cache key
      *  @return object $object desired object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      *  Version 0.2, 22.11.2008 (Refactoring due to global changes)<br />
      */
      function read($cacheKey){

         // get connection
         $mem = $this->__getMemcacheConnection();

         if($mem === false){
            return null;
          // end if
         }
         else{

            $namespace = $this->__getCacheConfigAttribute('Cache.Namespace');
            $cacheContent = $mem->get($namespace.'_'.$cacheKey);
            $mem->close();

            if($cacheContent !== false){

               $unserialized = @unserialize($cacheContent);

               if($unserialized !== false){
                  return $unserialized;
                // end if
               }
               else{
                  return null;
                // end else
               }

             // end if
            }
            else{
               return null;
             // end else
            }

          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Returns the desired cache content or null in case of failure.
      *
      *  @param string $cacheKey the application's cache key
      *  @param string $cacheFile fully qualified cache file name
      *  @param object $object desired object to serialize
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      *  Version 0.2, 23.11.2008 (Adapted to the new reader/writer strategy)<br />
      *  Version 0.3, 24.11.2008 (Refactored due to provider introduction.)<br />
      */
      function write($cacheKey,$object){

         // get memcache connection
         $mem = $this->__getMemcacheConnection();

         if($mem === false){
            return null;
          // end if
         }
         else{

            // write to cache (try to replace all the time)
            $namespace = $this->__getCacheConfigAttribute('Cache.Namespace');
            $namespace = $namespace.'_'.$cacheKey;
            $serialized = @serialize($object);

            if($serialized !== false){

               // remember current namespace and key
               $this->__CacheKeyStore[$namespace][] = $cacheKey;

               // try to replace
               $replace_result = $mem->replace($namespace,$serialized);

               if($replace_result !== true){
                  $store_result = $mem->set($namespace,$serialized);
                  $mem->close();
                  return $store_result;
                // end if
               }
               else{
                  $mem->close();
                  return true;
                // end else
               }

             // end if
            }
            else{
               return false;
             // end else
            }

          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Implements the abstract provider's cache cleaning method.
      *
      *  @param string $cacheKey the cache key or null
      *  @return string $result true|false
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.11.2008<br />
      */
      function clear($cacheKey = null){

         // get memcache connection
         $mem = $this->__getMemcacheConnection();

         if($mem === false){
            return false;
          // end if
         }
         else{

            // clear cache
            $namespace = $this->__getCacheConfigAttribute('Cache.Namespace');
            if($cacheKey === null){

               if(isset($this->__CacheKeyStore[$namespace])){

                  foreach($this->__CacheKeyStore[$namespace] as $cacheKey){
                     $mem->delete($namespace.'_'.$cacheKey);
                   // end foreach
                  }

                // end if
               }

               $mem->close();
               return true;

             // end if
            }
            else{
               $status = $mem->delete($namespace.'_'.$cacheKey);
               $mem->close();
               return $status;
             // end else
            }

          // end else
         }

       // end function
      }


      /**
      *  @protected
      *
      *  Returns the memcache connection (instance of PHP's Memcache class).
      *
      *  @return Memcache $mem the desired memcache connection.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.11.2008 (Refactored due to provider introduction.)<br />
      */
      protected function __getMemcacheConnection(){

         // get configuration params
         $host = $this->__getCacheConfigAttribute('Cache.Host');
         $port = $this->__getCacheConfigAttribute('Cache.Port');
         $pconn = $this->__getCacheConfigAttribute('Cache.PersistentConnect');

         // initialize memcache connection
         $mem = new Memcache();
         if($pconn == 'true'){
            $type = 'pconnect';
          // end if
         }
         else{
            $type = 'connect';
          // end else
         }

         $status = $mem->$type($host,$port);

         if($status === false){
            return false;
          // end if
         }
         else{
            return $mem;
          // end else
         }

       // end function
      }

    // end class
   }
?>