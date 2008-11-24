<?php
   /**
   *  @abstract
   *  @package tools::cache
   *  @class CacheCoreObject
   *
   *  Implements an abstact base class for the cache provider and the cache manager. Includes
   *  a generic access method to the cache configuration attributes.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 24.11.2008<br />
   */
   class CacheCoreObject extends coreObject
   {

      /**
      *  @private
      *
      *  Returns the value of the cache config attribute or triggers an error (FATAL), if the
      *  attribute is not given within the attributes array.
      *
      *  @param string $name name of the desired attribute
      *  @return string $value value of the attribute
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.11.2008<br />
      */
      function __getCacheConfigAttribute($name){

         if(!isset($this->__Attributes[$name])){
            $reg = &Singleton::getInstance('Registry');
            $env = $reg->retrieve('apf::core','Environment');
            trigger_error('['.get_class($this).'::__getCacheConfigAttribute()] The configuration directive "'.$name.'" is not present or empty. Please check your cache configuration ("'.$env.'_cacheconfig.ini") for namespace "tools::cache" and context "'.$this->__Context.'" or consult the documentation!',E_USER_ERROR);
            exit();
          // end if
         }
         else{
            return $this->__Attributes[$name];
          // end else
         }

       // end function
      }

    // end class
   }


   /**
   *  @abstract
   *  @package tools::cache
   *  @class AbstractCacheProvider
   *
   *  Interface for concrete provider implementations. To access the configuration, the provider
   *  is injected the current configuration params as the attributes array.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   *  Version 0.2, 24.11.2008 (Refactoring due to discussions on the fusion of writers and readers.)<br />
   */
   class AbstractCacheProvider extends CacheCoreObject
   {

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


   /**
   *  @package tools::cache
   *  @class CacheManager
   *
   *  Implements the cache manager component. Due to the generic implementation, all forms of
   *  caches can be implemented. For this reason, various targets and cache types are supported
   *  by the included reader and writer concept. For application examples, please refere to the
   *  online documentation. The configuration is accessible from the outside via the getAttribute()
   *  method.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   */
   class CacheManager extends CacheCoreObject
   {

      /**
      *  @private
      *  The current cache provider.
      */
      var $__Provider = null;


      /**
      *  @private
      *  Indicates, if the cache is active. Can be influenced by set('Active') or the
      *  cache configuration file. The cache is off by default to avoid strange behavior, if the
      *  config value is not set properly.
      */
      var $__Active = false;


      function CacheManager(){
      }


      /**
      *  @public
      *
      *  Implements the init() method used by the service manager. Initializes the cache
      *  manager with the corresponding cache configuration section.
      *
      *  @param array $section the desired cache config section
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.11.2008<br />
      *  Version 0.2, 22.11.2008 (Refactored due to fabric introduction.)<br />
      *  Version 0.3, 24.11.2008 (Refactoring due to discussions on the fusion of writers and readers)<br />
      */
      function init($configSection){

         // injects the config section
         $this->__Attributes = $configSection;

         // include and create the provider
         $namespace = $this->__getCacheConfigAttribute('Cache.Provider.Namespace');
         $class = $this->__getCacheConfigAttribute('Cache.Provider.Class');
         import($namespace,$class);
         $this->__Provider = $this->__getServiceObject($namespace,$class,'NORMAL');
         $this->__Provider->setAttributes($configSection);

         // map the active configuration key
         $active = $this->__getCacheConfigAttribute('Cache.Active');
         if($active == 'true'){
            $this->__Active = true;
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Returns the content from the cache. If the content is not found in cache,
      *  the reader returns null.
      *
      *  @param string $cacheKey the application's cache key
      *  @return void $cacheContent the cache content concerning the reader implementation
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.11.2008<br />
      */
      function getFromCache($cacheKey){

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
      *  @public
      *
      *  Writes the desired content to the cache.
      *
      *  @param string $cacheKey the application's cache key
      *  @param void $cacheContent the content to cache
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.11.2008<br />
      */
      function writeToCache($cacheKey,$content){

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
      *  @public
      *
      *  Clears the whole cache in case the cache key is null, or the cache item specified  by
      *  the given cache key.
      *
      *  @param string $cacheKey the application's cache key
      *  @param bool $status true in case of success, otherwise false
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.11.2008<br />
      */
      function clearCache($cacheKey = null){
         return $this->__Provider->clear($cacheKey);
       // end function
      }

    // end class
   }


   /**
   *  @package tools::cache
   *  @class CacheManagerFabric
   *
   *  Fabric for the cache manager. Must be created singleton using the service manager.
   *  Returns a cache manager instance by providing the desired config section.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 22.11.2008<br />
   */
   class CacheManagerFabric extends coreObject
   {

      /**
      *  @private
      *  Contains the cache manager instances.
      */
      var $__CacheManagerCache = array();


      function CacheManagerFabric(){
      }


      /**
      *  @public
      *
      *  Returns the cache manager instance by the desired config section.
      *
      *  @param string $configSection the config section
      *  @return CacheManager $cacheManager the desired cache manager instance
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 22.11.2008<br />
      */
      function &getCacheManager($configSection){

         $cacheKey = md5($configSection);
         if(!isset($this->__CacheManagerCache[$cacheKey])){

            // load config
            $config = &$this->__getConfiguration('tools::cache','cacheconfig');
            $section = $config->getSection($configSection);

            if($section === null){
               $reg = &Singleton::getInstance('Registry');
               $env = $reg->retrieve('apf::core','Environment');
               trigger_error('[CacheManagerFabric::getCacheManager()] The desired config section "'.$configSection.'" does not exist within the cache configuration. Please check your cache configuration ("'.$env.'_cacheconfig.ini") for namespace "tools::cache" and context "'.$this->__Context.'"!',E_USER_ERROR);
               exit();
             // end if
            }

            // create cache manager
            $this->__CacheManagerCache[$cacheKey] = &$this->__getAndInitServiceObject('tools::cache','CacheManager',$section);

          // end if
         }

         return $this->__CacheManagerCache[$cacheKey];

       // end function
      }

    // function
   }
?>