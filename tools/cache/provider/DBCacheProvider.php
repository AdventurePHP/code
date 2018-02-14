<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\tools\cache\provider;

use APF\core\database\AbstractDatabaseHandler;
use APF\core\database\ConnectionManager;
use APF\tools\cache\CacheBase;
use APF\tools\cache\CacheKey;
use APF\tools\cache\CacheProvider;

/**
 * Implements the cache reader for normal content to the database.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 23.11.2008<br />
 */
class DBCacheProvider extends CacheBase implements CacheProvider {

   public function read(CacheKey $cacheKey) {

      // get configuration params
      $namespace = $this->getConfigAttribute('Namespace');
      $tableName = $this->getConfigAttribute('Table');

      // initialize database connection
      $db = $this->getDatabaseConnection();

      // read from the database
      $select = 'SELECT `value` FROM `' . $tableName . '`
                    WHERE
                       `namespace` = \'' . $namespace . '\'
                       AND
                       `cachekey` = \'' . $cacheKey->getKey() . '\';';
      $result = $db->executeTextStatement($select);
      $data = $db->fetchData($result);

      return isset($data['value']) ? $data['value'] : null;

   }

   /**
    * Returns the database connection need.
    *
    * @return AbstractDatabaseHandler The database connection.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.11.2008<br />
    */
   protected function &getDatabaseConnection() {
      $connectionKey = $this->getConfigAttribute('Connection');
      /* @var $cM ConnectionManager */
      $cM = $this->getServiceObject(ConnectionManager::class);

      return $cM->getConnection($connectionKey);
   }

   public function write(CacheKey $cacheKey, $object) {

      // get configuration params
      $namespace = $this->getConfigAttribute('Namespace');
      $tableName = $this->getConfigAttribute('Table');

      // initialize database connection
      $db = $this->getDatabaseConnection();

      // insert into the the database
      $select = 'SELECT `value` FROM `' . $tableName . '`
                    WHERE
                       `namespace` = \'' . $namespace . '\'
                       AND
                       `cachekey` = \'' . $cacheKey->getKey() . '\';';
      $result = $db->executeTextStatement($select);
      $count = $db->getNumRows($result);

      if ($count > 0) {
         $stmt = 'UPDATE `' . $tableName . '`
                     SET `value` = \'' . $object . '\'
                     WHERE
                        `namespace` = \'' . $namespace . '\'
                        AND
                        `cachekey` = \'' . $cacheKey->getKey() . '\';';
      } else {
         $stmt = 'INSERT INTO `' . $tableName . '`
                     (`value`,`namespace`,`cachekey`)
                     VALUES
                     (\'' . $object . '\',\'' . $namespace . '\',\'' . $cacheKey->getKey() . '\');';
      }

      $db->executeTextStatement($stmt);

      return true;

   }

   public function clear(CacheKey $cacheKey = null) {

      // get configuration params
      $namespace = $this->getConfigAttribute('Namespace');
      $tableName = $this->getConfigAttribute('Table');

      // initialize database connection
      $db = $this->getDatabaseConnection();

      if ($cacheKey === null) {
         $delete = 'DELETE FROM `' . $tableName . '`
                       WHERE `namespace` = \'' . $namespace . '\';';
      } else {
         $delete = 'DELETE FROM `' . $tableName . '`
                       WHERE
                          `namespace` = \'' . $namespace . '\'
                          AND
                          `cachekey` = \'' . $cacheKey->getKey() . '\';';
      }
      $db->executeTextStatement($delete);

      return true;

   }

}
