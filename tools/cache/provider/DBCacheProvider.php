<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   /**
    * @package tools::cache::provider
    * @class DBCacheProvider
    *
    * Implements the cache reader for normal content to the database.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.11.2008<br />
    */
   class DBCacheProvider extends CacheBase implements CacheProvider {

      public function read(CacheKey $cacheKey){

         // get configuration params
         $namespace = $this->getConfigAttribute('Cache.Namespace');
         $tableName = $this->getConfigAttribute('Cache.Table');

         // initialize database connection
         $db = &$this->getDatabaseConnection();

         // read from the database
         $select = 'SELECT `value` FROM `'.$tableName.'`
                    WHERE
                       `namespace` = \''.$namespace.'\'
                       AND
                       `cachekey` = \''.$cacheKey->getKey().'\';';
         $result = $db->executeTextStatement($select);
         $data = $db->fetchData($result);

         return isset($data['value']) ? $data['value'] : null;

       // end function
      }

      public function write(CacheKey $cacheKey,$object){

         // get configuration params
         $namespace = $this->getConfigAttribute('Cache.Namespace');
         $tableName = $this->getConfigAttribute('Cache.Table');

         // initialize database connection
         $db = &$this->getDatabaseConnection();

         // insert into the the database
         $select = 'SELECT `value` FROM `'.$tableName.'`
                    WHERE
                       `namespace` = \''.$namespace.'\'
                       AND
                       `cachekey` = \''.$cacheKey->getKey().'\';';
         $result = $db->executeTextStatement($select);
         $count = $db->getNumRows($result);

         if($count > 0){
            $stmt = 'UPDATE `'.$tableName.'`
                     SET `value` = \''.$object.'\'
                     WHERE
                        `namespace` = \''.$namespace.'\'
                        AND
                        `cachekey` = \''.$cacheKey->getKey().'\';';
          // end if
         }
         else{
            $stmt = 'INSERT INTO `'.$tableName.'`
                     (`value`,`namespace`,`cachekey`)
                     VALUES
                     (\''.$object.'\',\''.$namespace.'\',\''.$cacheKey->getKey().'\');';
          // end else
         }

         $db->executeTextStatement($stmt);
         return true;

       // end function
      }

      public function clear(CacheKey $cacheKey = null){

         // get configuration params
         $namespace = $this->getConfigAttribute('Cache.Namespace');
         $tableName = $this->getConfigAttribute('Cache.Table');

         // initialize database connection
         $db = &$this->getDatabaseConnection();

         if($cacheKey === null){
            $delete = 'DELETE FROM `'.$tableName.'`
                       WHERE `namespace` = \''.$namespace.'\';';
         }
         else{
            $delete = 'DELETE FROM `'.$tableName.'`
                       WHERE
                          `namespace` = \''.$namespace.'\'
                          AND
                          `cachekey` = \''.$cacheKey->getKey().'\';';
         }
         $db->executeTextStatement($delete);
         return true;

       // end function
      }

      /**
       * @protected
       *
       * Returns the database connection need.
       *
       * @return AbstractDatabaseHandler The database connection.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.11.2008<br />
       */
      protected function &getDatabaseConnection(){

         $connectionKey = $this->getConfigAttribute('Cache.Connection');
         $cM = &$this->__getServiceObject('core::database','ConnectionManager');
         return $cM->getConnection($connectionKey);

       // end function
      }

    // end class
   }
?>