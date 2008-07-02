<?php
   import('modules::baumstruktur::biz','BaumKnoten');
   import('core::database','MySQLHandler');
   import('core::singleton','Singleton');


   /**
   *  @package modules::baumstruktur::data
   *  @module BaumMapper
   *
   *  Implementiert den Baum-Mapper der Daten-Schicht.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 28.06.2005<br />
   *  Version 0.2, 01.07.2005<br />
   *  Version 0.3, 04.12.2005<br />
   */
   class BaumMapper extends coreObject
   {

      function BaumMapper(){
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
      *  @module ladeDatenFuerRoot()
      *  @public
      *
      *  Läd das Root-Objekt.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.06.2005<br />
      *  Version 0.2, 01.07.2005<br />
      */
      function ladeDatenFuerRoot(){

         // Konfiguration laden
         $Config = &$this->__getConfiguration('modules::baumstruktur','baumstruktur');
         $this->__Config = $Config->getSection('Standard');

         // Daten selektieren
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT ".$this->__Config['T_Name']." AS Name, ".$this->__Config['T_VaterID']." AS VaterID, ".$this->__Config['T_Typ']." AS Typ, ".$this->__Config['T_Link']." AS Link, ".$this->__Config['T_Datum']." AS Datum , ".$this->__Config['T_Uhrzeit']." AS Uhrzeit, ".$this->__Config['T_Index']." AS PTIndex FROM ".$this->__Config['T_Tabelle']." WHERE ".$this->__Config['T_VaterID']." = '0'";
         $result = $SQL->executeTextStatement($select);
         $data = $SQL->fetchData($result);
         $Root = $this->__map2DomainObject($data);

         unset($SQL);

         return $Root;

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


      /**
      *  @module ladeAlleOrdner()
      *  @public
      *
      *  Läd alle Ordner ohne Beziehungen.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.06.2005<br />
      *  Version 0.2, 01.07.2005<br />
      */
      function ladeAlleOrdner(){

         // Konfiguration laden
         $Config = &$this->__getConfiguration('modules::baumstruktur','baumstruktur');
         $this->__Config = $Config->getSection('Standard');

         // Daten selektieren
         $Ordner = array();

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         $select = "SELECT ".$this->__Config['T_Name']." AS Name, ".$this->__Config['T_VaterID']." AS VaterID, ".$this->__Config['T_Typ']." AS Typ, ".$this->__Config['T_Link']." AS Link, ".$this->__Config['T_Datum']." AS Datum , ".$this->__Config['T_Uhrzeit']." AS Uhrzeit, ".$this->__Config['T_Index']." AS PTIndex FROM ".$this->__Config['T_Tabelle']." WHERE ".$this->__Config['T_Typ']." = 'dir' ORDER BY ".$this->__Config['T_Name']." ASC";
         $result = $SQL->executeTextStatement($select);

         while($data = $SQL->fetchData($result)){
            $Ordner[] = $this->__map2DomainObject($data);
          // end while
         }

         return $Ordner;

       // end function
      }


      /**
      *  @module speichereOrdnerKnoten()
      *  @public
      *
      *  Speichert einen Ordner-Knoten ab.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.06.2005<br />
      *  Version 0.2, 01.07.2005<br />
      *  Version 0.3, 12.03.2006<br />
      */
      function speichereOrdnerKnoten($Knoten){

         // Konfiguration laden
         $Config = &$this->__getConfiguration('modules::baumstruktur','baumstruktur');
         $this->__Config = $Config->getSection('Standard');

         // Prüfen auf Vorhandensein
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT ".$this->__Config['T_Name']." AS Name FROM ".$this->__Config['T_Tabelle']." WHERE ".$this->__Config['T_Index']." = '".$Knoten->zeigeIndex()."';";
         $result = $SQL->executeTextStatement($select);
         $Anz = $SQL->getNumRows($result);

         // Bei nicht vorhandenem Knoten -> einfügen
         if($Anz == 0){
            $insert = "INSERT INTO ".$this->__Config['T_Tabelle']." (".$this->__Config['T_Name'].", ".$this->__Config['T_VaterID'].", ".$this->__Config['T_Typ'].", ".$this->__Config['T_Datum'].", ".$this->__Config['T_Uhrzeit'].") VALUES ('".$Knoten->zeigeName()."', '".$Knoten->zeigeVaterID()."', '".$Knoten->zeigeTyp()."', '".(dateTimeManager::generateDate())."', '".(dateTimeManager::generateTime())."');";
            $Result = $SQL->executeTextStatement($insert);
          // end if
         }

         // Bei vorhandenem Knoten -> updaten
         if($Anz == 1){
            $update = "UPDATE ".$this->__Config['T_Tabelle']." SET ".$this->__Config['T_Name']." = '".$Knoten->zeigeName()."', ".$this->__Config['T_VaterID']." = '".$Knoten->zeigeVaterID()."', ".$this->__Config['T_Typ']." = '".$Knoten->zeigeTyp()."', ".$this->__Config['T_Datum']." = '".(dateTimeManager::generateDate())."', ".$this->__Config['T_Uhrzeit']." = '".(dateTimeManager::generateTime())."' WHERE ".$this->__Config['T_Index']." = '".$Knoten->zeigeIndex()."';";
            $Result = $SQL->executeTextStatement($update);
          // end if
         }

         return $Result;

       // end function
      }


      /**
      *  @module speichereDateiKnoten()
      *  @public
      *
      *  Speichert einen Ordner-Knoten ab.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.06.2005<br />
      *  Version 0.2, 01.07.2005<br />
      */
      function speichereDateiKnoten($Knoten){

         // Konfiguration laden
         $Config = &$this->__getConfiguration('modules::baumstruktur','baumstruktur');
         $this->__Config = $Config->getSection('Standard');

         // Prüfen auf Vorhandensein
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT ".$this->__Config['T_Name']." AS Name FROM ".$this->__Config['T_Tabelle']." WHERE ".$this->__Config['T_Index']." = '".$Knoten->zeigeIndex()."'";
         $result = $SQL->executeTextStatement($select);
         $Anz = $SQL->getNumRows($result);

         // Bei nicht vorhandenem Knoten -> einfügen
         if($Anz == 0){
            $insert = "INSERT INTO ".$this->__Config['T_Tabelle']." (".$this->__Config['T_Name'].", ".$this->__Config['T_VaterID'].", ".$this->__Config['T_Typ'].", ".$this->__Config['T_Link'].", ".$this->__Config['T_Datum'].", ".$this->__Config['T_Uhrzeit'].") VALUES ('".$Knoten->zeigeName()."', '".$Knoten->zeigeVaterID()."', '".$Knoten->zeigeTyp()."', '".$Knoten->zeigeLink()."', '".$Knoten->zeigeDatum()."', '".$Knoten->zeigeUhrzeit()."')";
            $Result = $SQL->executeTextStatement($insert);
          // end if
         }

         // Bei vorhandenem Knoten -> updaten
         if($Anz == 1){
            $update = "UPDATE ".$this->__Config['T_Tabelle']." SET ".$this->__Config['T_Name']." = '".$Knoten->zeigeName()."', ".$this->__Config['T_VaterID']." = '".$Knoten->zeigeVaterID()."', ".$this->__Config['T_Typ']." = '".$Knoten->zeigeTyp()."', ".$this->__Config['T_Link']." = '".$Knoten->zeigeLink()."', ".$this->__Config['T_Datum']." = '".$Knoten->zeigeDatum()."', ".$this->__Config['T_Uhrzeit']." = '".$Knoten->zeigeUhrzeit()."' WHERE ".$this->__Config['T_Index']." = '".$Knoten->zeigeIndex()."'";
            $Result = $SQL->executeTextStatement($update);
          // end if
         }

         return $Result;

       // end function
      }


      /**
      *  @module hatKnotenKinder()
      *  @public
      *
      *  Prüft, ob ein Knoten Kinder hat.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.06.2005<br />
      *  Version 0.2, 01.07.2005<br />
      */
      function hatKnotenKinder($Knoten){

         // Konfiguration laden
         $Config = &$this->__getConfiguration('modules::baumstruktur','baumstruktur');
         $this->__Config = $Config->getSection('Standard');

         // Daten selektieren
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT ".$this->__Config['T_Name']." FROM ".$this->__Config['T_Tabelle']." WHERE ".$this->__Config['T_VaterID']." = '".$Knoten."'";
         $result = $SQL->executeTextStatement($select);
         $Anz = $SQL->getNumRows($result);

         if($Anz > 0){
            return '1';
          // end if
         }
         if($Anz == 0){
            return '0';
          // end if
         }

       // end function
      }


      /**
      *  @module loescheKnoten()
      *  @public
      *
      *  Löscht einen Knoten anhand des übergebenen Index.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.06.2005<br />
      *  Version 0.2, 01.07.2005<br />
      */
      function loescheKnoten($Knoten){

         // Konfiguration laden
         $Config = &$this->__getConfiguration('modules::baumstruktur','baumstruktur');
         $this->__Config = $Config->getSection('Standard');

         // Daten selektieren
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "DELETE FROM ".$this->__Config['T_Tabelle']." WHERE ".$this->__Config['T_Index']." = '".$Knoten."'";
         $SQL->executeTextStatement($select);

       // end function
      }


      /**
      *  @module __mappeAlsDomainObjekt()
      *  @private
      *
      *  Mappt ein ResultSet in ein KnotenObjekt.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.06.2005<br />
      *  Version 0.2, 01.07.2005<br />
      *  Version 0.3, 14.07.2005<br />
      */
      function __map2DomainObject($ResultSet){

         $Knoten = new BaumKnoten();

         if(isset($ResultSet['Name'])){
            $Knoten->setzeName($ResultSet['Name']);
          // end if
         }
         if(isset($ResultSet['VaterID'])){
            $Knoten->setzeVaterID($ResultSet['VaterID']);
          // end if
         }
         if(isset($ResultSet['Typ'])){
            $Knoten->setzeTyp($ResultSet['Typ']);
          // end if
         }
         if(isset($ResultSet['Link'])){
            $Knoten->setzeLink($ResultSet['Link']);
          // end if
         }
         if(isset($ResultSet['Datum'])){
            $Knoten->setzeDatum($ResultSet['Datum']);
          // end if
         }
         if(isset($ResultSet['Uhrzeit'])){
            $Knoten->setzeUhrzeit($ResultSet['Uhrzeit']);
          // end if
         }
         if(isset($ResultSet['PTIndex'])){
            $Knoten->setzeIndex($ResultSet['PTIndex']);
          // end if
         }

         return $Knoten;

       // end function
      }

    // end class
   }
?>