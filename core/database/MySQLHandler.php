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
   *  Wrapper-Funktion zum Schließen einer DB-Verbindung.<br />
   *
   *  @author Christian Schäfer
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
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 21.06.2004<br />
   *  Version 0.2, 06.04.2005<br />
   *  Version 0.3, 22.01.2006<br />
   *  Version 0.4, 06.02.2006 (Konfiguration 'ZeigeFehler' bereinigt)<br />
   *  Version 0.5, 16.04.2006 (Bereinigung alter Methoden, Quell-Code-Dokumentation)<br />
   *  Version 0.6, 29.03.2007 (Auf neuen Logger umgestellt)<br />
   *  Version 0.7, 01.04.2007 (Performance-Tuning: Connect/Disconnect wird nun nur noch EIN Mal ausgeführt)<br />
   *  Version 0.8, 23.02.2008 (Kompatibilitätsanpassungen für ConnectionManager)<br />
   *  Version 0.9, 24.02.2008 (Weitere Kompatibilitätsanpassungen für ConnectionManager)<br />
   */
   class MySQLHandler extends coreObject
   {

      /**
      *  @private
      *  Verbindungs-Kennung.
      */
      var $__dbConn = null;


      /**
      *  @private
      *  ID des letzten Inserts.
      */
      var $__lastInsertID;


      /**
      *  @private
      *  Anzahl der selektierten Ergebnisse.
      */
      var $__NumRows;


      /**
      *  @private
      *  Datenbank-Host.
      */
      var $__dbHost;


      /**
      *  @private
      *  Datenbank-Benutzer.
      */
      var $__dbUser;


      /**
      *  @private
      *  Datenbank-Password.
      */
      var $__dbPass;


      /**
      *  @private
      *  Datenbank-Name.
      */
      var $__dbName;


      /**
      *  @private
      *  Log-Datei (Instanz des Loggers).
      */
      var $__dbLog;


      /**
      *  @private
      *  Log-Datei-Name.
      */
      var $__dbLogFileName = 'mysql';


      /**
      *  @private
      *  Debug-Mode an?
      */
      var $__dbDebug = false;


      /**
      *  @private
      *  Zeigt an, ob Klasse bereits initialisiert wurde.
      */
      var $__isInitialized = false;


      function MySQLHandler(){
      }


      /**
      *  @private
      *
      *  Initialisiert den MySQLHandler, falls dies noch nicht geschehen ist.<br />
      *  Methode ist ein interner Helper, da von aussen nur der Context gesetzt wird, und die<br />
      *  restliche Initialisierung möglichst nicht von Aussen erledigt werden sollte.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.03.2007<br />
      *  Version 0.2, 01.04.2007 (Connect wird beim INIT erledigt)<br />
      */
      function __initMySQLHandler(){

         // Falls noch nicht initialisiert wurde initialisieren
         if($this->__isInitialized == false){

            // Konfiguration auslesen
            $Config = &$this->__getConfiguration('core::database','connections');

            // Section auslesen
            $Section = $Config->getSection('MySQL');

            // Prüfen, ob Section existent
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
      function __connect(){

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
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 2002<br />
      *  Version 0.2, 10.04.2004<br />
      *  Version 0.3, 04.12.2005<br />
      *  Version 0.4, 24.12.2005<br />
      */
      function __close(){

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
      *  @author Christian Schäfer
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
      *  @param string $Namespace the namespace of the statement file
      *  @param string $StatementFile the name of the statement file (with ENVIRONMENT prefix!)
      *  @param array $Params a list of statement params (associative array)
      *  @param bool $ShowStatement indicates, if the statement is displayed for debug purposes
      *  @return resource $Result the result resource
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 24.12.2005<br />
      *  Version 0.2, 16.01.2006<br />
      *  Version 0.3, 19.01.2006<br />
      *  Version 0.4, 23.04.2006 (Changes due to the ApplicationManagers)<br />
      *  Version 0.5, 05.08.2006 (File extension must not be present in the file name any more. Statement params are now optional.)<br />
      *  Version 0.6, 05.08.2006 (Added the $ShowStatement param)<br />
      *  Version 0.7, 29.03.2007 (Adapted implementation to the new page controller implementation)<br />
      *  Version 0.8, 07.03.2008 (Bugfix: query was not executed with the right connection)<br />
      *  Version 0.9, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
      *  Version 1.0, 05.11.2008 (Added value escaping to the statement params)<br />
      *  Version 1.1, 26.03.2009 (Enhanced the error messages)<br />
      */
      function executeStatement($Namespace,$StatementFile,$Params = array(),$ShowStatement = false){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // Dateinamen generieren und prüfen, ob Datei existiert
         $Reg = &Singleton::getInstance('Registry');
         $Environment = $Reg->retrieve('apf::core','Environment');
         $File = APPS__PATH.'/config/'.str_replace('::','/',$Namespace).'/'.str_replace('::','/',$this->__Context).'/statements/'.$Environment.'_'.$StatementFile.'.sql';

         if(!file_exists($File)){
            trigger_error('[MySQLHandler->executeStatement()] There\'s no statement file with name "'.($Environment.'_'.$StatementFile.'.sql').'" for given namespace "config::'.$Namespace.'" and current context "'.$this->__Context.'::statements"!',E_USER_ERROR);
            exit(1);
          // end if
         }

         // Statement einlesen
         $Statement = file_get_contents($File);

         // Platzhalter ersetzen
         if(count($Params) > 0){

            // replace statement param by a escaped value
            foreach($Params as $Key => $Value){
               $Statement = str_replace('['.$Key.']',$this->escapeValue($Value),$Statement);
             // end foreach
            }

          // end if
         }

         // Statement ausgeben
         if($ShowStatement == true){
            trigger_error('[MySQLHandler->executeStatement()] Current Statement: '.$Statement);
          // end if
         }

         // Statement ausführen
         $result = @mysql_query($Statement,$this->__dbConn);

         // Fehler tracken
         $mysql_error = mysql_error($this->__dbConn);
         $mysql_errno = mysql_errno($this->__dbConn);

         if(!empty($mysql_error) || !empty($mysql_errno)){

            // Meldung generieren
            $Message = '('.$mysql_errno.') '.$mysql_error.' (Statement: '.$Statement.')';

            // Meldung protokollieren
            $this->__dbLog->logEntry($this->__dbLogFileName,$Message,'ERROR');

            // Fehler werfen
            if($this->__dbDebug == true){
               trigger_error('[MySQLHandler->executeStatement()] '.$Message);
             // end if
            }

          // end if
         }

         // $__lastInsertID setzen
         $ID = @mysql_fetch_assoc(@mysql_query('SELECT Last_Insert_ID() AS Last_Insert_ID;',$this->__dbConn));
         $this->__lastInsertID = $ID['Last_Insert_ID'];

         // Ergebnis zurückgeben
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
      *  @author Christian Schäfer
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
      *  @author Christian Schäfer
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
      *  @author Christian Schäfer
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
      *  @author Christian Schäfer
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
      *  @author Christian Schäfer
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
      *  @param string $Statement the mysql statement
      *  @return ressource $Result the resulting mysql result resource pointer
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.01.2006<br />
      *  Version 0.2, 07.03.2008 (Bugfix: the query was not executed with the right connection)<br />
      */
      function executeTextStatement($Statement){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // Statement ausführen
         $Result = @mysql_query($Statement,$this->__dbConn);

         // Fehler tracken
         $mysql_error = mysql_error($this->__dbConn);
         $mysql_errno = mysql_errno($this->__dbConn);

         if(!empty($mysql_error) || !empty($mysql_errno)){

            // Meldung generieren
            $Meldung = '('.$mysql_errno.') '.$mysql_error.' (Statement: '.$Statement.')';

            // Fehler protokollieren
            $this->__dbLog->logEntry($this->__dbLogFileName,$Meldung,'ERROR');

            if($this->__dbDebug == true){
               trigger_error('[MySQLHandler->executeTextStatement()] '.$Meldung);
             // end if
            }

          // end if
         }

         // $__lastInsertID setzen
         $ID = @mysql_fetch_assoc(@mysql_query('SELECT Last_Insert_ID() AS Last_Insert_ID',$this->__dbConn));
         $this->__lastInsertID = $ID['Last_Insert_ID'];

         // Ergebnis zurückgeben
         return $Result;

       // end function
      }


      /**
      *  @public
      *
      *  Returns the version of the database server.
      *
      *  @author Christian Schäfer
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
      *  @author Christian Schäfer
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
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 01.07.2006<br />
      *  Version 0.2, 18.08.2006 (Standard-Namen wurde von db_host auf db_name geändert)<br />
      */
      function backupDatabase($File = ''){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // Dump-File benennen
         if(empty($File)){
            $File = 'dump_'.($this->__db_name).'_'.date('Y_m_d__H_i_s').'.sql';
          // end if
         }

         // mysqldump ausführen
         exec('mysqldump --add-drop-table --complete-insert --create-options --extended-insert --force --lock-tables --host='.($this->__db_host).' --user='.($this->__db_user).' --password='.($this->__db_pass).' '.($this->__db_name).' > '.$File);

         // true zurückgeben
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
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 01.07.2006<br />
      */
      function restoreDatabase($File = ''){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // Prüfen, ob Dump-Datei existiert
         if(!file_exists($File)){
            return false;
          // end if
         }

         // Import ausführen
         exec('mysql --host='.($this->__db_host).' --user='.($this->__db_user).' --password='.($this->__db_pass).' --database='.($this->__db_name).' --force --xml --execute="source '.$File.'"');

         // true zurückgeben
         return true;

       // end function
      }

    // end class
   }
?>