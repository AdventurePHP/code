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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('core::database','AbstractDatabaseHandler');
   register_shutdown_function('sqliteTerminateConnection');

   /**
   *  @namespace core::database
   *
   *  Wrapper-Funktion zum Schließen einer DB-Verbindung für SQLite.<br />
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
   *  @namespace core::database
   *  @class SQLiteHandler
   *
   *  Implementiert die Datenbankabstraktionsschicht für die SQLite-Schnittstelle.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 23.02.2008<br />
   */
   class SQLiteHandler extends AbstractDatabaseHandler
   {

      /**
      *  @protected
      *  Name der Logdatei der Abstraktionsschicht.
      */
      protected $__dbLogFileName = 'sqlite';


      /**
      *  @protected
      *  Modus, in dem die Datenbank geöffnet werden soll.
      */
      protected $__dbMode = 0666;


      /**
      *  @protected
      *  Trackt Fehlermeldungen von SQLite.
      */
      protected $__dbError = null;


      function SQLiteHandler(){
      }


      /**
      *  @protected
      *
      *  Abstrakte Interface-Methode für das Aufbauen einer Datenbank-Verbindung.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.02.2008<br />
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
      *  @protected
      *
      *  Abstrakte Interface-Methode für das Schließen einer Datenbank-Verbindung.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.02.2008<br />
      */
      protected function __close(){

         // Verbindung schließen
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

         // Statement ausführen
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


         // Ergebnis zurückgeben
         return $Result;

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die Methode zum Ausführen eines Statements in einer Statementdatei.<br />
      *
      *  @param string $Namespace; Namespace der Statementdatei
      *  @param string $StatementFile; Name der Statementdatei
      *  @param array $Params; Parameter für das Statement
      *  @param bool $ShowStatement; Indiziert, ob das Statement ausgegeben werden soll
      *  @return resource $Result; Result-Ressource
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      *  Version 0.2, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
      */
      function executeStatement($Namespace,$StatementFile,$Params = array(),$ShowStatement = false){

         // Dateinamen generieren und prüfen, ob Datei existiert
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


         // Statement ausführen
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


         // Ergebnis zurückgeben
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
      *  Gibt die Anzahl der durch einen Update oder Delete betroffenen Datensätze zurück.<br />
      *
      *  @param resource $ResultResource; Ergebniszeiger
      *  @return int $AffectedRows; Anzahl der betroffenen Datensätze
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