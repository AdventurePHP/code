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
use APF\core\benchmark\BenchmarkTimer;
use APF\core\logging\LogEntry;
use APF\core\singleton\Singleton;

/**
 * This class implements a connection handler for the ConnectionManager
 * to use with mysqli extension.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.02.2008<br />
 */
class MySQLiHandler extends AbstractDatabaseHandler {

   /**
    * The number of rows, that are affected within a bind statement execution.
    *
    * @var int $bindNumRows
    */
   private $bindNumRows = 0;

   public function __construct() {
      $this->dbLogTarget = 'mysqli';
      $this->dbPort = '3306';
   }

   protected function connect() {

      // initiate connection
      $this->dbConn = mysqli_init();

      // as discussed under http://forum.adventure-php-framework.org/viewtopic.php?f=6&t=614
      // the mysqli extension triggers an error instead of throwing an exception. thus we have
      // to add an ugly "@" sign to convert this error into an exception. :(
      @$this->dbConn->real_connect(
            $this->dbHost,
            $this->dbUser,
            $this->dbPass,
            $this->dbName,
            $this->dbPort,
            $this->dbSocket);

      if ($this->dbConn->connect_error || mysqli_connect_error()) {
         throw new DatabaseHandlerException('[MySQLiHandler->connect()] Database connection '
               . 'could\'t be established (' . mysqli_connect_errno($this->dbConn) . ': '
               . mysqli_connect_error($this->dbConn) . ')!', E_USER_ERROR);
      }

      // configure client connection
      $this->initCharsetAndCollation();

      if ($this->dbCharset !== null) {
         if (!$this->dbConn->set_charset($this->dbCharset)) {
            throw new DatabaseHandlerException(
                  '[MySQLiHandler->connect()] Error loading character set ' . $this->dbCharset .
                  ' (' . $this->dbConn->error . ')!'
            );
         }
      }

   }

   protected function close() {
      if (!$this->dbConn->close()) {
         throw new DatabaseHandlerException('[MySQLiHandler->close()] An error occurred during closing of the '
               . 'database connection (' . mysqli_errno($this->dbConn) . ': ' . mysqli_error($this->dbConn) . ')!',
               E_USER_WARNING);
      }
      $this->dbConn = null;
   }

   /**
    * Executes a statement stored within a statement file.
    *
    * @param string $namespace Namespace of the statement file.
    * @param string $statementFile Name of the statement file (file body!).
    * @param string[] $params A list of statement parameters.
    * @param bool $logStatement Indicates, if the statement is logged for debug purposes.
    *
    * @return \MySQLi_Result The result of the statement executed.
    * @throws DatabaseHandlerException In case of any database related exception (e.g. statement
    *                                  syntax error or bind problems).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.03.2010<br />
    */
   public function executeStatement($namespace, $statementFile, array $params = array(), $logStatement = false) {

      // load statement file content
      $statement = $this->getPreparedStatement($namespace, $statementFile, $params);

      // log statements in debug mode or when requested explicitly
      if ($this->dbDebug == true || $logStatement == true) {
         $this->dbLog->logEntry($this->dbLogTarget, '[MySQLiHandler::executeStatement()] Current statement: ' . $statement, LogEntry::SEVERITY_DEBUG);
      }

      // execute the statement with use of the current connection!
      $this->dbConn->real_query($statement);

      // bug 547: map public connection properties to variables, to throw an exception for
      // illegal statements. otherwise, empty() will always return true.
      $error = $this->dbConn->error;
      $errno = $this->dbConn->errno;

      // get current error to be able to do error handling
      if (!empty($error) || !empty($errno)) {
         $message = '(' . $errno . ') ' . $error . ' (Statement: ' . $statement . ')';
         $this->dbLog->logEntry($this->dbLogTarget, $message, LogEntry::SEVERITY_ERROR);
         throw new DatabaseHandlerException('[MySQLiHandler->executeStatement()] ' . $message);
      }

      // track $lastInsertId for further usage
      $this->lastInsertId = $this->dbConn->insert_id;

      return $this->dbConn->store_result();
   }

   /**
    * Executes a statement with bind param support located within a statement file. The place
    * holders contained in the file are replaced by the given values.
    *
    * @param string $namespace Namespace of the statement file.
    * @param string $statementFile Name of the statement file (filebody!).
    * @param string[] $params A list of statement parameters.
    * @param bool $logStatement Indicates, if the statement is logged for debug purposes.
    *
    * @return string[] The resulting rows of the database call within one single array.
    * @throws DatabaseHandlerException In case of any database related exception (e.g. statement
    *                                  syntax error or bind problems).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.03.2010<br />
    */
   public function executeBindStatement($namespace, $statementFile, array $params = array(), $logStatement = false) {

      // load statement file content (params will be replaced "manually")
      $statement = $this->getPreparedStatement($namespace, $statementFile);

      // place holder setting is a bit tricky here, because the bind params
      // must be present in the order defined in the statement. Thus we must
      // re-order the $params array.
      /* @var $t BenchmarkTimer */
      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
      $statementId = md5($statement);
      $id = $statementId . ' re-order bind params';
      $t->start($id);
      preg_match_all('/\[([A-Za-z0-9_\-]+)\]/u', $statement, $matches, PREG_SET_ORDER);

      $sortedParams = array();
      $bindVariablesAvailable = (count($matches) > 0);

      // binding variables is only necessary, if we have dynamic statement
      // params defined within the statement file!
      if ($bindVariablesAvailable) {
         foreach ($matches as $stmtParam) {
            $stmtParamName = $stmtParam[1];
            if (isset($params[$stmtParamName])) {

               // add param to sorted array
               $sortedParams[$stmtParamName] = $params[$stmtParamName];

               // replace for binding
               $statement = str_replace('[' . $stmtParamName . ']', '?', $statement);
            }
         }
      }

      $t->stop($id);

      // log statements in debug mode or when requested explicitly
      if ($this->dbDebug == true || $logStatement == true) {
         $this->dbLog->logEntry($this->dbLogTarget, '[MySQLiHandler::executeBindStatement()] Current statement: ' . $statement, LogEntry::SEVERITY_DEBUG);
      }

      // bind params
      $id = $statementId . ' prepare statement';
      $t->start($id);
      $query = $this->createQuery($statement);
      $t->stop($id);

      $id = $statementId . ' bind params';
      $t->start($id);
      // binding variables is only necessary, if we have dynamic statement
      // params defined within the statement file!
      if ($bindVariablesAvailable) {
         $this->bindParams($query, $sortedParams);
      }
      $t->stop($id);

      // execute statement
      $id = $statementId . ' execute';
      $t->start($id);
      $query->execute();

      // track $lastInsertId fur further usage
      $this->lastInsertId = $query->insert_id;

      $t->stop($id);

      // fetch the result set using the meta data returned by the query
      $id = $statementId . ' fetch';
      $t->start($id);
      $statementResult = $this->fetchBindResult($query);
      $t->stop($id);

      // remember affected rows
      $this->bindNumRows = $query->num_rows;

      $query->free_result();
      $query->close();

      return $statementResult;
   }

   /**
    * Executes a statement provided using mysqli's bind feature.
    *
    * @param string $statement The statement to execute.
    * @param array $params A list of statement parameters.
    * @param boolean $logStatement Indicates, if the statement is logged for debug purposes.
    *
    * @return resource The execution result.
    * @throws DatabaseHandlerException In case the bind param mapping is wrong.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.03.2010<br />
    */
   public function executeTextBindStatement($statement, array $params = array(), $logStatement = false) {

      /* @var $t BenchmarkTimer */
      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
      $statementId = md5($statement);

      // prepare statement
      $id = $statementId . ' prepare statement';
      $t->start($id);
      $query = $this->createQuery($statement);
      $t->stop($id);

      // bind params
      $id = $statementId . ' bind params';
      $t->start($id);

      // additional check, because bind param count errors do not result in an exception!
      $paramStatementCount = substr_count($statement, '?');
      $paramCount = count($params);
      if ($paramStatementCount != $paramCount) {
         throw new DatabaseHandlerException('[MySQLiHandler->executeTextBindStatement()] Number '
               . 'of given params (' . $paramCount . ') does not match number of bind params '
               . 'within the statement (' . $paramStatementCount . ')! Current statement: '
               . $statement, E_USER_ERROR);
      }

      $this->bindParams($query, $params);
      $t->stop($id);

      // log statements in debug mode or when requested explicitly
      if ($this->dbDebug === true || $logStatement === true) {
         $this->dbLog->logEntry($this->dbLogTarget, '[MySQLiHandler::executeTextBindStatement()] Current statement: ' . $statement, LogEntry::SEVERITY_DEBUG);
      }

      // execute statement
      $id = $statementId . ' execute';
      $t->start($id);
      $query->execute();

      // track $lastInsertId fur further usage
      $this->lastInsertId = $query->insert_id;

      $t->stop($id);

      // fetch the result set using the meta data returned by the query
      $id = $statementId . ' fetch';
      $t->start($id);
      $statementResult = $this->fetchBindResult($query);
      $t->stop($id);

      // remember affected rows
      $this->bindNumRows = $query->num_rows;

      $query->free_result();
      $query->close();

      return $statementResult;
   }

   /**
    * @return int The number of rows affected by a statement execution.
    */
   public function getBindNumRows() {
      return $this->bindNumRows;
   }

   /**
    * Creates a MySQLi statement representation by the given statement string.
    *
    * @param string $statement The statement to create the statement instance of.
    *
    * @return \MYSQLi_STMT The desired statement instance.
    * @throws DatabaseHandlerException In case of any statement errors.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.03.2010<br />
    */
   private function createQuery($statement) {
      $query = $this->dbConn->prepare($statement);

      // bug 547: map public connection properties to variables, to throw an exception for
      // illegal statements. otherwise, empty() will always return true.
      $error = $this->dbConn->error;
      $errno = $this->dbConn->errno;

      if ($query === false || !empty($error) || !empty($errno)) {
         throw new DatabaseHandlerException($error, $errno);
      }

      return $query;
   }

   /**
    * Binds the given params to the presented prepared statement.
    *
    * @param \MYSQLi_STMT $query The prepared query to bind the params to.
    * @param string[] $params A list of statement parameters.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.03.2010<br />
    */
   private function bindParams(&$query, array $params) {

      // don't bind params in case we have none!
      if (count($params) == 0) {
         return;
      }

      $binds = array();
      foreach ($params as $key => $DUMMY) {
         // Bug 694: bind via reference to avoid "expected to be a reference, value given in" errors.
         $binds[] = & $params[$key];
      }
      call_user_func_array(
            array(&$query, 'bind_param'),
            array_merge(
                  array(str_repeat('s', count($params))),
                  $binds
            )
      );
   }

   /**
    * Fetches the result from a prepared query.
    *
    * @param \MYSQLi_STMT $query The prepared query to fetch the result from.
    *
    * @return string[] The result array or null in case we have no result.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.03.2010<br />
    */
   private function fetchBindResult(\mysqli_stmt $query) {

      $result = null;

      do {

         $metaData = $query->result_metadata();

         // in case the meta data is not present (e.g. for INSERT statements),
         // we cannot fetch any data. thus we return null to indicate no result
         if ($metaData === false) {
            break;
         }

         $resultRow = array();
         $resultParams = array();
         while ($field = $metaData->fetch_field()) {
            $resultParams[] = & $resultRow[$field->name];
         }

         $bindResult = array();

         call_user_func_array(array(&$query, 'bind_result'), $resultParams);
         while ($query->fetch()) {
            $currentRow = array();
            foreach ($resultRow as $key => $val) {
               $currentRow[$key] = $val;
            }
            $bindResult[] = $currentRow;
         }
         $result[] = $bindResult;
      } while ($query->more_results() && $query->next_result()); // for sprocs

      return (count($result) === 1) ? $result[0] : $result;
   }


   /**
    * Quotes data for use in MySQL statements.
    *
    * @param string $value String to quote.
    *
    * @return string Quoted string.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.02.2010<br />
    */
   public function escapeValue($value) {
      return $this->dbConn->real_escape_string($value);
   }

   /**
    * Fetches a record from the database using the given result resource.
    *
    * @param resource $resultCursor The result resource returned by executeStatement() or executeTextStatement().
    * @param int $type The type the returned data should have. Use the static *_FETCH_MODE constants.
    *
    * @return string[] The associative result array. Returns false if no row was found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.03.2010<br />
    * Version 0.2, 30.07.2010 (Changed return value to false if no row was found, in order to follow the interface definition)<br />
    * Version 0.3, 08.08.2010 (Added optional second parameter) <br />
    */
   public function fetchData($resultCursor, $type = self::ASSOC_FETCH_MODE) {
      if ($resultCursor == null) {
         return false;
      }

      if ($type === self::ASSOC_FETCH_MODE) {
         $return = $resultCursor->fetch_assoc();
      } elseif ($type === self::OBJECT_FETCH_MODE) {
         $return = $resultCursor->fetch_object();
      } else {
         $return = $resultCursor->fetch_row();
      }

      if ($return === null) {
         return false;
      }

      return $return;
   }

   /**
    * Sets the data pointer to the given offset using the result resource.
    *
    * @param resource $result The statement execution result.
    * @param int $offset The row number offset.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.03.2010<br />
    */
   public function setDataPointer($result, $offset) {
      @mysqli_data_seek($result, $offset);
   }

   /**
    * Returns the amount of rows, that are affected by a previous update or delete call.
    *
    * @param resource $resultCursor The result resource pointer.
    *
    * @return int The number of affected rows.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.03.2010<br />
    */
   public function getAffectedRows($resultCursor) {
      return mysqli_affected_rows($this->dbConn);
   }

   /**
    * Returns the number of selected rows by the given result resource.
    *
    * @param resource $result the mysql result resource.
    *
    * @return int The number of selected rows.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.03.2010<br />
    * Version 0.2, 01.12.2010 (The mysqli lib returns false for selects on empty tables. This causes an error with mysqli_num_rows() using the GORM!)<br />
    */
   public function getNumRows($result) {
      return $result === false ? 0 : mysqli_num_rows($result);
   }

   /**
    * Executes a statement applied as a string to the method and returns the
    * result pointer.
    *
    * @param string $statement The statement to execute.
    * @param boolean $logStatement Indicates, whether the given statement should be
    *                              logged for debug purposes.
    *
    * @return resource The database result resource.
    * @throws DatabaseHandlerException In case of any database error.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.03.2010<br />
    */
   public function executeTextStatement($statement, $logStatement = false) {

      // log statements in debug mode or when requested explicitly
      if ($this->dbDebug == true || $logStatement == true) {
         $this->dbLog->logEntry($this->dbLogTarget, '[MySQLiHandler::executeTextStatement()] Current statement: ' . $statement, LogEntry::SEVERITY_DEBUG);
      }

      // execute the statement with use of the current connection!
      $this->dbConn->real_query($statement);

      // bug 547: map public connection properties to variables, to throw an exception for
      // illegal statements. otherwise, empty() will always return true.
      $error = $this->dbConn->error;
      $errno = $this->dbConn->errno;

      if (!empty($error) || !empty($errno)) {
         $message = '(' . $errno . ') ' . $error . ' (Statement: ' . $statement . ')';
         $this->dbLog->logEntry($this->dbLogTarget, $message, LogEntry::SEVERITY_ERROR);
         throw new DatabaseHandlerException('[MySQLiHandler->executeTextStatement()] ' . $message);
      }

      // track $lastInsertId for further usage
      $this->lastInsertId = $this->dbConn->insert_id;

      return $this->dbConn->store_result();
   }

   /**
    * Returns the version of the database server.
    *
    * @return string The mysql server information.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.03.2010<br />
    */
   public function getServerInfo() {
      return mysqli_get_server_info($this->dbConn);
   }

   public function getHostInfo() {
      return mysqli_get_host_info($this->dbConn);
   }

   /**
    * Returns the name of the current database.
    *
    * @return string The database name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.03.2010<br />
    */
   public function getDatabaseName() {
      return $this->dbName;
   }

}
