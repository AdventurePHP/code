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

   import('core::database','AbstractDatabaseHandler');
   import('core::database','DatabaseHandlerException');

   /**
    * @package core::database
    * @class MySQLxHandler
    *
    * This class implements a connection handler for the ConnectionManager to use with mysql
    * databases using the mysql extension.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.02.2008<br />
    */
   class MySQLxHandler extends AbstractDatabaseHandler {

      public function __construct() {
         $this->__dbLogFileName = 'mysqlx';
      }

      /**
       * @protected
       *
       * Initiates the database connectio and preselects the desired database.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 2002<br />
       * Version 0.2, 2002<br />
       * Version 0.3, 2002<br />
       * Version 0.4, 10.04.2004<br />
       * Version 0.5, 04.12.2005<br />
       * Version 0.6, 24.12.2005<br />
       * Version 0.7, 04.01.2005<br />
       * Version 0.8, 09.10.2008 (Removed the @ before mysql_connect to get a more detailed in case of connection errors)<br />
       * Version 0.9, 18.03.2009 (Bugfix: create a new connection, even if the connection data is the same. This otherwise may result in interference of connections, that use different databases.)<br />
       */
      protected function __connect(){

         // initiate connection
         $this->__dbConn = mysql_connect($this->__dbHost,$this->__dbUser,$this->__dbPass,true);

         if(!is_resource($this->__dbConn)){
            throw new DatabaseHandlerException('[MySQLxHandler->__connect()] Database connection '
                    .'could\'t be established ('.mysql_errno().': '.mysql_error().')!',E_USER_ERROR);
         }

         // configure client connection
         $this->initCharsetAndCollation();

         // Select the database. The ugly @ sign is needed to provide nice error messages.
         $result = @mysql_select_db($this->__dbName,$this->__dbConn);

         if(!$result){
            throw new DatabaseHandlerException('[MySQLxHandler->__connect()] Database couldn\'t be selected ('.mysql_errno().': '.mysql_error().')!',E_USER_ERROR);
         }

       // end function
      }

      /**
       * @protected
       *
       * Closes the database connection.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 2002<br />
       * Version 0.2, 10.04.2004<br />
       * Version 0.3, 04.12.2005<br />
       * Version 0.4, 24.12.2005<br />
       */
      protected function __close(){

         $result = @mysql_close($this->__dbConn);
         $this->__dbConn = null;

         if(!$result){
            throw new DatabaseHandlerException('[MySQLxHandler->__close()] An error occured during '
                    .'closing of the database connection ('.mysql_errno().': '.mysql_error().')!',
                    E_USER_WARNING);
         }

       // end function
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
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 24.12.2005<br />
       * Version 0.2, 16.01.2006<br />
       * Version 0.3, 19.01.2006<br />
       * Version 0.4, 23.04.2006 (Changes due to the ApplicationManagers)<br />
       * Version 0.5, 05.08.2006 (File extension must not be present in the file name any more. Statement params are now optional.)<br />
       * Version 0.6, 05.08.2006 (Added the $showStatement param)<br />
       * Version 0.7, 29.03.2007 (Adapted implementation to the new page controller implementation)<br />
       * Version 0.8, 07.03.2008 (Bugfix: query was not executed with the right connection)<br />
       * Version 0.9, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
       * Version 1.0, 05.11.2008 (Added value escaping to the statement params)<br />
       * Version 1.1, 26.03.2009 (Enhanced the error messages)<br />
       * Version 1.2, 03.05.2009 (Forth param set to true now results in a debug log entry instead of an error)<br />
       */
      public function executeStatement($namespace,$statementFile,$params = array(),$logStatement = false){

         $statement = $this->getPreparedStatement($namespace, $statementFile, $params);

         // log statements in debug mode or when requested explicitly
         if($this->__dbDebug == true || $logStatement == true){
            $this->__dbLog->logEntry($this->__dbLogFileName,
               '[MySQLxHandler::executeStatement()] Current statement: '.$statement,
               'DEBUG');
         }

         // execute the statement with use of the current connection!
         $result = @mysql_query($statement,$this->__dbConn);

         // get current error to be able to do error handling
         $mysql_error = mysql_error($this->__dbConn);
         $mysql_errno = mysql_errno($this->__dbConn);

         if(!empty($mysql_error) || !empty($mysql_errno)){
            $message = '[MySQLxHandler::executeStatement()] ('.$mysql_errno.') '.$mysql_error.' (Statement: '.$statement.')';
            $this->__dbLog->logEntry($this->__dbLogFileName,$message,'ERROR');
            throw new DatabaseHandlerException('[MySQLxHandler::executeStatement()] '.$message);
         }

         // track $__lastInsertID fur further usage
         $ID = @mysql_fetch_assoc(@mysql_query('SELECT Last_Insert_ID() AS Last_Insert_ID;',$this->__dbConn));
         $this->__lastInsertID = $ID['Last_Insert_ID'];

         return $result;

       // end function
      }

      /**
       * @public
       *
       * Quotes data for use in mysql statements.
       *
       * @param string $Value string to quote
       * @return string $escapedValue quoted string
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.01.2008<br />
       * Version 0.2, 17.11.2008 (Bugfix: if the method is called before any other, the connection is null)<br />
       */
      public function escapeValue($value){
         return mysql_real_escape_string($value,$this->__dbConn);
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
      public function fetchData($resultCursor, $type = self::ASSOC_FETCH_MODE){
         if($type === self::ASSOC_FETCH_MODE){
            return mysql_fetch_assoc($resultCursor);
         }
         elseif($type === self::OBJECT_FETCH_MODE){
            return mysql_fetch_object($resultCursor);
         }
         else {
            return mysql_fetch_row($resultCursor);
         }
      }

      /**
      *  @public
      *
      *  Sets the data pointer to the given offset using the result resource.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 15.01.2006<br />
      */
      public function setDataPointer($result,$offset){
         @mysql_data_seek($result,$offset);
      }

      /**
       * @public
       *
       * Returns the amount of rows, that are affected by a previous update or delete call.
       *
       * @param resource $resultCursor The result resource pointer.
       * @return int The number of affected rows.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 04.01.2006<br />
       * Version 0.2, 07.03.2008<br />
       */
      public function getAffectedRows($resultCursor){
         return mysql_affected_rows($this->__dbConn);
      }

      /**
       * @public
       *
       * Returns the number of selected rows by the given result resource.
       *
       * @param $result the mysql result resource
       * @return $numRows the number of selected rows
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 04.01.2006<br />
       */
      public function getNumRows($result){
         return mysql_num_rows($result);
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
       * @throws DatabaseHandlerException In case of any database error.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.02.2008<br />
       */
      public function executeTextStatement($statement,$logStatement = false){

         // log statements in debug mode or when requested explicitly
         if($this->__dbDebug == true || $logStatement == true){
            $this->__dbLog->logEntry($this->__dbLogFileName,
               '[MySQLxHandler::executeTextStatement()] Current statement: '.$statement,
               'DEBUG');
          // end if
         }

         // execute the statement with use of the current connection!
         $result = @mysql_query($statement,$this->__dbConn);

         // get current error to be able to do error handling
         $mysql_error = mysql_error($this->__dbConn);
         $mysql_errno = mysql_errno($this->__dbConn);

         if(!empty($mysql_error) || !empty($mysql_errno)){
            $message = '('.$mysql_errno.') '.$mysql_error.' (Statement: '.$statement.')';
            $this->__dbLog->logEntry($this->__dbLogFileName,$message,'ERROR');
            throw new DatabaseHandlerException('[MySQLxHandler->executeTextStatement()] '.$message);
         }

         // track $__lastInsertID for further usage
         $ID = @mysql_fetch_assoc(@mysql_query('SELECT Last_Insert_ID() AS Last_Insert_ID',$this->__dbConn));
         $this->__lastInsertID = $ID['Last_Insert_ID'];

         return $result;

       // end function
      }

      /**
       * @public
       *
       * Returns the version of the database server.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 05.03.2006<br />
       * Version 0.2, 07.03.2008 (Now the connection is applied to the call.)<br />
       */
      public function getServerInfo(){
         return mysql_get_server_info($this->__dbConn);
      }

      /**
       * @public
       *
       * Returns the name of the current database.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 05.03.2006<br />
       */
      public function getDatabaseName(){
         return $this->__dbName;
      }

    // end class
   }
?>