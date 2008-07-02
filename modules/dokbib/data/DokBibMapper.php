<?php
   import('modules::dokbib::biz','SuchErgebnis');
   import('core::database','MySQLHandler');


   /**
   *  @package modules::dokbib::data
   *  @module DokBibMapper
   *
   *  Implementiert die Datenschicht der Dokumenten Bibliothek.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 06.07.2005<br />
   *  Version 0.2, 07.07.2005<br />
   *  Version 0.3, 09.07.2005<br />
   *  Version 0.4, 17.12.2006 (Methoden für Pfad-Laden privatisiert;Dokumentation ergänzt)<br />
   */
   class DokBibMapper extends coreObject
   {

      /**
      *  @private
      *  Hilfsvariable für die Pfad-Gewinnung
      */
      var $__Pfad = array();


      function DokBibMapper(){
      }


      /**
      *  @module ladeSuchErgebnisse()
      *  @public
      *
      *  Läd die Sichergebnisse anhand eines Suchbegriffs.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.07.2005<br />
      *  Version 0.2, 07.07.2005<br />
      */
      function ladeSuchErgebnisse($SuchText){

         // Tabellen-Konfiguration ziehen
         $Config = &$this->__getConfiguration('modules::baumstruktur','baumstruktur');
         $this->__Config = $Config->getSection('Standard');


         // Daten selektieren
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT ".$this->__Config['T_Name']." AS Name, ".$this->__Config['T_Index']." AS PTIndex, ".$this->__Config['T_Datum']." AS Datum, ".$this->__Config['T_Uhrzeit']." AS Uhrzeit, ".$this->__Config['T_Link']." AS Link, ".$this->__Config['T_Typ']." AS Typ FROM ".$this->__Config['T_Tabelle']." WHERE ".$this->__Config['T_Name']." LIKE '%".$SuchText."%' LIMIT 15";
         $result = $SQL->executeTextStatement($select);

         $Ergebnisse = array();

         while($data = $SQL->fetchData($result)){
            $Ergebnisse[] = $this->__mappeAlsErgebnisDomainObjekt($data);
          // end while
         }


         // Ergebnis zurückgeben
         return $Ergebnisse;

       // end function
      }


      /**
      *  @module __mappeAlsErgebnisDomainObjekt()
      *  @private
      *
      *  Mappt das Suchergebnis-Array in ein Domain Objekt.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.07.2005<br />
      *  Version 0.2, 07.07.2005<br />
      *  Version 0.3, 08.07.2005<br />
      */
      function __mappeAlsErgebnisDomainObjekt($ResultSet){

         $SuchErgebnis = new SuchErgebnis();

         if(isset($ResultSet['Name'])){
            $SuchErgebnis->setzeName($ResultSet['Name']);
          // end if
         }
         if(isset($ResultSet['Datum'])){
            $SuchErgebnis->setzeDatum($ResultSet['Datum']);
          // end if
         }
         if(isset($ResultSet['Uhrzeit'])){
            $SuchErgebnis->setzeUhrzeit($ResultSet['Uhrzeit']);
          // end if
         }
         if(isset($ResultSet['PTIndex'])){
            $SuchErgebnis->setzePfad($this->__ladePfad($ResultSet['PTIndex']));
          // end if
         }
         if(isset($ResultSet['Link'])){
            $SuchErgebnis->setzeLink($ResultSet['Link']);
          // end if
         }
         if(isset($ResultSet['Typ'])){
            $SuchErgebnis->setzeTyp($ResultSet['Typ']);
          // end if
         }

         return $SuchErgebnis;

       // end function
      }


      /**
      *  @module __ladePfad()
      *  @private
      *
      *  Läd den Pfad eines Knotens.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.07.2005<br />
      *  Version 0.2, 07.07.2005<br />
      */
      function __ladePfad($Index){

         $this->__ladePfadRekursiv($Index);
         $Pfad = array_reverse($this->__Pfad);
         $this->__Pfad = array();  // Member-Variable für erneuten Durchlauf zurücksetzen

         return $Pfad;

       // end function
      }


      /**
      *  @module __ladePfadRekursiv()
      *  @private
      *
      *  Läd den Pfad eines einzelnen Knotens. Wird zum Laden des rekursiven Laden<br />
      *  des Pfades verwendet.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.07.2005<br />
      *  Version 0.2, 07.07.2005<br />
      *  Version 0.3, 09.07.2005<br />
      *  Version 0.4, 22.07.2005<br />
      *  Version 0.5, 17.12.2006 (Rekursives Laden beinhaltet nun auch die ID's der Vater-Ordner)<br />
      */
      function __ladePfadRekursiv($Index){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT ".$this->__Config['T_Name']." AS Name, ".$this->__Config['T_VaterID']." AS VaterID, ".$this->__Config['T_Typ']." AS Typ FROM ".$this->__Config['T_Tabelle']." WHERE ".$this->__Config['T_Index']." = '".$Index."';";
         $result = $SQL->executeTextStatement($select);
         $data = $SQL->fetchData($result);

         if($data['VaterID'] != '0'){

            // Nur Ordner in die Pfadangeben mit aufnehmen
            if($data['Typ'] == 'dir'){
               $Offset = count($this->__Pfad);
               $this->__Pfad[$Offset]['Name'] = $data['Name'];
               $this->__Pfad[$Offset]['ID'] = $Index;
             // end if
            }

            // Für vorhandenen Vater-Knoten nochmals aufrufen
            $this->__ladePfadRekursiv($data['VaterID']);

          // end if
         }
         else{

            // Nur Ordner in die Pfadangeben mit aufnehmen
            if($data['Typ'] == 'dir'){
               $Offset = count($this->__Pfad);
               $this->__Pfad[$Offset]['Name'] = $data['Name'];
               $this->__Pfad[$Offset]['ID'] = $Index;
             // end if
            }

          // end else
         }

       // end function
      }

    // end class
   }
?>