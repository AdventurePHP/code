<?php
   import('core::filesystem','FilesystemManager');


   /**
   *  @class CacheManager
   *
   *  Implements the cache manager component. Due to the generic implementation, all forms of
   *  caches can be implemented. For this reason, various targets and cache types are supported
   *  by the included reader and writer concept. For application examples, please refere to the
   *  online documentation.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   */
   class CacheManager extends coreObject
   {

      /**
      *  @private
      *  The cache base folder.
      */
      var $__BaseFolder = '.';


      /**
      *  @private
      *  Indicates, if the cache should be used.
      */
      var $__Active = false;


      /**
      *  @private
      *  Default cache lifetime.
      */
      var $__LifeTime = 86400;


      /**
      *  @private
      *  Cache namespace within the base folder.
      */
      var $__Namespace = 'default';


      function CacheManager(){
      }


      /**
      *  @public
      *
      *  Implements the init() method used by the service manager. Initializes the cache
      *  manager with the corresponding cache configuration.
      *
      *  @param string $configKey the desired cache config section
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.11.2008<br />
      */
      function init($configKey){

         // load config
         $config = &$this->__getConfiguration('tools::cache','cacheconfig');

         // is cache active?
         $active = $config->getValue($configKey,'Cache.Active');
         if($active !== null){
            if($active == 'true'){
               $this->__Active = true;
             // end if
            }
          // end if
         }
         else{
            return true;
          // end else
         }

         // set the cache lifetime
         $lifeTime = $config->getValue($configKey,'Cache.LifeTime');
         if($lifeTime !== null && !empty($lifeTime)){
            $this->__LifeTime = (int)$lifeTime;
          // end if
         }

         // set the cache namespace
         $namespace = $config->getValue($configKey,'Cache.Namespace');
         if($namespace === null || empty($namespace)){
            trigger_error('[CacheManager::init()] The cache configuration section "'.$configKey.'" does not contain a valid "Cache.Namespace" definition. Hence, the namespace is set to "default"!',E_USER_WARNING);
          // end if
         }
         else{
            $this->__Namespace = $namespace;
          // end else
         }

         // set the base folder
         $baseFolder = $config->getValue($configKey,'Cache.BaseFolder');
         if($baseFolder === null || empty($baseFolder)){
            trigger_error('[CacheManager::init()] The cache configuration section "'.$configKey.'" does not contain a valid "Cache.BaseFolder" definition. Hence, the cache is set to inactive!',E_USER_WARNING);
            $this->__Active = false;
          // end if
         }
         else{
            $this->__BaseFolder = $baseFolder;
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Returns the content from the cache. If the content is not found in cache,
      *  the reader returns null.
      *
      *  @param AbstractCacheReader $reader the desired cache file reader
      *  @param string $cacheKey the application's cache key
      *  @return void $cacheContent the cache content concerning the reader implementation
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.11.2008<br />
      */
      function getFromCache($reader,$cacheKey){

         if($this->__Active === true){

            // lifetime feature sollte hier und _nur_ hier implementiert werden, so
            // dass es nur einmal konfiguriert werden muss und sich die reader nicht mehr
            // drum kümmern müssen. Man könnte das so machen, dass eine Cache.Lifetime = "0"
            // einfach eine ewige Lebensdauer ist.
            $cacheFile = $this->__getCacheFile($cacheKey);
            $reader->set('LifeTime',$this->__LifeTime);
            return $reader->read($cacheFile);
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Writes the desired content to the cache.
      *
      *  @param AbstractCacheWriter $writer the desired cache file writer
      *  @param string $cacheKey the application's cache key
      *  @param void $cacheContent the content to cache
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.11.2008<br />
      */
      function writeToCache($writer,$cacheKey,$content){

         if($this->__Active === true){
            $cacheFile = $this->__getCacheFile($cacheKey);
            FilesystemManager::createFolder(dirname($cacheFile));
            return $writer->write($cacheFile,$content);
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Creates the complete cache file name.
      *
      *  @param string $cacheKey the application's cache key
      *  @return string $cacheFile the cache file name
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.11.2008<br />
      */
      function __getCacheFile($cacheKey){

         $cacheKey = md5($cacheKey);
         $subFolder = substr($cacheKey,0,2);
         return $this->__BaseFolder.'/'.str_replace('::','/',$this->__Namespace).'/'.$subFolder.'/'.$cacheKey.'.apfc';

       // end function
      }

    // end class
   }


   /**
   *  @abstract
   *  @class AbstractCacheWriter
   *
   *  Interface for concrete writer implementations.
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
      *  Return true on success.
      */
      function write($cacheFile,$object){
         return true;
       // end function
      }

    // end class
   }


   /**
   *  @abstract
   *  @class AbstractCacheReader
   *
   *  Interface for concrete reader implementations. The reader must decide, if it uses
   *  the lifetime feature.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   */
   class AbstractCacheReader extends coreObject
   {


      /**
      *  @private
      *  The cache life time.
      */
      var $__LifeTime = null;


      function AbstractCacheReader(){
      }


      /**
      *  Must return null, if the content cannot be read.
      */
      function read($cacheKey){
         return null;
       // end function
      }

    // end class
   }


   /**
   *  @class ObjectCacheReader
   *
   *  Implements the cache reader for serialized php objects.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   */
   class ObjectCacheReader extends AbstractCacheReader
   {

      /**
      *  @private
      *  Indicates, if the lifetime feature shoud be used.
      */
      var $__UseLifeTime;


      /**
      *  @public
      *
      *  Initializes the reader implementation.
      *
      *  @param bool $useLifeTime indicates, if the cache lifetime should be considered (true = yes, false = no)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.11.2008<br />
      */
      function ObjectCacheReader($useLifeTime = false){
         $this->__UseLifeTime = $useLifeTime;
       // end if
      }


      /**
      *  @public
      *
      *  Returns the desired cache content or null in case of failure.
      *
      *  @param string $cacheFile fully qualified cache file name
      *  @return object $object desired object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      */
      function read($cacheFile){

         if(!file_exists($cacheFile)){
            return null;
          // end if
         }
         else{

            // initialize validity of th ecache file
            $valid = true;

            // do lifetime check
            if($this->__UseLifeTime === true){

               // get last modification time of the cache file
               $modTime = filemtime($cacheFile);
               clearstatcache();

               // calculate cache file validity
               $diff = $modTime - (time() - $this->__LifeTime);
               if($diff < 0){
                  $valid = false;
                // end if
               }

             // end if
            }

            // read from cache
            if($valid === true){

               $unserialized = @unserialize(file_get_contents($cacheFile));
               if($unserialized === false){
                  return null;
                // end if
               }
               else{
                  return $unserialized;
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

    // end class
   }


   /**
   *  @class ObjectCacheWriter
   *
   *  Implements the cache writer for serialized php objects.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 11.11.2008<br />
   */
   class ObjectCacheWriter extends AbstractCacheWriter
   {

      function ObjectCacheWriter(){
      }


      /**
      *  @public
      *
      *  Returns the desired cache content or null in case of failure.
      *
      *  @param string $cacheFile fully qualified cache file name
      *  @param object $object desired object to serialize
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      */
      function write($cacheFile,$object){

         $fH = fopen($cacheFile,'w+');
         fwrite($fH,serialize($object));
         fclose($fH);

       // end function
      }

    // end class
   }


   /**
   *  @class ObjectCacheReader
   *
   *  Implements the cache reader for serialized php objects.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   */
   class TextCacheReader extends AbstractCacheReader
   {

      function TextCacheReader(){
      }


      /**
      *  @public
      *
      *  Returns the desired cache content or null in case of failure.
      *
      *  @param string $cacheFile fully qualified cache file name
      *  @return string $content desired cache content
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      */
      function read($cacheFile){

         if(!file_exists($cacheFile)){
            return null;
          // end if
         }
         else{
            return file_get_contents($cacheFile);
          // end else
         }

       // end function
      }

    // end class
   }


   /**
   *  @class ObjectCacheWriter
   *
   *  Implements the cache writer for normal text content.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 11.11.2008<br />
   */
   class TextCacheWriter extends AbstractCacheReader
   {

      function TextCacheWriter(){
      }


      /**
      *  @public
      *
      *  Returns the desired cache content or null in case of failure.
      *
      *  @param string $cacheFile fully qualified cache file name
      *  @param string $content desired content to cache
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      */
      function write($cacheFile,$content){

         $fH = fopen($cacheFile,'w+');
         fwrite($fH,$content);
         fclose($fH);

       // end function
      }

    // end class
   }
?>