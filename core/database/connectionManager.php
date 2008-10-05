<?php
   /**
   *  @package core::database
   *  @class connectionManager
   *
   *  Implementiert eine Fabric für Datenbank-Handler. Muss als ServiceObject erzeugt werden,<br />
   *  da sonst der Singleton-Cache nicht korrekt benutzt werden kann und die Applikation dann<br />
   *  i.d.R. unperformant werden!<br />
   *  Beispiel:<br />
   *  <br />
   *  $connMgr = &$this->__getServiceObject('core::database','ConnectionManager');<br />
   *  $DBHandler = &$connMgr->getConnection('<ConnectionKey>');<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 09.11.2007<br />
   *  Version 0.2, 24.02.2008 (Existing connections are cached now)<br />
   */
   class connectionManager extends coreObject
   {

      /**
      *  @private
      *  Cache for existing database connections.
      */
      var $__Connections = array();


      function connectionManager(){
      }


      /**
      *  @public
      *
      *  Gibt einen initialisierten Handler für eine Datenbank-Verbindung zurück.<br />
      *
      *  @param string $ConnectionKey desired configuration section
      *  @return object $DatebaseHandler instance of an AbstractDatabaseHandler connection layer
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.11.2007<br />
      *  Version 0.2, 23.02.2008<br />
      *  Version 0.3, 24.02.2008 (Caching eingeführt; kein ConfigOffset => E_USER_ERROR)<br />
      *  Version 0.4, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
      *  Version 0.5, 05.10.2008 (Bugfix: usage of two or more identical connections (e.g. of type MySQLx) led to interferences. Thus, service object usage was changed (line 84))<br />
      */
      function &getConnection($ConnectionKey){

         // Prüfen, ob bereits eine Instanz erzeugt wurde
         $ConnectionHash = md5($ConnectionKey);

         if(isset($this->__Connections[$ConnectionHash])){
            return $this->__Connections[$ConnectionHash];
          // end if
         }

         // Konfiguration lesen
         $Config = &$this->__getConfiguration('core::database','connections');

         // Sektion gemäß ConnectionKey holen
         $Section = $Config->getSection($ConnectionKey);

         if($Section == null){
            $Reg = &Singleton::getInstance('Registry');
            $Environment = $Reg->retrieve('apf::core','Environment');
            trigger_error('[connectionManager::getConnection()] The given configuration section ("'.$ConnectionKey.'") does not exist in configuration file "'.$Environment.'_connections.ini" in namespace "core::database"!',E_USER_ERROR);
            exit(1);
          // end if
         }

         // Handler einbinden
         if(!class_exists($Section['DB.Type'].'Handler')){
            import('core::database',$Section['DB.Type'].'Handler');
          // end if
         }

         // Handler erzeugen und cachen
         $this->__Connections[$ConnectionHash] = &$this->__getAndInitServiceObject('core::database',$Section['DB.Type'].'Handler',$Section,'NORMAL');

         // Handler zurückgeben
         return $this->__Connections[$ConnectionHash];

       // end function
      }

    // end class
   }
?>