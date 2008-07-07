<?php
   import('core::database','AbstractDatabaseHandler');

   register_shutdown_function('sqliteTerminateConnection');

   /**
   *  @package core::database
   *
   *  Wrapper-Funktion zum Schlie�en einer DB-Verbindung f�r SQLite.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 24.02.2008<br />
   */
   function sqliteTerminateConnection(){
      $SQLite = &Singleton::getInstance('SQLiteHandler');
      $SQLite->closeConnection();
    // end function
   }


   /**
   *  @package core::database
   *  @class SQLiteHandler
   *
   *  Implementiert die Datenbankabstraktionsschicht f�r die SQLite-Schnittstelle.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 23.02.2008<br />
   */
   class SQLiteHandler extends AbstractDatabaseHandler
   {

      /**
      *  @private
      *  Name der Logdatei der Abstraktionsschicht.
      */
      var $__dbLogFileName = 'sqlite';


      /**
      *  @private
      *  Modus, in dem die Datenbank ge�ffnet werden soll.
      */
      var $__dbMode = 0666;


      /**
      *  @private
      *  Trackt Fehlermeldungen von SQLite.
      */
      var $__dbError = null;


      function SQLiteHandler(){
      }


      /**
      *  @private
      *
      *  Abstrakte Interface-Methode f�r das Aufbauen einer Datenbank-Verbindung.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.02.2008<br />
      */
      function __connect(){

         // Verbindung �ffnen
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
      *  @private
      *
      *  Abstrakte Interface-Methode f�r das Schlie�en einer Datenbank-Verbindung.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.02.2008<br />
      */
      function __close(){

         // Verbindung schlie�en
         @sqlite_close($this->__dbConn);

         // Connection null setzen
         $this->__dbConn = null;

       // end function
      }


      /**
      *  @public
      *
      *  Konkrete Implementierung der executeTextStatement() Methode.<br />
      *
      *  @param string $Statement; SQL-Statement
      *  @return ressource $Result; SQLite-Result-Ressource
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.02.2008<br />
      *  Version 0.2, 24.02.2008 (Fehlermeldung erweitert)<br />
      */
      function executeTextStatement($Statement){

         // Statement ausf�hren
         $Result = sqlite_query($this->__dbConn,$Statement);

         // Fehler tracken
         if($Result === false){

            // Meldung generieren
            $Message = sqlite_error_string(sqlite_last_error($this->__dbConn));
            $Message .= ' (Statement: '.$Statement.')';

            // Fehler protokollieren
            $this->__dbLog->logEntry($this->__dbLogFileName,$Message,'ERROR');

            if($this->__dbDebug == true){
               trigger_error('[SQLiteHandler->executeTextStatement()] '.$Message);
             // end if
            }

          // end if
         }


         // $this->__lastInsertID setzen
         $this->__lastInsertID = sqlite_last_insert_rowid($this->__dbConn);


         // Ergebnis zur�ckgeben
         return $Result;

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die Methode zum Ausf�hren eines Statements in einer Statementdatei.<br />
      *
      *  @param string $Namespace; Namespace der Statementdatei
      *  @param string $StatementFile; Name der Statementdatei
      *  @param array $Params; Parameter f�r das Statement
      *  @param bool $ShowStatement; Indiziert, ob das Statement ausgegeben werden soll
      *  @return resource $Result; Result-Ressource
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      *  Version 0.2, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
      */
      function executeStatement($Namespace,$StatementFile,$Params = array(),$ShowStatement = false){

         // Dateinamen generieren und pr�fen, ob Datei existiert
         $Reg = &Singleton::getInstance('Registry');
         $Environment = $Reg->retrieve('apf::core','Environment');
         $File = APPS__PATH.'/config/'.str_replace('::','/',$Namespace).'/'.str_replace('::','/',$this->__Context).'/statements/'.$Environment.'_'.$StatementFile.'.sql';

         if(!file_exists($File)){
            trigger_error('[SQLiteHandler->executeStatement()] There\'s no statement file with name "'.($Environment.'_'.$StatementFile.'.sql').'" for given namespace "'.$Namespace.'" and current context "'.$this->__Context.'"!');
            exit();
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
            trigger_error('[SQLiteHandler->executeStatement()] Current Statement: '.$Statement);
          // end if
         }


         // Statement ausf�hren
         $Result = sqlite_query($this->__dbConn,$Statement);


         // Fehler tracken
         if($Result === false){

            // Meldung generieren
            $Message = sqlite_error_string(sqlite_last_error($this->__dbConn));
            $Message .= ' (Statement: '.$Statement.')';

            // Fehler protokollieren
            $this->__dbLog->logEntry($this->__dbLogFileName,$Message,'ERROR');

            if($this->__dbDebug == true){
               trigger_error('[SQLiteHandler->executeTextStatement()] '.$Message);
             // end if
            }

          // end if
         }


         // $this->__lastInsertID setzen
         $this->__lastInsertID = sqlite_last_insert_rowid($this->__dbConn);


         // Ergebnis zur�ckgeben
         return $Result;

       // end function
      }


      /**
      *  @public
      *
      *  Holt einen Datensatz, der aus einem ResultResource stammt aus der Datenbank ab.<br />
      *
      *  @param resource $ResultResource; SQLite ResultResource
      *  @return ressource $Result; SQLite-Result-Ressource
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.02.2008<br />
      */
      function fetchData($ResultResource){
         return sqlite_fetch_array($ResultResource,SQLITE_ASSOC);
       // end function
      }


      /**
      *  @public
      *
      *  Implementiert eine Methode zum Escapen von speziellen Zeichen.<br />
      *
      *  @param string $Value; Zu escapender String
      *  @return string $EcapedValue; Escapter String
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.02.2008<br />
      */
      function escapeValue($Value){
         return sqlite_escape_string($Value);
       // end function
      }


      /**
      *  @public
      *
      *  Gibt die Anzahl der durch einen Update oder Delete betroffenen Datens�tze zur�ck.<br />
      *
      *  @param resource $ResultResource; Ergebniszeiger
      *  @return int $AffectedRows; Anzahl der betroffenen Datens�tze
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      function getAffectedRows($ResultResource){
         return sqlite_num_rows($ResultResource);
       // end function
      }

    // end class
   }
?>