<?php
   import('core::database','MySQLHandler');


   /**
   *  @package core::database
   *  @class MySQLxHandler
   *
   *  Kompatibilitätsklasse für den MySQLHandler zur Benutzung über den connectionManager.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.02.2008<br />
   */
   class MySQLxHandler extends MySQLHandler
   {

      function MySQLxHandler(){
      }


      /**
      *  @public
      *
      *  Initialisiert den MySQLxHandler.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.02.2008<br />
      */
      function init($ConfigSection){

         // Falls noch nicht initialisiert wurde initialisieren
         if($this->__isInitialized == false){

            // Zugangsdaten auslesen
            $this->__dbHost = $ConfigSection['DB.Host'];
            $this->__dbUser = $ConfigSection['DB.User'];
            $this->__dbPass = $ConfigSection['DB.Pass'];
            $this->__dbName = $ConfigSection['DB.Name'];

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

            // Logdatei festlegen (Instanz des Loggers)
            $this->__dbLog = &Singleton::getInstance('Logger');

            // Klasse als initialisiert kennzeichnen
            $this->__isInitialized = true;

            // Zur DB verbinden
            $this->__connect();

          // end if
         }

       // end function
      }

    // end class
   }
?>