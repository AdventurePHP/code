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
namespace APF\modules\pager\data;

use APF\core\benchmark\BenchmarkTimer;
use APF\core\database\AbstractDatabaseHandler;
use APF\core\database\ConnectionManager;
use APF\core\http\mixins\GetRequestResponse;
use APF\core\http\Session;
use APF\core\pagecontroller\APFObject;
use APF\core\singleton\Singleton;

/**
 * Represents the data layer of the pager.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 06.08.2006<br />
 * Version 0.2, 19.02.2009 (Added the connection key handling)<br />
 * Version 0.3, 24.01.2009 (Added session caching to gain performance)<br />
 */
final class PagerMapper extends APFObject {

   use GetRequestResponse;

   /**
    * Defines the database connection key. Must be filled within the init() method.
    */
   protected $connectionKey = null;

   /**
    * Initializes the connection key of the mapper.
    *
    * @param string $connectionKey The database connection key.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.01.2009<br />
    */
   public function __construct($connectionKey) {
      $this->connectionKey = $connectionKey;
   }

   /**
    * Returns the number of entries of the current object.
    *
    * @param string $namespace the namespace of the statement
    * @param string $statement the name of the statement file
    * @param array $params additional params for the statement
    * @param bool $cache decides if caching is active or not (true = yes, false = no)
    *
    * @return string $entriesCount the number of entries
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.08.2006<br />
    * Version 0.2, 16.08.2006 (Added an argument for further statement params)<br />
    * Version 0.3, 19.01.2009 (Added the connection key handling)<br />
    * Version 0.4, 24.01.2009 (Added session caching)<br />
    * Version 0.5, 25.01.2009 (Removed nullpointer bug due to session object definition)<br />
    */
   public function getEntriesCount($namespace, $statement, array $params = [], $cache = true) {

      $t = Singleton::getInstance(BenchmarkTimer::class);
      /* @var $t BenchmarkTimer */
      $t->start('PagerMapper::getEntriesCount()');

      $params = $this->sanitizeParameters($params);

      // try to load the entries count from the session
      $entriesCount = null;
      $session = null;
      $sessionKey = null;
      if ($cache === true) {
         $session = $this->getSession();
         $sessionKey = $this->getSessionKey($namespace, $statement, $params) . '_EntriesCount';
         $entriesCount = $session->load($sessionKey);
      }

      // load from database if not in session
      if ($entriesCount === null) {
         $conn = $this->getConnection();
         $result = $conn->executeStatement($namespace, $statement, $params);
         $data = $conn->fetchData($result);
         $entriesCount = $data['EntriesCount'];

         // only save to session, when cache is enabled
         if ($cache === true) {
            $session->save($sessionKey, $entriesCount);
         }
      }

      $t->stop('PagerMapper::getEntriesCount()');

      return $entriesCount;
   }

   /**
    * @param array $params The list of pager statement parameters.
    *
    * @return array The sanitized list of pager statement parameters.
    */
   private function sanitizeParameters(array $params = []) {
      $conn = $this->getConnection();
      foreach ($params as $key => $value) {
         $params[$conn->escapeValue($key)] = $conn->escapeValue($value);
      }

      return $params;
   }

   /**
    * @return AbstractDatabaseHandler The current database connection.
    */
   private function &getConnection() {
      /* @var $cM ConnectionManager */
      $cM = $this->getServiceObject(ConnectionManager::class);

      return $cM->getConnection($this->connectionKey);
   }

   /**
    * @return Session
    */
   protected function getSession() {
      return $this->getRequest()->getSession(__NAMESPACE__);
   }

   /**
    * Returns the session key for the current statement and params.
    *
    * @param string $namespace namespace of the statement
    * @param string $statement name of the statement file
    * @param array $params statement params
    *
    * @return string $sessionKey the desired session key
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.01.2009<br />
    */
   protected function getSessionKey($namespace, $statement, array $params = []) {
      return 'PagerMapper_' . md5($namespace . $statement . implode('', $params));
   }

   /**
    * Returns a list of the object ids, that should be loaded for the current page.
    *
    * @param string $namespace the namespace of the statement
    * @param string $statement the name of the statement file
    * @param array $params additional params for the statement
    * @param bool $cache decides if caching is active or not (true = yes, false = no)
    *
    * @return array $entries a list of entry ids
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.08.2006<br />
    * Version 0.2, 19.01.2009 (Added the connection key handling)<br />
    * Version 0.3, 24.01.2009 (Added session caching)<br />
    * Version 0.4, 25.01.2009 (Removed null pointer bug due to session object definition)<br />
    * Version 0.5, 27.12.2010 (Bug-fix: In case of empty results, no empty objects are returned any more.)<br />
    */
   public function loadEntries($namespace, $statement, array $params = [], $cache = true) {

      $t = Singleton::getInstance(BenchmarkTimer::class);
      /* @var $t BenchmarkTimer */
      $t->start('PagerMapper::loadEntries()');

      $params = $this->sanitizeParameters($params);

      // try to load the entries count from the session
      $session = null;
      $sessionKey = null;
      if ($cache === true) {
         $session = $this->getSession();
         $sessionKey = $this->getSessionKey($namespace, $statement, $params) . '_EntryIDs';
         $entryIds = $session->load($sessionKey);
      } else {
         $entryIds = null;
      }

      // load from database if not in session
      if ($entryIds === null) {

         $conn = $this->getConnection();
         $result = $conn->executeStatement($namespace, $statement, $params);

         // map empty results to empty array
         if ($result === false) {
            return [];
         }

         $entryIds = [];
         while ($data = $conn->fetchData($result)) {
            $entryIds[] = $data['DB_ID'];
         }

         // only save to session, when cache is enabled
         if ($cache === true) {
            $session->save($sessionKey, serialize($entryIds));
         }
      } else {
         $entryIds = unserialize($entryIds);
      }

      $t->stop('PagerMapper::loadEntries()');

      return $entryIds;
   }

}
