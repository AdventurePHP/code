<?php
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
   class MemCacheWriter extends AbstractCacheWriter
   {

      function MemCacheWriter(){
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

         // get configuration params
         $host = $this->__ParentObject->getAttribute('Cache.Host');
         $port = $this->__ParentObject->getAttribute('Cache.Port');
         $namespace = $this->__ParentObject->getAttribute('Cache.Namespace');
         $pconn = $this->__ParentObject->getAttribute('Cache.PersistentConnect');

         // initialize memcache connection
         $mem = new Memcache();
         if($pconn == 'true'){
            $conn_success = $mem->pconnect($host,$port);
          // end if
         }
         else{
            $conn_success = $mem->connect($host,$port);
          // end else
         }

         // write to the cache
         if($conn_success !== true){
            return null;
          // end if
         }
         else{

            // write to cache (try to replace all the time)
            $namespace = $namespace.'/'.$cacheKey;
            $serialized = @serialize($object);

            if($serialized !== false){

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

    // end class
   }
?>