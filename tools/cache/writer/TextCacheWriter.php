<?php
   /**
   *  @class TextCacheWriter
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
      *  Writes the desired text content to cache.
      *
      *  @param string $cacheFile fully qualified cache file name
      *  @param string $content desired content to cache
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      *  Version 0.2, 23.11.2008 (Adapted to the new reader/writer strategy)<br />
      */
      function write($cacheKey,$content){

         $fH = fopen($this->__getCacheFile($cacheKey),'w+');
         fwrite($fH,$content);
         fclose($fH);
         return true;

       // end function
      }


      /**
      *  @private
      *
      *  Prepares the cache file name and creates the cache folder.
      *
      *  @param string $cacheKey the cache key
      *  @return string $cacheFile fully qualified cache file name
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.11.2008<br />
      */
      function __getCacheFile($cacheKey){

         // get cache file name
         $reader = &$this->__ParentObject->getByReference('Reader');
         $cacheFile = $reader->getCacheFile($cacheKey);

         // create folder structure
         FilesystemManager::createFolder(dirname($cacheFile));

         return $cacheFile;

       // end function
      }

    // end class
   }
?>