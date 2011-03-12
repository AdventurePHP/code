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
import('core::database', 'AbstractDatabaseHandler');
import('core::database', 'DatabaseHandlerException');

/**
 * @package core::database
 * @class SQLiteHandler
 *
 * Implementiert die Datenbankabstraktionsschicht f�r die SQLite-Schnittstelle.<br />
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 23.02.2008<br />
 */
class SQLiteHandler extends AbstractDatabaseHandler {

   /**
    * @protected
    * @var int File system permission mode of the database.
    */
   protected $__dbMode = 0666;
   
   /**
    * @protected
    * @var string Error tracking container for SQLite errors.
    */
   protected $__dbError = null;

   public function __construct() {
      $this->__dbLogFileName = 'sqlite';
   }

   /**
    * @protected
    *
    * Implements the connect method to create a connection to the desired sqlite database.
    *
    * @throws DatabaseHandlerException In case the database connection cannot be established.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.02.2008<br />
    */
   protected function __connect() {

      $this->__dbConn = @sqlite_open($this->__dbName, $this->__dbMode, $this->__dbError);

      if (!is_resource($this->__dbConn)) {
         throw new DatabaseHandlerException('[SQLiteHandler->__connect()] Database "'
                 . $this->__dbName . '" cannot be opened! Message: ' . $this->__dbError, E_USER_ERROR);
      }
   }

   /**
    * @protected
    *
    * Implements the close method for the sqlite database.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.02.2008<br />
    */
   protected function __close() {
      @sqlite_close($this->__dbConn);
      $this->__dbConn = null;
   }

   /**
    * @public
    *
    * Executes a statement applied as a string to the method and returns the
    * result pointer.
    *
    * @param string $statement The statement string.
    * @param boolean $logStatement Inidcates, whether the given statement should be
    *                              logged for debug purposes.
    * @return resource The database result resource.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.02.2008<br />
    */
   public function executeTextStatement($statement, $logStatement = false) {

      if ($logStatement == true) {
         $this->__dbLog->logEntry($this->__dbLogFileName,
                 '[SQLiteHandler::executeTextStatement()] Current statement: ' . $statement,
                 'DEBUG');
      }

      $result = sqlite_query($this->__dbConn, $statement);

      if ($result === false) {
         $message = sqlite_error_string(sqlite_last_error($this->__dbConn));
         $message .= ' (Statement: ' . $statement . ')';
         $this->__dbLog->logEntry($this->__dbLogFileName, $message, 'ERROR');
      }

      // remember last insert id for futher usage
      $this->__lastInsertID = sqlite_last_insert_rowid($this->__dbConn);

      return $result;
   }

   /**
    * @public
    *
    * Executes a statement, located within a statement file. The place holders contained in the
    * file are replaced by the given values.
    *
    * @param string $namespace Namespace of the statement file.
    * @param string $statementName Name of the statement file (filebody!).
    * @param string[] $params A list of statement parameters.
    * @param bool $logStatement Indicates, if the statement is logged for debug purposes.
    * @return resource The database result resource.
    * @throws DatabaseHandlerException In case the statement file does not exist.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.02.2008<br />
    * Version 0.2, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
    */
   public function executeStatement($namespace, $statementName, $params = array(), $logStatement = false) {

      $statement = $this->getPreparedStatement($namespace, $statementName, $params);

      if ($logStatement == true) {
         $this->__dbLog->logEntry($this->__dbLogFileName,
                 '[SQLiteHandler::executeTextStatement()] Current statement: ' . $statement,
                 'DEBUG');
      }

      $result = sqlite_query($this->__dbConn, $statement);

      if ($result === false) {
         $message = sqlite_error_string(sqlite_last_error($this->__dbConn));
         $message .= ' (Statement: ' . $statement . ')';
         $this->__dbLog->logEntry($this->__dbLogFileName, $message, 'ERROR');
      }

      // remember last insert id for futher usage
      $this->__lastInsertID = sqlite_last_insert_rowid($this->__dbConn);

      return $result;
   }

   /**
    * @public
    *
    * Fetches a record from the database using the given result resource.
    *
    * @param resource $resultCursor The result resource returned by executeStatement() or executeTextStatement().
    * @param int $type The type the returned data should have. Use the static *_FETCH_MODE constants.
    * @return string[] The associative result array.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.09.2009<br />
    * Version 0.2, 08.08.2010 (Added optional second parameter) <br />
    */
   public function fetchData($resultCursor, $type = self::ASSOC_FETCH_MODE) {
      if ($type === self::ASSOC_FETCH_MODE) {
         return sqlite_fetch_array($resultCursor, SQLITE_ASSOC);
      } elseif ($type === self::OBJECT_FETCH_MODE) {
         return sqlite_fetch_object($resultCursor);
      } else {
         return sqlite_fetch_array($resultCursor, SQLITE_NUM);
      }
   }

   /**
    * @public
    *
    * Escapes given values to be SQL injection save.
    *
    * @param string $value The unescaped value.
    * @return string The escapted string.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.02.2008<br />
    */
   public function escapeValue($value) {
      return sqlite_escape_string($value);
   }

   /**
    * @public
    *
    * Returns the amount of rows, that are affected by a previous update or delete call.
    *
    * @param resource $resultCursor The result resource pointer.
    * @return int The number of affected rows.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.02.2008<br />
    */
   public function getAffectedRows($resultCursor) {
      return sqlite_num_rows($resultCursor);
   }

   /**
    * @public
    *
    * Returns the number of selected rows by the given result resource.
    *
    * @param $result The sqlite result resource.
    * @return int The number of selected rows.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.03.2011 (Added missing method.)<br />
    */
   public function getNumRows($result) {
      return sqlite_num_rows($result);
   }

}
?>