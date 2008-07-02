<?php
   /**
   *  @package core::database
   *  @class connectionManager
   *
   *  Implementiert eine Fabric f�r Datenbank-Handler. Muss als ServiceObject erzeugt werden,<br />
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
   *  Version 0.2, 24.02.2008 (Bestehende Connections werden nun gecached)<br />
   */
   class connectionManager extends coreObject
   {

      /**
      *  @private
      *  Cache f�r die bestehenden Connections
      */
      var $__Connections = array();


      function connectionManager(){
      }


      /**
      *  @public
      *
      *  Gibt einen initialisierten Handler f�r eine Datenbank-Verbindung zur�ck.<br />
      *
      *  @param string $ConnectionKey; Sektion der Connection-Konfiguration
      *  @return object $DatebaseHandler; Instanz auf die Implementierung eines AbstractDatabaseHandler's
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.11.2007<br />
      *  Version 0.2, 23.02.2008<br />
      *  Version 0.3, 24.02.2008 (Caching eingef�hrt; kein ConfigOffset => E_USER_ERROR)<br />
      *  Version 0.4, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
      */
      function &getConnection($ConnectionKey){

         // Pr�fen, ob bereits eine Instanz erzeugt wurde
         $ConnectionHash = md5($ConnectionKey);

         if(isset($this->__Connections[$ConnectionHash])){
            return $this->__Connections[$ConnectionHash];
          // end if
         }


         // Konfiguration lesen
         $Config = &$this->__getConfiguration('core::database','connections');


         // Sektion gem�� ConnectionKey holen
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
         $this->__Connections[$ConnectionHash] = $this->__getAndInitServiceObject('core::database',$Section['DB.Type'].'Handler',$Section);


         // Handler zur�ckgeben
         return $this->__Connections[$ConnectionHash];

       // end function
      }

    // end class
   }
?>