<?php
   /**
   *  @class TextCacheReader
   *
   *  Implements the cache reader for normal text content.
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
      function read($cacheKey){

         $cacheFile = $this->getCacheFile($cacheKey);
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


      /**
      *  @public
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
      function getCacheFile($cacheKey){

         $cacheKey = md5($cacheKey);
         $subFolder = substr($cacheKey,0,2);
         $baseFolder = $this->__ParentObject->getAttribute('Cache.BaseFolder');
         $namespace = $this->__ParentObject->getAttribute('Cache.Namespace');
         return $baseFolder.'/'.str_replace('::','/',$namespace).'/'.$subFolder.'/'.$cacheKey.'.apfc';

       // end function
      }

    // end class
   }
?>