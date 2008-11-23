<?php
   import('core::filesystem','FilesystemManager');


   /**
   *  @abstract
   *  @class AbstractCacheWriter
   *
   *  Interface for concrete writer implementations. To access the configuration, the reader has
   *  a reference on the cache manager within the "ParentObject" attribute.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   */
   class AbstractCacheWriter extends coreObject
   {

      function AbstractCacheWriter(){
      }


      /**
      *  @abstract
      *  @public
      *
      *  Interface definition of the writer's write() method. Must return true on write success,
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

    // end class
   }


   /**
   *  @abstract
   *  @class AbstractCacheReader
   *
   *  Interface for concrete reader implementations. To access the configuration, the reader has
   *  a reference on the cache manager within the "ParentObject" attribute.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   */
   class AbstractCacheReader extends coreObject
   {

      function AbstractCacheReader(){
      }


      /**
      *  @abstract
      *  @public
      *
      *  Interface definition of the reader's read() method. Must return the desired cache
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
   class CacheManager extends coreObject
   {

      /**
      *  @private
      *  The desired reader implementation.
      */
      var $__Reader = null;


      /**
      *  @private
      *  The desired writer implementation.
      */
      var $__Writer = null;


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
      */
      function init($section){

         // fill the current configuration
         $this->__Attributes = $section;

         // check for the default values
         $reg = &Singleton::getInstance('Registry');
         $env = $reg->retrieve('apf::core','Environment');
         $mandatoryDirectives = array(
                                      'Cache.Reader.Namespace',
                                      'Cache.Reader.Class',
                                      'Cache.Writer.Namespace',
                                      'Cache.Writer.Class',
                                      'Cache.Active'
                                     );
         foreach($mandatoryDirectives as $directive){

            if(!isset($this->__Attributes[$directive]) || empty($this->__Attributes[$directive])){
               trigger_error('[CacheManager::init()] The given config section does not contain a valid "'.$directive.'" directive. Please check your cache configuration ("'.$env.'_cacheconfig.ini") for namespace "tools::cache" and context "'.$this->__Context.'"!',E_USER_ERROR);
               exit();
             // end if
            }

          // end foreach
         }

         // include the reader and writer
         import($this->__Attributes['Cache.Reader.Namespace'],$this->__Attributes['Cache.Reader.Class']);
         import($this->__Attributes['Cache.Writer.Namespace'],$this->__Attributes['Cache.Writer.Class']);

         // create and init the reader and writer instances
         $this->__Reader = $this->__getServiceObject($this->__Attributes['Cache.Reader.Namespace'],$this->__Attributes['Cache.Reader.Class'],'NORMAL');
         $this->__Reader->setByReference('ParentObject',$this);
         $this->__Writer = $this->__getServiceObject($this->__Attributes['Cache.Writer.Namespace'],$this->__Attributes['Cache.Writer.Class'],'NORMAL');
         $this->__Writer->setByReference('ParentObject',$this);

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

         if($this->getAttribute('Cache.Active') == 'true'){
            return $this->__Reader->read($cacheKey);
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

         if($this->getAttribute('Cache.Active') == 'true'){
            return $this->__Writer->write($cacheKey,$content);
          // end if
         }
         else{
            return false;
          // end else
         }

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