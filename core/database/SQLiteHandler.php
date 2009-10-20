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

   /**
   *  @namespace core::database
   *  @class SQLiteHandler
   *
   *  Implementiert die Datenbankabstraktionsschicht für die SQLite-Schnittstelle.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 23.02.2008<br />
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

      public function SQLiteHandler(){
         $this->__dbLogFileName = 'sqlite';
      }

      /**
       * @protected
       *
       * Implements the connect method to create a connection to the desired sqlite database.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.02.2008<br />
       */
      protected function __connect(){

         // Verbindung öffnen
         $this->__dbConn = @sqlite_open($this->__dbName,$this->__dbMode,$this->__dbError);

         // Fehler ausgeben, falls vorhanden
         if(!is_resource($this->__dbConn)){
            trigger_error('[SQLiteHandler->__connect()] Database "'.$this->__dbName.'" cannot be opened! Message: '.$this->__dbError,E_USER_ERROR);
            exit(1);
          // end if
         }

       // end function
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
      protected function __close(){
         @sqlite_close($this->__dbConn);
         $this->__dbConn = null;
       // end function
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
      public function executeTextStatement($statement,$logStatement = false){

         if($logStatement == true){
            $this->__dbLog->logEntry($this->__dbLogFileName,
               '[SQLiteHandler::executeTextStatement()] Current statement: '.$statement,
               'DEBUG');
          // end if
         }

         $result = sqlite_query($this->__dbConn,$statement);

         if($result === false){

            $message = sqlite_error_string(sqlite_last_error($this->__dbConn));
            $message .= ' (Statement: '.$statement.')';

            $this->__dbLog->logEntry($this->__dbLogFileName,$message,'ERROR');

            if($this->__dbDebug == true){
               trigger_error('[SQLiteHandler->executeTextStatement()] '.$message);
             // end if
            }

          // end if
         }

         // remember last insert id for futher usage
         $this->__lastInsertID = sqlite_last_insert_rowid($this->__dbConn);

         return $result;

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
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.02.2008<br />
       * Version 0.2, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
       */
      public function executeStatement($namespace,$statementName,$params = array(),$logStatement = false){

         $reg = &Singleton::getInstance('Registry');
         $env = $reg->retrieve('apf::core','Environment');
         $file = APPS__PATH.'/config/'.str_replace('::','/',$namespace).'/'.str_replace('::','/',$this->__Context).'/statements/'.$env.'_'.$statementName.'.sql';

         if(!file_exists($file)){
            trigger_error('[SQLiteHandler->executeStatement()] There\'s no statement file with name "'.($env.'_'.$statementName.'.sql').'" for given namespace "'.$namespace.'" and current context "'.$this->__Context.'"!');
            exit();
          // end if
         }

         $statement = file_get_contents($file);

         // replace params by str_replace()
         if(count($params) > 0){

            foreach($params as $key => $value){
               $statement = str_replace('['.$key.']',$value,$statement);
             // end foreach
            }

          // end if
         }

         if($logStatement == true){
            $this->__dbLog->logEntry($this->__dbLogFileName,
               '[SQLiteHandler::executeTextStatement()] Current statement: '.$statement,
               'DEBUG');
          // end if
         }

         $result = sqlite_query($this->__dbConn,$statement);

         if($result === false){

            $message = sqlite_error_string(sqlite_last_error($this->__dbConn));
            $message .= ' (Statement: '.$statement.')';

            $this->__dbLog->logEntry($this->__dbLogFileName,$message,'ERROR');

            if($this->__dbDebug == true){
               trigger_error('[SQLiteHandler->executeTextStatement()] '.$message);
             // end if
            }

          // end if
         }

         // remember last insert id for futher usage
         $this->__lastInsertID = sqlite_last_insert_rowid($this->__dbConn);

         return $result;

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
       * Version 0.1, 20.09.2009<br />
       */
      public function fetchData($resultCursor){
         return sqlite_fetch_array($resultCursor,SQLITE_ASSOC);
       // end function
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
      public function escapeValue($value){
         return sqlite_escape_string($value);
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
       * Version 0.1, 24.02.2008<br />
       */
      public function getAffectedRows($resultCursor){
         return sqlite_num_rows($resultCursor);
       // end function
      }

    // end class
   }
?>