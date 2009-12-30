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

   import('tools::cache','AbstractCacheProvider');
   import('tools::cache','CacheCoreObject');
   
   /**
    * @package tools::cache
    * @class CacheManager
    *
    * Implements the cache manager component. Due to the generic implementation, all forms of
    * caches can be implemented. For this reason, various targets and cache types are supported
    * by the included reader and writer concept. For application examples, please refere to the
    * online documentation. The configuration is accessible from the outside via the getAttribute()
    * method.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 31.10.2008<br />
    */
   final class CacheManager extends CacheCoreObject {

      /**
       * @private
       * @var AbstractCacheProvider The current cache provider.
       */
      private $__Provider = null;

      /**
       * @private
       * Indicates, if the cache is active. Can be influenced by set('Active') or the
       * cache configuration file. The cache is off by default to avoid strange behavior, if the
       * config value is not set properly.
       * @var boolean True, in case the cache is active, false otherwise.
       */
      private $__Active = false;

      public function CacheManager(){
      }

      /**
       * @public
       *
       * Implements the init() method used by the service manager. Initializes the cache
       * manager with the corresponding cache configuration section.
       *
       * @param string[] $section the desired cache config section
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.11.2008<br />
       * Version 0.2, 22.11.2008 (Refactored due to fabric introduction.)<br />
       * Version 0.3, 24.11.2008 (Refactoring due to discussions on the fusion of writers and readers)<br />
       */
      public function init($initParam){

         // injects the config section
         $this->__Attributes = $initParam;

         // include and create the provider
         $namespace = $this->__getCacheConfigAttribute('Cache.Provider.Namespace');
         $class = $this->__getCacheConfigAttribute('Cache.Provider.Class');
         import($namespace,$class);
         $this->__Provider = $this->__getServiceObject($namespace,$class,'NORMAL');
         $this->__Provider->setAttributes($initParam);

         // map the active configuration key
         $active = $this->__getCacheConfigAttribute('Cache.Active');
         if($active == 'true'){
            $this->__Active = true;
          // end if
         }

       // end function
      }

      /**
       * @public
       *
       * Returns the content from the cache. If the content is not found in cache,
       * the reader returns null.
       *
       * @param string $cacheKey the application's cache key
       * @return mixed $cacheContent the cache content concerning the reader implementation
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.11.2008<br />
       */
      public function getFromCache($cacheKey){

         if($this->__Active === true){
            return $this->__Provider->read($cacheKey);
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Writes the desired content to the cache.
       *
       * @param string $cacheKey the application's cache key
       * @param mixed $cacheContent the content to cache
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.11.2008<br />
       */
      public function writeToCache($cacheKey,$content){

         if($this->__Active === true){
            return $this->__Provider->write($cacheKey,$content);
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Clears the whole cache in case the cache key is null, or the cache item specified  by
       * the given cache key.
       *
       * @param string $cacheKey the application's cache key
       * @param bool $status true in case of success, otherwise false
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.11.2008<br />
       */
      public function clearCache($cacheKey = null){
         return $this->__Provider->clear($cacheKey);
       // end function
      }

    // end class
   }
?>