<?php
   import('tools::cache::writer','TextCacheWriter');


   /**
   *  @class ObjectCacheWriter
   *
   *  Implements the cache writer for serialized php objects.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 11.11.2008<br />
   *  Version 0.2, 23.11.2008 (The writer now inherits from the TextCacheWriter because of same functionalities)<br />
   */
   class ObjectCacheWriter extends TextCacheWriter
   {

      function ObjectCacheWriter(){
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
      */
      function write($cacheKey,$object){

         $fH = fopen($this->__getCacheFile($cacheKey),'w+');
         fwrite($fH,serialize($object));
         fclose($fH);
         return true;

       // end function
      }

    // end class
   }
?>