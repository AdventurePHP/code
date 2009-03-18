<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('core::database','connectionManager');


   /**
   *  @class DBCacheProvider
   *  @namespace tools::cache::provider
   *
   *  Implements the cache reader for normal content to the database.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 23.11.2008<br />
   */
   class DBCacheProvider extends AbstractCacheProvider
   {

      function DBCacheProvider(){
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
         $namespace = $this->__getCacheConfigAttribute('Cache.Namespace');
         $tableName = $this->__getCacheConfigAttribute('Cache.Table');

         // initialize database connection
         $db = &$this->__getDatabaseConnection();

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
         $namespace = $this->__getCacheConfigAttribute('Cache.Namespace');
         $tableName = $this->__getCacheConfigAttribute('Cache.Table');

         // initialize database connection
         $db = &$this->__getDatabaseConnection();

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


      /**
      *  @public
      *
      *  Implements the abstract provider's cache cleaning method.
      *
      *  @param string $cacheKey the cache key or null
      *  @return string $result true|false
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.11.2008<br />
      */
      function clear($cacheKey = null){

         // get configuration params
         $namespace = $this->__getCacheConfigAttribute('Cache.Namespace');
         $tableName = $this->__getCacheConfigAttribute('Cache.Table');

         // initialize database connection
         $db = &$this->__getDatabaseConnection();

         if($cacheKey === null){
            $delete = 'DELETE FROM `'.$tableName.'`
                       WHERE `namespace` = \''.$namespace.'\';';
          // end if
         }
         else{
            $delete = 'DELETE FROM `'.$tableName.'`
                       WHERE
                          `namespace` = \''.$namespace.'\'
                          AND
                          `cachekey` = \''.$cacheKey.'\';';
          // end else
         }
         $db->executeTextStatement($delete);
         return true;

       // end function
      }


      /**
      *  @private
      *
      *  Returns the database connection need.
      *
      *  @return AbstractDatabaseHandler $conn the database connection
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.11.2008<br />
      */
      function &__getDatabaseConnection(){

         $connectionKey = $this->__getCacheConfigAttribute('Cache.Connection');
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         return $cM->getConnection($connectionKey);

       // end function
      }

    // end class
   }
?>