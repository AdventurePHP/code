<?php
   import('core::logging','Logger');


   /**
   *  @package core::database
   *  @class AbstractDatabaseHandler
   *  @abstract
   *
   *  Abstrakter DatenbankHandler. Basis für konkrete Datenbank-Handler.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 10.02.2008<br />
   */
   class AbstractDatabaseHandler extends coreObject
   {

      /**
      *  @private
      *  Kennzeichnet, ob der Handler bereits initialisiert wurde.
      */
      var $__isInitialized = false;


      /**
      *  @private
      *  Datenbank-Server.
      */
      var $__dbHost = null;


      /**
      *  @private
      *  Datenbank-Benutzer.
      */
      var $__dbUser = null;


      /**
      *  @private
      *  Datenbank-Passwort.
      */
      var $__dbPass = null;


      /**
      *  @private
      *  Datenbank-Name.
      */
      var $__dbName = null;


      /**
      *  @private
      *  Datenbank Debug-Mode?.
      */
      var $__dbDebug = false;


      /**
      *  @private
      *  Datenbank-Verbindung.
      */
      var $__dbConn = null;


      /**
      *  @private
      *  Instanz des Loggers.
      */
      var $__dbLog = null;


      /**
      *  @private
      *  Name der Log-Datei. Muss in der konkreten Implementierung definiert werden.
      */
      var $__dbLogFileName;


      /**
      *  @private
      *  ID des letzten Inserts.
      */
      var $__lastInsertID;


      function AbstractDatabaseHandler(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte init()-Methode um eine Erzeugung per serviceManager<br />
      *  zu unterstützen. Initialisiert den Handler einmalig.<br />
      *
      *  @param array $ConfigSection; Array mit Konfigurationsparametern
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2008<br />
      */
      function init($ConfigSection){

         // Prüfen, ob bereits initialisiert
         if($this->__isInitialized == false){

            // Server setzen
            if(isset($ConfigSection['DB.Host'])){
               $this->__dbHost = $ConfigSection['DB.Host'];
             // end if
            }

            // Benutzer setzen
            if(isset($ConfigSection['DB.User'])){
               $this->__dbUser = $ConfigSection['DB.User'];
             // end if
            }

            // Passwort setzen
            if(isset($ConfigSection['DB.Pass'])){
               $this->__dbPass = $ConfigSection['DB.Pass'];
             // end if
            }

            // Name der Datenbank setzen
            $this->__dbName = $ConfigSection['DB.Name'];

            // Debug Mode setzen
            if(isset($ConfigSection['DB.DebugMode'])){

               if($ConfigSection['DB.DebugMode'] == 'true' || $ConfigSection['DB.DebugMode'] == '1'){
                  $this->__dbDebug = true;
                // end if
               }
               else{
                  $this->__dbDebug = false;
                // end else
               }

             // end if
            }

            // Instanz des Loggers erzeugen
            $this->__dbLog = &Singleton::getInstance('Logger');

            // Handler als initialisiert kennzeichnen
            $this->__isInitialized = true;

            // Zur Datenbank verbinden
            $this->__connect();

          // end if
         }

       // end function
      }


      /**
      *  @private
      *  @abstract
      *
      *  Abstrakte Interface-Methode für das Aufbauen einer Datenbank-Verbindung.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2008<br />
      */
      function __connect(){
      }


      /**
      *  @private
      *  @abstract
      *
      *  Abstrakte Interface-Methode für das Schließen einer Datenbank-Verbindung.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2008<br />
      */
      function __close(){
      }


      /**
      *  @public
      *
      *  Öffentliche Methode zum Trennen der Datenbank-Verbindung (für shutdown function).<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2008<br />
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
      *  @abstract
      *
      *  Methode zum Ausführen eines Statements in einer Statementdatei.<br />
      *
      *  @param string $Namespace; Namespace der Statementdatei
      *  @param string $StatementFile; Name der Statementdatei
      *  @param array $Params; Parameter für das Statement
      *  @param bool $ShowStatement; Indiziert, ob das Statement ausgegeben werden soll
      *  @return resource $Result; Result-Ressource
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2008<br />
      */
      function executeStatement($Namespace,$StatementFile,$Params = array(),$ShowStatement = false){
      }


      /**
      *  @public
      *  @abstract
      *
      *  Methode zum Ausführen eines Statements.<br />
      *
      *  @param string $Statement; Datenbankstatement
      *  @return resource $Result; Result-Ressource
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2008<br />
      */
      function executeTextStatement($Statement){
      }


      /**
      *  @public
      *  @abstract
      *
      *  Methode zum Escapen von speziellen Zeichen.<br />
      *
      *  @param string $Value; Zu escapender String
      *  @return string $EcapedValue; Escapter String
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.02.2008<br />
      */
      function escapeValue($Value){
      }


      /**
      *  @public
      *  @abstract
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
      }

    // end class
   }
?>