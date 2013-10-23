<?php
namespace APF\core\logging\writer;

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
use APF\core\database\AbstractDatabaseHandler;
use APF\core\database\ConnectionManager;
use APF\core\logging\entry\SimpleLogEntry;
use APF\core\logging\LogWriter;
use APF\core\pagecontroller\APFObject;

/**
 * @package APF\core\logging\writer
 * @class DatabaseLogWriter
 *
 * Implements a log writer to persist the applied entries to a database table.
 * <p/>
 * In order to correctly write the log entries, please create a table with the
 * following DDL:
 * <code>
 * CREATE TABLE IF NOT EXISTS `{$this->logTable}` (
 *    `target` varchar(10) NOT NULL default '',
 *    `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
 *    `severity` varchar(10) NOT NULL default '',
 *    `message` text NOT NULL
 * );
 * </code>
 * The fields are filled as follows:
 * <ul>
 * <li>target: contains the log target this log writer instance is registered with (e.g. <em>db</em>)</li>
 * <li>timestamp: a <em>yy-mm-dd hh:ii:ss</em> timestamp retrieved from the log entry</li>
 * <li>severity: the severity of the log entry</li>
 * <li>message: the log entry's message</li>
 * </ul>
 * Additionally, you may define a primary key to efficiently select entries from
 * the database.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.01.2013<br />
 */
class DatabaseLogWriter extends APFObject implements LogWriter {

   /**
    * @var string The log target identifier.
    */
   protected $target;

   /**
    * @var string The database connection name.
    */
   protected $connectionName = null;

   /**
    * @var string The name of the table to write the log entries to.
    */
   protected $logTable;

   /**
    * @public
    *
    * Initializes the DatabaseLogWriter with it's database connection name.
    *
    * @param string $connectionName The database connection name.
    * @param string $logTable The name of the table to write the log entries to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2013<br />
    */
   public function __construct($connectionName, $logTable) {
      $this->connectionName = $connectionName;
      $this->logTable = $logTable;
   }

   /**
    * @public
    *
    * Let's you set the database connection name that refers to a configuration
    * section within the <em>config/core/database/{CONTEXT}/{ENVIRONMENT}_connections.ini</em>
    * configuration file. For details, please refer to the database configuration chapter
    * of the <em>ConnectionManager</em>.
    *
    * @param string $connectionName The database connection name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2013<br />
    */
   public function setConnectionName($connectionName) {
      $this->connectionName = $connectionName;
   }

   /**
    * @public
    *
    * Let's you set the database table name the log entries are written to.
    *
    * @param string $logTable The name of the table to write the log entries to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2013<br />
    */
   public function setLogTable($logTable) {
      $this->logTable = $logTable;
   }

   /**
    * @return AbstractDatabaseHandler The configured database connection.
    */
   private function &getDatabaseConnection() {
      /* @var $cM ConnectionManager */
      $cM = & $this->getServiceObject('APF\core\database\ConnectionManager');
      return $cM->getConnection($this->connectionName);
   }

   public function writeLogEntries(array $entries) {

      $conn = & $this->getDatabaseConnection();

      // flush log entries to the table
      foreach ($entries as $entry) {

         /* @var $entry SimpleLogEntry */
         $insert = 'INSERT INTO `' . $this->logTable . '` (
                        `target`,
                        `timestamp`,
                        `severity`,
                        `message`
                     ) VALUES (
                        \'' . $this->target . '\',
                        \'' . $entry->getDate() . ' ' . $entry->getTime() . '\',
                        \'' . $entry->getSeverity() . '\',
                        \'' . $entry->getMessage() . '\'
                     );';
         $conn->executeTextStatement($insert);
      }
   }

   public function setTarget($target) {
      $this->target = $target;
   }

}
