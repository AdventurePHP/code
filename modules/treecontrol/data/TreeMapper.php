<?php
   import('core::database','MySQLHandler');


   /**
   *  @package modules::treecontrol::data
   *  @module TreeMapper
   *
   *  Implementiert die Daten-Schicht für das Modul.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 28.02.2008<br />
   */
   class TreeMapper extends coreObject
   {

      /**
      *  @private
      *  Kennzeichnet, ob Klasse initialisiert wurde.
      */
      var $__isInitialized = false;


      /**
      *  @private
      *  Name der Primär-Schlüssel-Spalte.
      */
      var $__TableKey;


      /**
      *  @private
      *  Name der Datenbank-Tabelle.
      */
      var $__TableName;


      /**
      *  @private
      *  ID des Root-Knotens.
      */
      var $__RootNodeID;


      /**
      *  @private
      *  Name der Konfigurationsdatei für das Typen-Mapping.
      */
      var $__TypeMapping;


      /**
      *  @private
      *  Hashmap mit Objekt-Mapping-Informationen.
      */
      var $__ObjectMaps = array();


      function TreeMapper(){
      }


      /**
      *  @module __mapResult2DomainObject()
      *  @private
      *
      *  Implementiert die abstrakte init()-Methode.<br />
      *
      *  @param string $ConfigKey; Konfigurationsabschnitt
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.02.2008<br />
      */
      function init($ConfigKey){

         if($this->__isInitialized == false){

            // Konfiguration laden
            $Config = &$this->__getConfiguration('modules::treecontrol','treecontrol');

            // Konfiguration in lokale KLassenvariablen mappen
            $this->__TableKey = $Config->getValue($ConfigKey,'TableKey');
            $this->__TableName = $Config->getValue($ConfigKey,'TableName');
            $this->__RootNodeID = $Config->getValue($ConfigKey,'RootNodeID');
            $this->__TypeMapping = $Config->getValue($ConfigKey,'TypeMapping');

            // Als initialisiert kennzeichen
            $this->__isInitialized = true;

          // end if
         }

       // end function
      }


      /**
      *  @module __mapResult2DomainObject()
      *  @private
      *
      *  Mappt ein Resultset in ein Domain-Objekt. Es werden nur Werte gemappt, die in der<br />
      *  Mapping-Datei aufgeführt sind.<br />
      *
      *  @param array $ResultSet; Datenbank-Result-Array
      *  @return object $DomainObject; Domain-Objekt je nach Knotentyp
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.02.2008<br />
      */
      function __mapResult2DomainObject($ResultSet){

         // Typ bestimmen
         $Mapping = $this->__getMappingByTypeID($ResultSet[$this->__TableKey]);

         // Klasse einbinden
         if(!class_exists($Mapping['Class'])){
            import($Mapping['Namespace'],$Mapping['File']);
          // end if
         }

         // Klasse erzeugen
         $Object = new $Mapping['Class'];

         // Felder mappen
         $FieldMap = $this->__getFieldMapByTypeID($ResultSet[$this->__TableKey]);

         foreach($FieldMap as $Key => $Value){

            if(isset($ResultSet[$Key])){
               $Object->set($Value,$ResultSet[$Key]);
             // end if
            }

          // end foreach
         }

         // Objekt zurückgeben
         return $Object;

       // end function
      }


      /**
      *  @module __getMappingByTypeID()
      *  @private
      *
      *  Gibt das Mapping-Set für eine definierte ID zurück.<br />
      *
      *  @param int $TypeID; Id eines Typs
      *  @return array $ObjectMap; ObjektMap
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.02.2008<br />
      *  Version 0.2, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
      */
      function __getMappingByTypeID($TypeID){

         // ObjectMaps laden
         if(count($this->__ObjectMaps) == 0){
            $this->__loadObjectMaps();
          // end if
         }

         // Map suchen
         foreach($this->__ObjectMaps as $Key => $DUMMY){

            if($this->__ObjectMaps[$Key]['ID'] == $TypeID){
               return $this->__ObjectMaps[$Key];
             // end if
            }

          // end foreach
         }

         // Fehler anzeigen, falls keine Map gefunden
         $Reg = &Singleton::getInstance('Registry');
         $Environment = $Reg->retrieve('apf::core','Environment');
         trigger_error('[TreeMapper->__getClassNameByTypeID()] No map object configured for id "'.$TypeID.'". Please refere to configuration file "'.$Environment.'_'.$this->__TypeMapping.'.ini" in namespace "modules::treecontrol" and context "'.$this->__Context.'"!');

       // end function
      }


      /**
      *  @module __getFieldMapByTypeID()
      *  @private
      *
      *  Liefert ein Mapping-Array. Keys sind DB-Felder, Values Objekt-Member.<br />
      *
      *  @param int $TypeID; Id eines Typs
      *  @return array $FieldMap; FieldMap
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.02.2008
      */
      function __getFieldMapByTypeID($TypeID){

         // Mapping laden
         $Mapping = $this->__getMappingByTypeID($TypeID);

         // Map nach | trennen
         $Parts = explode('|',$Mapping['FieldMap']);
         $PartsCount = count($Parts);

         // FieldMap bauen
         $FieldMap = array();

         for($i = 0; $i < $PartsCount; $i++){
            $Temp = explode(':',$Parts[$i]);
            $FieldMap[trim($Temp[0])] = trim($Temp[1]);
          // end for
         }

         // Map zurückgeben
         return $FieldMap;

       // end function
      }


      /**
      *  @module __loadObjectMaps()
      *  @private
      *
      *  Füllt die ObjectMaps-Member-Variable.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.02.2008
      */
      function __loadObjectMaps(){
         $Config = &$this->__getConfiguration('modules::treecontrol',$this->__TypeMapping);
         $this->__ObjectMaps = $Config->getConfiguration();
       // end function
      }


      /**
      *
      *
      *
      *
      */
      function loadNodeByID($NodeID = null){

         // Standard-Knoten setzen
         if($NodeID == null){
            $NodeID = $this->__RootNodeID;
          // end if
         }

         // Knoten laden



       // end function
      }






      /**
      *  @module ladeDatenFuerPfad()
      *  @public
      *
      *  Läd alle Kinder für den übergebenen Pfad in ein Array.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.06.2005<br />
      *  Version 0.2, 01.07.2005<br />
      */
      function ladeDatenFuerPfad($Pfad){

         // Konfiguration laden
         $Config = &$this->__getConfiguration('modules::baumstruktur','baumstruktur');
         $this->__Config = $Config->getSection('Standard');

         // Daten selektieren
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT ".$this->__Config['T_Name']." AS Name, ".$this->__Config['T_VaterID']." AS VaterID, ".$this->__Config['T_Typ']." AS Typ, ".$this->__Config['T_Link']." AS Link, ".$this->__Config['T_Datum']." AS Datum, ".$this->__Config['T_Uhrzeit']." AS Uhrzeit, ".$this->__Config['T_Index']." AS PTIndex FROM ".$this->__Config['T_Tabelle']." WHERE ".$this->__Config['T_VaterID']." = '".$Pfad."' ORDER BY ".$this->__Config['T_Typ']." ASC, ".$this->__Config['T_Name']." ASC";
         $result = $SQL->executeTextStatement($select);

         $Kinder = array();

         while($data = $SQL->fetchData($result)){
            $Kinder[] = $this->__map2DomainObject($data);
          // end while
         }

         return $Kinder;

       // end function
      }


      /**
      *  @module ladeEinzelnenKnotenPerIndex()
      *  @public
      *
      *  Läd einen Knoten anhand eines Indexes.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.06.2005<br />
      *  Version 0.2, 01.07.2005<br />
      */
      function ladeEinzelnenKnotenPerIndex($Index){

         // Konfiguration laden
         $Config = &$this->__getConfiguration('modules::baumstruktur','baumstruktur');
         $this->__Config = $Config->getSection('Standard');

         // Daten selektieren
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT ".$this->__Config['T_Name']." AS Name, ".$this->__Config['T_VaterID']." AS VaterID, ".$this->__Config['T_Typ']." AS Typ, ".$this->__Config['T_Link']." AS Link, ".$this->__Config['T_Datum']." AS Datum , ".$this->__Config['T_Uhrzeit']." AS Uhrzeit, ".$this->__Config['T_Index']." AS PTIndex FROM ".$this->__Config['T_Tabelle']." WHERE ".$this->__Config['T_Index']." = '".$Index."'";
         $result = $SQL->executeTextStatement($select);
         $data = $SQL->fetchData($result);
         $Knoten = $this->__map2DomainObject($data);

         unset($SQL);

         return $Knoten;

       // end function
      }

    // end class
   }
?>