<?php
   import('tools::cache::reader','TextCacheReader');


   /**
   *  @class ObjectCacheReader
   *
   *  Implements the cache reader for serialized php objects.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   *  Version 0.2, 23.11.2008 (The reader now inherits from the TextCacheReader because of same functionalities)<br />
   */
   class ObjectCacheReader extends TextCacheReader
   {

      function ObjectCacheReader(){
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

         $cacheFile = $this->getCacheFile($cacheKey);
         if(!file_exists($cacheFile)){
            return null;
          // end if
         }
         else{

            $unserialized = @unserialize(file_get_contents($cacheFile));
            if($unserialized === false){
               return null;
             // end if
            }
            else{
               return $unserialized;
             // end else
            }

          // end else
         }

       // end function
      }

    // end class
   }
?>