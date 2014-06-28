<?php
namespace APF\core\database;

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
use APF\core\logging\LogEntry;

/**
 * This class implements a connection handler for the ConnectionManager
 * to use with pdo interface.
 *
 * @author Tobias Lückel (megger)
 * @version
 * Version 0.1, 11.04.2012<br />
 */
class PDOHandler extends AbstractDatabaseHandler {

   /**
    * Database type for pdo connection
    *
    * @var string $dbPDO
    */
   protected $dbPDO = null;

   /**
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function __construct() {
      $this->dbLogTarget = 'pdo';
   }

   public function init($initParam) {

      // set database type for pdo connection
      if (isset($initParam['PDO'])) {
         $this->dbPDO = $initParam['PDO'];
      }

      parent::init($initParam);
   }

   /**
    * Provides internal service to open a database connection.
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   protected function connect() {

      // get dsn based on the configuration
      $dsn = $this->getDSN();

      // log dsn if debugging is active
      if ($this->dbDebug === true) {
         $this->dbLog->logEntry($this->dbLogTarget, '[PDOHandler::connect()] Current DSN: ' . $dsn, LogEntry::SEVERITY_DEBUG);
      }

      // connect to database
      $this->dbConn = new \PDO($dsn, $this->dbUser, $this->dbPass);

      // switch errormode of PDO to exceptions
      $this->dbConn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

      // configure client connection
      $this->initCharsetAndCollation();
   }

   /**
    * Provides internal service to close a database connection.
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   protected function close() {
      $this->dbConn = null;
   }

   /**
    * Turns off autocommit mode! Changes to the database via PDO are not
    * committed until calling commit().
    * rollBack() will roll back all changes and turns on the autocommit mode!
    *
    * @return boolean
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function beginTransaction() {
      return $this->dbConn->beginTransaction();
   }

   /**
    * Commits a transaction and turns on the autocommit mode!
    *
    * @return boolean
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function commit() {
      return $this->dbConn->commit();
   }

   /**
    * Rolls back the current transaction
    *
    * @return boolean
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function rollBack() {
      return $this->dbConn->rollBack();
   }

   /**
    * Checks if a transaction is active
    *
    * @return boolean
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function inTransaction() {
      return $this->dbConn->inTransaction();
   }

   /**
    * Prepares a statement for execution and returns a PDOStatement object
    *
    * @param string $statement The statement string
    *
    * @return \PDOStatement A PDOStatement object to work with
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function prepareStatement($statement) {
      return $this->dbConn->prepare($statement);
   }

   /**
    * Executes a statement, located within a statement file. The place holders contained in the
    * file are replaced by the given values.
    *
    * @param string $namespace Namespace of the statement file.
    * @param string $statementFile Name of the statement file (file body!).
    * @param string[] $params A list of statement parameters.
    * @param bool $logStatement Indicates, if the statement is logged for debug purposes.
    *
    * @return \PDOStatement A PDOStatement object to work with.
    * @throws DatabaseHandlerException In case the statement execution failed.
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function executeStatement($namespace, $statementFile, array $params = array(), $logStatement = false) {
      // load statement file content
      $statement = $this->getPreparedStatement($namespace, $statementFile, $params);

      // log statements in debug mode or when requested explicitly
      if ($this->dbDebug == true || $logStatement == true) {
         $this->dbLog->logEntry($this->dbLogTarget, '[PDOHandler::executeStatement()] Current statement: ' . $statement, LogEntry::SEVERITY_DEBUG);
      }

      // prepare statement for execution
      $pdoStatement = $this->dbConn->prepare($statement);

      // check if the statement was executed
      if (!$pdoStatement || !$pdoStatement->execute()) {
         $errorInfo = $this->dbConn->errorInfo();
         $message = '(' . $errorInfo[0] . '/' . $errorInfo[1] . ') ' . $errorInfo[2] . ' (Statement: ' . $statement . ')';
         $this->dbLog->logEntry($this->dbLogTarget, $message, LogEntry::SEVERITY_DEBUG);
         throw new DatabaseHandlerException('[PDOHandler::executeStatement()] ' . $message);
      }

      // track $lastInsertId for further usage
      $this->lastInsertId = $this->dbConn->lastInsertId();

      return $pdoStatement;
   }

   /**
    * Executes a statement applied as a string to the method and returns the
    * result pointer.
    *
    * @param string $statement The statement string.
    * @param boolean $logStatement Indicates, whether the given statement should be
    *                              logged for debug purposes.
    *
    * @return \PDOStatement A PDOStatement object to work with.
    * @throws DatabaseHandlerException In case the statement execution failed.
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function executeTextStatement($statement, $logStatement = false) {
      // log statements in debug mode or when requested explicitly
      if ($this->dbDebug == true || $logStatement == true) {
         $this->dbLog->logEntry($this->dbLogTarget, '[PDOHandler::executeTextStatement()] Current statement: ' . $statement, LogEntry::SEVERITY_DEBUG);
      }

      // prepare statement for execution
      $pdoStatement = $this->dbConn->prepare($statement);

      // check if statement was executed
      if (!$pdoStatement || !$pdoStatement->execute()) {
         $errorInfo = $this->dbConn->errorInfo();
         $message = '(' . $errorInfo[0] . '/' . $errorInfo[1] . ') ' . $errorInfo[2] . ' (Statement: ' . $statement . ')';
         $this->dbLog->logEntry($this->dbLogTarget, $message, LogEntry::SEVERITY_ERROR);
         throw new DatabaseHandlerException('[PDOHandler::executeStatement()] ' . $message);
      }

      // track $lastInsertId for further usage
      $this->lastInsertId = $this->dbConn->lastInsertId();

      return $pdoStatement;
   }

   /**
    * Fetches a record from the database using the given PDOStatement.
    *
    * @param \PDOStatement $pdoStatement The PDOStatement returned by executeStatement() or executeTextStatement().
    * @param int $type The type the returned data should have. Use the static *_FETCH_MODE constants.
    *
    * @return string[] The associative result array. Returns false if no row was found.
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function fetchData($pdoStatement, $type = self::ASSOC_FETCH_MODE) {
      $return = null;
      switch ($type) {
         case self::ASSOC_FETCH_MODE:
            $return = $pdoStatement->fetch(\PDO::FETCH_ASSOC);
            break;
         case self::OBJECT_FETCH_MODE:
            $return = $pdoStatement->fetch(\PDO::FETCH_OBJ);
            break;
         case self::NUMERIC_FETCH_MODE:
            $return = $pdoStatement->fetch(\PDO::FETCH_NUM);
            break;
      }
      if ($return == null) {
         return false;
      }

      return $return;
   }

   /**
    * Escapes given values to be SQL injection save.
    *
    * @param string $value The un-escaped value.
    *
    * @return string The escaped string.
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function escapeValue($value) {
      $quoted = $this->dbConn->quote($value);

      return substr($quoted, 1, strlen($quoted) - 2);
   }

   /**
    * Returns the amount of rows, that are affected by a previous update or delete call.
    *
    * @param \PDOStatement $pdoStatement The PDOStatement.
    *
    * @return int The number of affected rows.
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function getAffectedRows($pdoStatement) {
      return $pdoStatement->rowCount();
   }

   /**
    * Returns the number of selected rows by the given PDOStatement.
    * Some databases may return the number of rows returned by a select statement.
    * However, this behaviour is not guaranteed for all databases and
    * should not be relied on for portable applications.
    *
    * @param \PDOStatement $pdoStatement The PDOStatement.
    *
    * @return int The number of selected rows.
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function getNumRows($pdoStatement) {
      return $pdoStatement->rowCount();
   }

   /**
    * Returns the data source name (DSN) for the database connection.
    * The string is build bases on the configuration parameter 'PDO'
    * Actual following db drivers are supported:
    *  - mysql(i)
    *
    * @return string
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   private function getDSN() {
      $dsn = '';
      switch (strtolower($this->dbPDO)) {
         case 'mysql':
         case 'mysqli':
            if (isset($this->dbSocket) && $this->dbSocket != '') {
               $dsn = 'mysql:unix_socket=' . $this->dbSocket;
            } else {
               $dsn = 'mysql:host=';
               if (isset($this->dbHost)) {
                  $dsn .= $this->dbHost;
               } else {
                  $dsn .= 'localhost';
               }
               if ($this->dbPort !== null) {
                  $dsn .= ';port=' . $this->dbPort;
               }
               if ($this->dbCharset !== null) {
                  $dsn .= ';charset=' . $this->dbCharset;
               }
            }
            $dsn .= ';dbname=' . $this->dbName;
            break;
      }

      return $dsn;
   }
}
