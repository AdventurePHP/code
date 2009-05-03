<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('core::logging','Logger');
   register_shutdown_function('mysqlTerminateConnection');


   /**
   *  @namespace core::database
   *
   *  Wrapper-Funktion zum Schlie�en einer DB-Verbindung.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 01.04.2007<br />
   */
   function mysqlTerminateConnection(){
      $SQL = &Singleton::getInstance('MySQLHandler');
      $SQL->closeConnection();
    // end function
   }


   /**
   *  @namespace core::database
   *  @class MySQLHandler
   *
   *  Dienst zur Abstraktion einer MySQL-Datenbank. Beinhaltet<br />
   *  keine Caching-Mechanismen.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 21.06.2004<br />
   *  Version 0.2, 06.04.2005<br />
   *  Version 0.3, 22.01.2006<br />
   *  Version 0.4, 06.02.2006 (Konfiguration 'ZeigeFehler' bereinigt)<br />
   *  Version 0.5, 16.04.2006 (Bereinigung alter Methoden, Quell-Code-Dokumentation)<br />
   *  Version 0.6, 29.03.2007 (Auf neuen Logger umgestellt)<br />
   *  Version 0.7, 01.04.2007 (Performance-Tuning: Connect/Disconnect wird nun nur noch EIN Mal ausgef�hrt)<br />
   *  Version 0.8, 23.02.2008 (Kompatibilit�tsanpassungen f�r ConnectionManager)<br />
   *  Version 0.9, 24.02.2008 (Weitere Kompatibilit�tsanpassungen f�r ConnectionManager)<br />
   */
   class MySQLHandler extends coreObject
   {

      /**
      *  @protected
      *  Verbindungs-Kennung.
      */
      protected $__dbConn = null;


      /**
      *  @protected
      *  ID des letzten Inserts.
      */
      protected $__lastInsertID;


      /**
      *  @protected
      *  Anzahl der selektierten Ergebnisse.
      */
      protected $__NumRows;


      /**
      *  @protected
      *  Datenbank-Host.
      */
      protected $__dbHost;


      /**
      *  @protected
      *  Datenbank-Benutzer.
      */
      protected $__dbUser;


      /**
      *  @protected
      *  Datenbank-Password.
      */
      protected $__dbPass;


      /**
      *  @protected
      *  Datenbank-Name.
      */
      protected $__dbName;


      /**
      *  @protected
      *  Log-Datei (Instanz des Loggers).
      */
      protected $__dbLog;


      /**
      *  @protected
      *  Log-Datei-Name.
      */
      protected $__dbLogFileName = 'mysql';


      /**
      *  @protected
      *  Debug-Mode an?
      */
      protected $__dbDebug = false;


      /**
      *  @protected
      *  Zeigt an, ob Klasse bereits initialisiert wurde.
      */
      protected $__isInitialized = false;


      function MySQLHandler(){
      }


      /**
      *  @private
      *
      *  Initialisiert den MySQLHandler, falls dies noch nicht geschehen ist.<br />
      *  Methode ist ein interner Helper, da von aussen nur der Context gesetzt wird, und die<br />
      *  restliche Initialisierung m�glichst nicht von Aussen erledigt werden sollte.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 28.03.2007<br />
      *  Version 0.2, 01.04.2007 (Connect wird beim INIT erledigt)<br />
      */
      protected function __initMySQLHandler(){

         // Falls noch nicht initialisiert wurde initialisieren
         if($this->__isInitialized == false){

            // Konfiguration auslesen
            $Config = &$this->__getConfiguration('core::database','connections');

            // Section auslesen
            $Section = $Config->getSection('MySQL');

            // Pr�fen, ob Section existent
            if($Config == null){
               trigger_error('[MySQLHandler->__initMySQLHandler()] Configuration "dbconnectiondaten" in namspace "core::database" and context "'.$this->__Context.'" contains no valid data!',E_USER_ERROR);
               exit();
             // end if
            }

            // Zugangsdaten auslesen
            $this->__dbHost = $Section['DB.Host'];
            $this->__dbUser = $Section['DB.User'];
            $this->__dbPass = $Section['DB.Pass'];
            $this->__dbName = $Section['DB.Name'];

            // Debug-Mode aktivieren / deaktivieren
            if(isset($Section['DB.DebugMode'])){
               if($Section['DB.DebugMode'] == 'true' || $Section['DB.DebugMode'] == '1'){
                  $this->__dbDebug = true;
                // end if
               }
               else{
                  $this->__dbDebug = false;
                // end else
               }

             // end if
            }
            else{
               $this->__dbDebug = false;
             // end else
            }

            // Logdatei festlegen (Instanz des Logger's)
            $this->__dbLog = &Singleton::getInstance('Logger');

            // Klasse als initialisiert kennzeichnen
            $this->__isInitialized = true;

            // Zur DB verbinden
            $this->__connect();

          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  Initiates the database connectio and preselects the desired database.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 2002<br />
      *  Version 0.2, 2002<br />
      *  Version 0.3, 2002<br />
      *  Version 0.4, 10.04.2004<br />
      *  Version 0.5, 04.12.2005<br />
      *  Version 0.6, 24.12.2005<br />
      *  Version 0.7, 04.01.2005<br />
      *  Version 0.8, 09.10.2008 (Removed the @ before mysql_connect to get a more detailed in case of connection errors)<br />
      *  Version 0.9, 18.03.2009 (Bugfix: create a new connection, even if the connection data is the same. This otherwise may result in interference of connections, that use different databases.)<br />
      */
      protected function __connect(){

         // initiate connection
         $this->__dbConn = mysql_connect($this->__dbHost,$this->__dbUser,$this->__dbPass,true);

         if(!is_resource($this->__dbConn)){
            trigger_error('[MySQLHandler->__connect()] Database connection could\'t be established ('.mysql_errno().': '.mysql_error().')!',E_USER_ERROR);
            exit();
          // end if
         }

         // select the database
         $result = @mysql_select_db($this->__dbName,$this->__dbConn);

         if(!$result){
            trigger_error('[MySQLHandler->__connect()] Database couldn\'t be selected ('.mysql_errno().': '.mysql_error().')!',E_USER_ERROR);
            exit();
          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  Closes the database connection.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 2002<br />
      *  Version 0.2, 10.04.2004<br />
      *  Version 0.3, 04.12.2005<br />
      *  Version 0.4, 24.12.2005<br />
      */
      protected function __close(){

         $result = @mysql_close($this->__dbConn);
         $this->__dbConn = null;

         if(!$result){
            trigger_error('[MySQLHandler->__close()] An error occured during closing of the database connection ('.mysql_errno().': '.mysql_error().')!',E_USER_WARNING);
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Public method to close the database connection. Used by the shutdown function.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 01.04.2007<br />
      *  Version 0.2, 01.04.2007 (Connection is only closed if existent)<br />
      */
      function closeConnection(){

         if($this->__dbConn != null){
            $this->__close();
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Executes a statement file located in the given namespace. The place holders contained in the
      *  file are replaced by the given values.
      *
      *  @param string $namespace The namespace of the statement file
      *  @param string $statementFile The name of the statement file (with ENVIRONMENT prefix!)
      *  @param array $params A list of statement params (associative array)
      *  @param bool $logStatement Indicates, if the statement is logged for debug purposes
      *  @return resource The result resource
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 24.12.2005<br />
      *  Version 0.2, 16.01.2006<br />
      *  Version 0.3, 19.01.2006<br />
      *  Version 0.4, 23.04.2006 (Changes due to the ApplicationManagers)<br />
      *  Version 0.5, 05.08.2006 (File extension must not be present in the file name any more. Statement params are now optional.)<br />
      *  Version 0.6, 05.08.2006 (Added the $showStatement param)<br />
      *  Version 0.7, 29.03.2007 (Adapted implementation to the new page controller implementation)<br />
      *  Version 0.8, 07.03.2008 (Bugfix: query was not executed with the right connection)<br />
      *  Version 0.9, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
      *  Version 1.0, 05.11.2008 (Added value escaping to the statement params)<br />
      *  Version 1.1, 26.03.2009 (Enhanced the error messages)<br />
      *  Version 1.2, 03.05.2009 (Forth param set to true now results in a debug log entry instead of an error)<br />
      */
      function executeStatement($namespace,$statementFile,$params = array(),$logStatement = false){

         $this->__initMySQLHandler();

         // check, whether the desired statement file exists
         $reg = &Singleton::getInstance('Registry');
         $env = $reg->retrieve('apf::core','Environment');
         $file = APPS__PATH.'/config/'.str_replace('::','/',$namespace).'/'.str_replace('::','/',$this->__Context).'/'.$env.'_'.$statementFile.'.sql';

         if(!file_exists($file)){
            trigger_error('[MySQLHandler->executeStatement()] There\'s no statement file with name "'.($env.'_'.$statementFile.'.sql').'" for given namespace "config::'.$namespace.'" and current context "'.$this->__Context.'::statements"!',E_USER_ERROR);
            exit(1);
          // end if
         }

         $statement = file_get_contents($file);

         // set place holders
         if(count($params) > 0){

            // replace statement param by a escaped value
            foreach($params as $key => $value){
               $statement = str_replace('['.$key.']',$this->escapeValue($value),$statement);
             // end foreach
            }

          // end if
         }

         if($logStatement == true){
            $this->__dbLog->logEntry($this->__dbLogFileName,
               '[MySQLHandler::executeStatement()] Current statement: '.$statement,
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

            if($this->__dbDebug == true){
               trigger_error('[MySQLHandler::executeStatement()] '.$message);
             // end if
            }

          // end if
         }

         // track $__lastInsertID fur further usage
         $ID = @mysql_fetch_assoc(@mysql_query('SELECT Last_Insert_ID() AS Last_Insert_ID;',$this->__dbConn));
         $this->__lastInsertID = $ID['Last_Insert_ID'];

         return $result;

       // end function
      }


      /**
      *  @public
      *
      *  Quotes data for use in mysql statements.
      *
      *  @param string $Value string to quote
      *  @return string $escapedValue quoted string
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 07.01.2008<br />
      *  Version 0.2, 17.11.2008 (Bugfix: if the method is called before any other, the connection is null)<br />
      */
      function escapeValue($value){
         $this->__initMySQLHandler();
         return mysql_real_escape_string($value,$this->__dbConn);
       // end function
      }


      /**
      *  @public
      *
      *  Fetches a record from the database using the given result resource.
      *
      *  @param resource $ResultCursor the mysql result resource
      *  @return array $Data the associative result array
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 24.12.2005<br />
      *  Version 0.2, 23.02.2008 (Array is now returned directly)<br />
      */
      function fetchData($ResultCursor){
         $this->__initMySQLHandler();
         return mysql_fetch_assoc($ResultCursor);
       // end function
      }


      /**
      *  @public
      *
      *  Sets the data pointer to the given offset using the result resource.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 15.01.2006<br />
      */
      function setDataPointer($result,$offset){
         $this->__initMySQLHandler();
         @mysql_data_seek($result,$offset);
       // end function
      }


      /**
      *  @public
      *
      *  Returns the count of affected database records. Can be used to indicate the rows, that are updated/deleted.
      *
      *  @return int $AffectedRows the amount of affected rows
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      *  Version 0.2, 07.03.2008<br />
      */
      function getAffectedRows(){
         $this->__initMySQLHandler();
         return mysql_affected_rows($this->__dbConn);
       // end function
      }


      /**
      *  @public
      *
      *  Returns the last auto_increment id for the last INSERT statement.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      */
      function getLastID(){
         $this->__initMySQLHandler();
         return $this->__lastInsertID;
       // end function
      }


      /**
      *  @public
      *
      *  Returns the number of selected rows by the given result resource.
      *
      *  @param $result the mysql result resource
      *  @return $numRows the number of selected rows
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      */
      function getNumRows($result){
         $this->__initMySQLHandler();
         return mysql_num_rows($result);
       // end function
      }


      /**
      *  @public
      *
      *  Executes a statement given by the first argument.
      *
      *  @param string $statement The mysql statement
      *  @param boolean $logStatement Inidcates, whether the given statement should be logged for debug purposes
      *  @return ressource The resulting mysql result resource pointer
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 22.01.2006<br />
      *  Version 0.2, 07.03.2008 (Bugfix: the query was not executed with the right connection)<br />
      *  Version 0.3, 03.05.2009 (Added the $logStatement param)<br />
      */
      function executeTextStatement($statement,$logStatement = false){

         $this->__initMySQLHandler();

         if($logStatement == true){
            $this->__dbLog->logEntry($this->__dbLogFileName,
               '[MySQLHandler::executeTextStatement()] Current statement: '.$statement,
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

            if($this->__dbDebug == true){
               trigger_error('[MySQLHandler->executeTextStatement()] '.$message);
             // end if
            }

          // end if
         }

         // track $__lastInsertID for further usage
         $ID = @mysql_fetch_assoc(@mysql_query('SELECT Last_Insert_ID() AS Last_Insert_ID',$this->__dbConn));
         $this->__lastInsertID = $ID['Last_Insert_ID'];

         return $result;

       // end function
      }


      /**
      *  @public
      *
      *  Returns the version of the database server.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.03.2006<br />
      *  Version 0.2, 07.03.2008 (Now the connection is applied to the call.)<br />
      */
      function getServerInfo(){
         $this->__initMySQLHandler();
         return mysql_get_server_info($this->__dbConn);
       // end function
      }


      /**
      *  @public
      *
      *  Returns the name of the current database.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.03.2006<br />
      */
      function getDatabaseName(){
         $this->__initMySQLHandler();
         return $this->__db_name;
       // end function
      }


      /**
      *  @public
      *  @deprecated
      *
      *  Erzeugt einen Dump der aktuellen Datenbank mit dem CLI-Tool 'mysqldump'.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 01.07.2006<br />
      *  Version 0.2, 18.08.2006 (Standard-Namen wurde von db_host auf db_name ge�ndert)<br />
      */
      function backupDatabase($File = ''){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // Dump-File benennen
         if(empty($File)){
            $File = 'dump_'.($this->__db_name).'_'.date('Y_m_d__H_i_s').'.sql';
          // end if
         }

         // mysqldump ausf�hren
         exec('mysqldump --add-drop-table --complete-insert --create-options --extended-insert --force --lock-tables --host='.($this->__db_host).' --user='.($this->__db_user).' --password='.($this->__db_pass).' '.($this->__db_name).' > '.$File);

         // true zur�ckgeben
         return true;

       // end function
      }


      /**
      *  @public
      *  @deprecated
      *
      *  Spielt ein Datenbank-Backup wieder ein, das zuvor mit backupDatabase() erzeugt wurde.<br />
      *  Benutzt das CLI-Tool 'mysql'.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 01.07.2006<br />
      */
      function restoreDatabase($File = ''){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // Pr�fen, ob Dump-Datei existiert
         if(!file_exists($File)){
            return false;
          // end if
         }

         // Import ausf�hren
         exec('mysql --host='.($this->__db_host).' --user='.($this->__db_user).' --password='.($this->__db_pass).' --database='.($this->__db_name).' --force --xml --execute="source '.$File.'"');

         // true zur�ckgeben
         return true;

       // end function
      }

    // end class
   }
?>