<?php
   import('core::database','connectionManager');


   /**
   *  @class DBCacheWriter
   *
   *  Implements the cache writer for normal content to the database.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 23.11.2008<br />
   */
   class DBCacheWriter extends AbstractCacheWriter
   {

      function DBCacheWriter(){
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
      *  Version 0.1, 23.11.2008<br />
      */
      function write($cacheKey,$object){

         // get configuration params
         $connectionKey = $this->__ParentObject->getAttribute('Cache.Connection');
         $tableName = $this->__ParentObject->getAttribute('Cache.Table');
         $namespace = $this->__ParentObject->getAttribute('Cache.Namespace');

         // initialize database connection
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $db = &$cM->getConnection($connectionKey);

         // insert into the the database
         $select = 'SELECT `value` FROM `'.$tableName.'`
                    WHERE
                       `namespace` = \''.$namespace.'\'
                       AND
                       `cachekey` = \''.$cacheKey.'\';';
         $result = $db->executeTextStatement($select);
         $count = $db->getNumRows($result);

         if($count > 0){
            $stmt = 'UPDATE `'.$tableName.'`
                     SET `value` = \''.$object.'\'
                     WHERE
                        `namespace` = \''.$namespace.'\'
                        AND
                        `cachekey` = \''.$cacheKey.'\';';
          // end if
         }
         else{
            $stmt = 'INSERT INTO `'.$tableName.'`
                     (`value`,`namespace`,`cachekey`)
                     VALUES
                     (\''.$object.'\',\''.$namespace.'\',\''.$cacheKey.'\');';
          // end else
         }

         $db->executeTextStatement($stmt);
         return true;

       // end function
      }

    // end class
   }
?>