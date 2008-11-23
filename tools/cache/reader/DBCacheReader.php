<?php
   import('core::database','connectionManager');


   /**
   *  @class DBCacheReader
   *
   *  Implements the cache reader for normal content to the database.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 23.11.2008<br />
   */
   class DBCacheReader extends AbstractCacheReader
   {

      function DBCacheReader(){
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
      *  Version 0.1, 23.10.2008<br />
      */
      function read($cacheKey){

         // get configuration params
         $connectionKey = $this->__ParentObject->getAttribute('Cache.Connection');
         $tableName = $this->__ParentObject->getAttribute('Cache.Table');
         $namespace = $this->__ParentObject->getAttribute('Cache.Namespace');

         // initialize database connection
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $db = &$cM->getConnection($connectionKey);

         // read from the database
         $select = 'SELECT `value` FROM `'.$tableName.'`
                    WHERE
                       `namespace` = \''.$namespace.'\'
                       AND
                       `cachekey` = \''.$cacheKey.'\';';
         $result = $db->executeTextStatement($select);
         $data = $db->fetchData($result);

         if(isset($data['value'])){
            return $data['value'];
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }

    // end class
   }
?>