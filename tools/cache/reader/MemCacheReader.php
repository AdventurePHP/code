<?php
   /**
   *  @class MemCacheReader
   *
   *  Implements the cache reader for serialized php objects.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   *  Version 0.2, 23.11.2008 (The reader now inherits from the TextCacheReader because of same functionalities)<br />
   */
   class MemCacheReader extends AbstractCacheReader
   {

      function MemCacheReader(){
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

         if($conn_success !== true){
            return null;
          // end if
         }
         else{

            $cacheContent = $mem->get($namespace.'/'.$cacheKey);
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

    // end class
   }
?>