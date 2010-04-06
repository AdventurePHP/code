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
    * @class MySQLiHandler
    *
    * This class implements a connection handler for the ConnectionManager
    * to use with mysqli extension.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.02.2008<br />
    */
   class MySQLiHandler extends AbstractDatabaseHandler {

      /**
       * @var int The number of rows, that are affected within a bind statement execution.
       */
      private $__bindNumRows = 0;

      public function MySQLiHandler(){
         $this->__dbLogFileName = 'mysqli';
      }

      /**
       * @protected
       *
       * Initiates the database connection and preselects the desired database.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.02.2010<br />
       */
      protected function __connect(){
         
         // initiate connection
         $this->__dbConn = mysqli_init();
         $this->__dbConn->real_connect($this->getServerHost(),$this->__dbUser,$this->__dbPass,$this->__dbName,$this->getServerPort());

         if($this->__dbConn->connect_error || mysqli_connect_error()){
            throw new DatabaseHandlerException('[MySQLiHandler->__connect()] Database connection '
                    .'could\'t be established ('.mysqli_connect_errno($this->__dbConn).': '
                            .mysqli_connect_error($this->__dbConn).')!',E_USER_ERROR);
          // end if
         }

         // configure client connection
         $this->initCharsetAndCollation();

       // end function
      }

      private function getServerHost(){
         $colon = strpos($this->__dbHost,':');
         if($colon !== false){
            return substr($this->__dbHost,0,$colon);
         }
         return $this->__dbHost;
      }

      private function getServerPort(){
         $colon = strpos($this->__dbHost,':');
         if($colon !== false){
            return substr($this->__dbHost,$colon + 1);
         }
         return '3306';
      }

      /**
       * @protected
       *
       * Closes the database connection.
       *
       * @throws DatabaseHandlerException In case, the database connection could not be terminated.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.02.2010<br />
       */
      protected function __close(){

         if(!$this->__dbConn->close()){
            $this->__dbConn = null;
            throw new DatabaseHandlerException('[MySQLiHandler->__close()] An error occured during closing of the '
                    .'database connection ('.mysqli_errno().': '.mysqli_error().')!',E_USER_WARNING);
         }
         $this->__dbConn = null;

       // end function
      }

      /**
       *
       * @param string $namespace Namespace of the statement file.
       * @param string $statementName Name of the statement file (filebody!).
       * @param string[] $params A list of statement parameters.
       * @param bool $logStatement Indicates, if the statement is logged for debug purposes.
       * @return MySQLi_Result The result of the statement executed.
       * @throws DatabaseHandlerException In case of any database related exception (e.g. statement
       *                                  syntax error or bind problems).
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.03.2010<br />
       */
      public function executeStatement($namespace,$statementFile,$params = array(),$logStatement = false){

         // load statement file content
         $statement = $this->getStatementFromFile($namespace,$statementFile);

         // set place holders
         if(count($params) > 0){

            // replace statement param by a escaped value
            foreach($params as $key => $value){
               $statement = str_replace('['.$key.']',$this->escapeValue($value),$statement);
             // end foreach
            }

          // end if
         }

         // log statements in debug mode or when requested explicitly
         if($this->__dbDebug == true || $logStatement == true){
            $this->__dbLog->logEntry($this->__dbLogFileName,
               '[MySQLiHandler::executeStatement()] Current statement: '.$statement,
               'DEBUG');
          // end if
         }

         // execute the statement with use of the current connection!
         $this->__dbConn->real_query($statement);

         // get current error to be able to do error handling
         if(!empty($this->__dbConn->error) || !empty($this->__dbConn->errno)){
            $message = '('.$this->__dbConn->errno.') '.$this->__dbConn->error.' (Statement: '.$statement.')';
            $this->__dbLog->logEntry($this->__dbLogFileName,$message,'ERROR');
            throw new DatabaseHandlerException('[MySQLiHandler->executeStatement()] '.$message);
         }

         // track $__lastInsertID for further usage
         $this->__lastInsertID = $this->__dbConn->insert_id;

         return $this->__dbConn->store_result();

      }

      /**
       * @private
       *
       * Loads the content of a statement file.
       *
       * @param string $namespace Namespace of the statement file.
       * @param string $statementName Name of the statement file (filebody!).
       * @return string The content of the statement file.
       * @throws DatabaseHandlerException In case the statement file cannot be loaded.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.03.2010<br />
       */
      private function getStatementFromFile($namespace,$statementFile){

         $reg = &Singleton::getInstance('Registry');
         $env = $reg->retrieve('apf::core','Environment');
         $file = APPS__PATH.'/config/'.str_replace('::','/',$namespace).'/'.str_replace('::','/',$this->__Context).'/'.$env.'_'.$statementFile.'.sql';

         if(!file_exists($file)){
            throw new DatabaseHandlerException('[MySQLiHandler->getStatementFromFile()] There\'s '
                    .'no statement file with name "'.($env.'_'.$statementFile.'.sql').'" for given '
                    .'namespace "config::'.$namespace.'" and current context "'.$this->__Context.'"!',
                    E_USER_ERROR);
         }

         return file_get_contents($file);
      }

      /**
       * @public
       *
       * Executes a statement with bind param support located within a statement file. The place
       * holders contained in the file are replaced by the given values.
       *
       * @param string $namespace Namespace of the statement file.
       * @param string $statementName Name of the statement file (filebody!).
       * @param string[] $params A list of statement parameters.
       * @param bool $logStatement Indicates, if the statement is logged for debug purposes.
       * @return string[] The resulting rows of the database call within one single array.
       * @throws DatabaseHandlerException In case of any database related exception (e.g. statement
       *                                  syntax error or bind problems).
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 08.03.2010<br />
       */
      public function executeBindStatement($namespace,$statementFile,$params = array(),$logStatement = false){

         // load statement file content
         $statement = $this->getStatementFromFile($namespace,$statementFile);

         // place holder setting is a bit tricky here, because the bind params
         // must be present in the order defined in the statement. Thus we must
         // re-order the $params array.
         $t = &Singleton::getInstance('BenchmarkTimer');
         $statementId = md5($statement);
         $id = $statementId.' re-order bind params';
         $t->start($id);
         preg_match_all('/\[([A-Za-z0-9]+)\]/i',$statement,$matches,PREG_SET_ORDER);

         $sortedParams = array();
         $bindVariablesAvailable = (count($matches) > 0);

         // binding variables is only necessary, if we have dynamic statement
         // params defined within the statement file!
         if($bindVariablesAvailable){
            foreach($matches as $stmtParam){
               $stmtParamName = $stmtParam[1];
               if(isset($params[$stmtParamName])){

                  // add param to sorted array
                  $sortedParams[$stmtParamName] = $params[$stmtParamName];

                  // replace for binding
                  $statement = str_replace('['.$stmtParamName.']','?',$statement);

               }
            }
         }

         // log statements in debug mode or when requested explicitly
         if($this->__dbDebug == true ||$logStatement == true){
            $this->__dbLog->logEntry($this->__dbLogFileName,
               '[MySQLiHandler::executeBindStatement()] Current statement: '.$statement,'DEBUG');
          // end if
         }
         $t->stop($id);

         // bind params
         $id = $statementId.' prepare statement';
         $t->start($id);
         $query = $this->createQuery($statement);
         $t->stop($id);

         $id = $statementId.' bind params';
         $t->start($id);
         // binding variables is only necessary, if we have dynamic statement
         // params defined within the statement file!
         if($bindVariablesAvailable){
            $this->bindParams($query,$sortedParams);
         }
         $t->stop($id);

         // execute statement
         $id = $statementId.' execute';
         $t->start($id);
         $query->execute();

         // track $__lastInsertID fur further usage
         $this->__lastInsertID = $query->insert_id;

         $t->stop($id);

         // fetch the result set using the meta data returned by the query
         $id = $statementId.' fetch';
         $t->start($id);
         $statementResult = $this->fetchBindResult($query);
         $t->stop($id);

         // remember affected rows
         $this->__bindNumRows = $query->num_rows;

         $query->free_result();
         $query->close();
         return $statementResult;

       // end function
      }

      /**
       * @public
       *
       * Executes a statement provided using mysqli's bind feature.
       *
       * @param string $statement The statement to execute.
       * @param string[] $params A list of statement parameters.
       * @param boolean $logStatement Indicates, if the statement is logged for debug purposes.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 08.03.2010<br />
       */
      public function executeTextBindStatement($statement,$params = array(),$logStatement = false){

         $t = &Singleton::getInstance('BenchmarkTimer');
         $statementId = md5($statement);

         // prepare statement
         $id = $statementId.' prepare statement';
         $t->start($id);
         $query = $this->createQuery($statement);
         $t->stop($id);

         // bind params
         $id = $statementId.' bind params';
         $t->start($id);

         // additional check, because bind param count errors do not result in an exception!
         $paramStatementCount = substr_count($statement,'?');
         $paramCount = count($params);
         if($paramStatementCount != $paramCount){
            throw new DatabaseHandlerException('[MySQLiHandler->executeTextBindStatement()] Number '
                    .'of given params ('.$paramCount.') does not match number of bind params '
                    .'within the statement ('.$paramStatementCount.')! Current statement: '
                    .$statement,E_USER_ERROR);
         }
         
         $this->bindParams($query,$params);
         $t->stop($id);

         // execute statement
         $id = $statementId.' execute';
         $t->start($id);
         $query->execute();

         // track $__lastInsertID fur further usage
         $this->__lastInsertID = $query->insert_id;

         $t->stop($id);

         // fetch the result set using the meta data returned by the query
         $id = $statementId.' fetch';
         $t->start($id);
         $statementResult = $this->fetchBindResult($query);
         $t->stop($id);

         // remember affected rows
         $this->__bindNumRows = $query->num_rows;

         $query->free_result();
         $query->close();
         return $statementResult;

      }

      /**
       * @return int The number of rows affected by a statement execution.
       */
      public function getBindNumRows(){
         return $this->__bindNumRows;
      }

      /**
       * @private
       *
       * Creates a MySQLi statement representation by the given statement string.
       *
       * @param string $statement The statement to create the statement instance of.
       * @return MYSQLi_STMT The desired statement instance.
       * @throws DatabaseHandlerException In case of any statement errors.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 08.03.2010<br />
       */
      private function createQuery($statement){
         $query = $this->__dbConn->prepare($statement);
         if($query === false || !empty($this->__dbConn->error) || !empty($this->__dbConn->errno)){
            throw new DatabaseHandlerException($this->__dbConn->error,$this->__dbConn->errno);
         }
         return $query;
      }

      /**
       * @private
       * 
       * Binds the given params to the presented prepared statement.
       *
       * @param MYSQLi_STMT $query The prepared query to bind the params to.
       * @param string[] $params A list of statement parameters.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.03.2010<br />
       */
      private function bindParams(&$query,$params){
         $binds = array();
         foreach($params as $key => $DUMMY){
            $binds[] = $params[$key];
         }
         call_user_func_array(
                 array(&$query,'bind_param'),
                 array_merge(
                         array(str_repeat('s',count($params))),
                         $binds
                 )
         );
      }

      /**
       * @private
       *
       * Fetches the result from a prepared query.
       * 
       * @param MYSQLi_STMT $query The prepared query to fetch the result from.
       * @return string[] The result array.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.03.2010<br />
       */
      private function fetchBindResult(&$query){
         $metaData = $query->result_metadata();
         while($field = $metaData->fetch_field()){
            $resultParams[] = &$resultRow[$field->name];
         }

         call_user_func_array(array(&$query,'bind_result'),$resultParams);
         $bindResult = array();
         while($query->fetch()){
            foreach($resultRow as $key => $val){
               $currentRow[$key] = $val;
            }
            $bindResult[] = $currentRow;
         }
         return $bindResult;
      }

      /**
       * @public
       *
       * Quotes data for use in MySQL statements.
       *
       * @param string $value String to quote.
       * @return string Quoted string.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.02.2010<br />
       */
      public function escapeValue($value){
         return $this->__dbConn->real_escape_string($value);
       // end function
      }

      /**
       * @public
       *
       * Fetches a record from the database using the given result resource.
       *
       * @param resource $resultCursor The result resource returned by executeStatement() or executeTextStatement().
       * @return string[] The associative result array.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.03.2010<br />
       */
      public function fetchData($resultCursor){
         if($resultCursor == null){
            return array();
         }
         return $resultCursor->fetch_assoc();
       // end function
      }

      /**
       * @public
       *
       * Sets the data pointer to the given offset using the result resource.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.03.2010<br />
       */
      public function setDataPointer($result,$offset){
         @mysqli_data_seek($result,$offset);
       // end function
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
       * Version 0.1, 09.03.2010<br />
       */
      public function getAffectedRows($resultCursor){
         return mysqli_affected_rows($this->__dbConn);
      }

      /**
      *  @public
      *
      *  Returns the number of selected rows by the given result resource.
      *
      *  @param $result the mysql result resource
      *  @return $numRows the number of selected rows
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.03.2010<br />
      */
      function getNumRows($result){
         return mysqli_num_rows($result);
      }

      /**
       * @public
       *
       * Executes a statement applied as a string to the method and returns the
       * result pointer.
       *
       * @param string $statement The statement to execute.
       * @param boolean $logStatement Inidcates, whether the given statement should be
       *                              logged for debug purposes.
       * @return resource The database result resource.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.03.2010<br />
       */
      public function executeTextStatement($statement,$logStatement = false){

         // log statements in debug mode or when requested explicitly
         if($this->__dbDebug == true || $logStatement == true){
            $this->__dbLog->logEntry($this->__dbLogFileName,
               '[MySQLiHandler::executeTextStatement()] Current statement: '.$statement,
               'DEBUG');
          // end if
         }

         // execute the statement with use of the current connection!
         $this->__dbConn->real_query($statement);

         // get current error to be able to do error handling
         if(!empty($this->__dbConn->error) || !empty($this->__dbConn->errno)){
            $message = '('.$this->__dbConn->errno.') '.$this->__dbConn->error.' (Statement: '.$statement.')';
            $this->__dbLog->logEntry($this->__dbLogFileName,$message,'ERROR');
            throw new DatabaseHandlerException('[MySQLiHandler->executeTextStatement()] '.$message);
         }

         // track $__lastInsertID for further usage
         $this->__lastInsertID = $this->__dbConn->insert_id;

         return $this->__dbConn->store_result();

       // end function
      }

      /**
       * @public
       *
       * Returns the version of the database server.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.03.2010<br />
       */
      public function getServerInfo(){
         return mysqli_get_server_info($this->__dbConn);
      }

      /**
       * @public
       *
       * Returns the name of the current database.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.03.2010<br />
       */
      public function getDatabaseName(){
         return $this->__dbName;
      }

    // end class
   }
?>