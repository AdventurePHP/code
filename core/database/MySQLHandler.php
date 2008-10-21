<?php
   import('core::logging','Logger');

   register_shutdown_function('mysqlTerminateConnection');

   /**
   *  @package core::database
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
   *  @package core::database
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
      */
      function __connect(){

         // initiate connection
         $this->__dbConn = mysql_connect($this->__dbHost,$this->__dbUser,$this->__dbPass);

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
      *  Trennt die durch __verbindeDatenbank() aufgebaute MySQL-Verbindung.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 2002<br />
      *  Version 0.2, 10.04.2004<br />
      *  Version 0.3, 04.12.2005<br />
      *  Version 0.4, 24.12.2005<br />
      */
      function __close(){

         // Verbindung schließen
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
      *  Öffentliche Funktion zum Trennen der DB-Verbindung (für shutdown function).<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 01.04.2007<br />
      *  Version 0.2, 01.04.2007 (Verbindung wird nur dan geschlossen, wenn auch vorhanden)<br />
      */
      function closeConnection(){

         // Verbindung schließen, falls diese besteht
         if($this->__dbConn != null){
            $this->__close();
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Führt ein Statement in einem Namespace aus. Platzhalter werden<br />
      *  durch die in $Variablen gegebenen Werte ersetzt.<br />
      *
      *  @param string $Namespace; Namespace der Statementdatei
      *  @param string $StatementFile; Name der Statementdatei
      *  @param array $Params; Parameter für das Statement
      *  @param bool $ShowStatement; Indiziert, ob das Statement ausgegeben werden soll
      *  @return resource $Result; Result-Ressource
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 24.12.2005<br />
      *  Version 0.2, 16.01.2006<br />
      *  Version 0.3, 19.01.2006<br />
      *  Version 0.4, 23.04.2006 (Änderung auf Grund des ApplicationManagers)<br />
      *  Version 0.5, 05.08.2006 (Dateiendung muss beim Stmt-File nicht mehr angegeben werden; Es müssen nicht zwingend Parameter angegeben werden)<br />
      *  Version 0.6, 05.08.2006 (optionalen Parameter $ShowStatement hinzugefügt)<br />
      *  Version 0.7, 29.03.2007 (An neue Implementierung für PC V2 angepasst)<br />
      *  Version 0.8, 07.03.2008 (Bug behoben, dass Query nicht auf die korrekte Verbindung ausgeführt wurde)<br />
      *  Version 0.9, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
      */
      function executeStatement($Namespace,$StatementFile,$Params = array(),$ShowStatement = false){

         // Initialisiere Klasse
         $this->__initMySQLHandler();


         // Dateinamen generieren und prüfen, ob Datei existiert
         $Reg = &Singleton::getInstance('Registry');
         $Environment = $Reg->retrieve('apf::core','Environment');
         $File = APPS__PATH.'/config/'.str_replace('::','/',$Namespace).'/'.str_replace('::','/',$this->__Context).'/statements/'.$Environment.'_'.$StatementFile.'.sql';

         if(!file_exists($File)){
            trigger_error('[MySQLHandler->executeStatement()] There\'s no statement file with name "'.($Environment.'_'.$StatementFile.'.sql').'" for given namespace "'.$Namespace.'" and current context "'.$this->__Context.'"!',E_USER_ERROR);
            exit(1);
          // end if
         }

         // Statement einlesen
         $Statement = file_get_contents($File);

         // Platzhalter ersetzen
         if(count($Params) > 0){

            foreach($Params as $Key => $Value){
               $Statement = str_replace('['.$Key.']',$Value,$Statement);
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
      *  Quotiert Daten für den Eintrag in die Datenbank.<br />
      *
      *  @param string $Value; String, der quotiert werden soll
      *  @return string $EscapedValue; Quotierter String
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 07.01.2008<br />
      */
      function escapeValue($Value){
         return mysql_real_escape_string($Value,$this->__dbConn);
       // end function
      }


      /**
      *  @public
      *
      *  Holt einen Datensatz, der aus einem ResultSet stammt aus der Datenbank ab.<br />
      *
      *  @param resource $ResultCursor; MySQL-ResultResource
      *  @return array $Data; Array der Ergebnisdaten
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 24.12.2005<br />
      *  Version 0.2, 23.02.2008 (Array wird nun direkt zurückgegeben)<br />
      */
      function fetchData($ResultCursor){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // Daten abholen
         return mysql_fetch_assoc($ResultCursor);

       // end function
      }


      /**
      *  @public
      *
      *  Setzt den Result-Pointer auf eine durch $offset angegebenen Ergebnis-Zeile.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 15.01.2006<br />
      */
      function setDataPointer($result,$offset){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // Pointer auf Daten-Set setzen
         @mysql_data_seek($result,$offset);

       // end function
      }


      /**
      *  @public
      *
      *  Liefert die Anzahl der durch ein Statement betroffene Datensätze.<br />
      *
      *  @return int $AffectedRows; Anzahl an betroffenen Datensätzen für eine Verbindungskennung.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      *  Version 0.2, 07.03.2008 (Verbindungskennung übergeben)<br />
      */
      function getAffectedRows(){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // Affected Rows für die aktuelle Verbindungskennung zurückgeben
         return mysql_affected_rows($this->__dbConn);

       // end function
      }


      /**
      *  @public
      *
      *  Liefert die ID des zuletzt eingefügten Datensatzes (Nummer des Primary Keys).<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      */
      function getLastID(){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // LastInsertID zurückgeben
         return $this->__lastInsertID;

       // end function
      }


      /**
      *  @public
      *
      *  Liefert die ID des zuletzt eingefügten Datensatzes (Nummer des Primary Keys).<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      */
      function getNumRows($Result){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // NumRows zurückgeben
         return mysql_num_rows($Result);

       // end function
      }


      /**
      *  @public
      *
      *  Führt ein Statement, das via String übergeben wurde aus.<br />
      *
      *  @param string $Statement; SQL-Statement
      *  @return ressource $Result; MySQL-Result-Ressource
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.01.2006<br />
      *  Version 0.2, 07.03.2008 (Bug behoben, dass Query nicht auf die korrekte Verbindung ausgeführt wurde)<br />
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
      *  Gibt die Version des Datenbank-Servers zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.03.2006<br />
      *  Version 0.2, 07.03.2008 (Verbindungskennung wird nun übergeben)<br />
      */
      function getServerInfo(){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // Daten zurückgeben
         return mysql_get_server_info($this->__dbConn);

       // end function
      }


      /**
      *  @public
      *
      *  Gibt den Namen der Datenbank zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.03.2006<br />
      */
      function getDatabaseName(){

         // Initialisiere Klasse
         $this->__initMySQLHandler();

         // DB-Name zurückgeben
         return $this->__db_name;

       // end function
      }


      /**
      *  @public
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