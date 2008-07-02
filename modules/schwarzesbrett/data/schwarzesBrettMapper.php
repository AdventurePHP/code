<?php
   import('modules::schwarzesbrett::biz','schwarzesBrettEintrag');
   import('core::database','MySQLHandler');
   import('core::singleton','Singleton');
   import('core::logging','Logger');


   /**
   *  @package modules::schwarzesbrett::data
   *  @module schwarzesBrettMapper
   *
   *  Implementiert die Daten-Schicht des Schwarzen Brettes.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 04.01.2006<br />
   *  Version 0.2, 11.03.2006<br />
   */
   class schwarzesBrettMapper extends coreObject
   {

      function schwarzesBrettMapper(){
      }


      /**
      *  @module speichereEintrag()
      *  @public
      *
      *  Speichert einen im Formular erstellten Eintrag.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      */
      function speichereEintrag($E){

         $Inhalte = array('Datum' => $E->zeigeAttribut('Datum'),
                          'Uhrzeit' => $E->zeigeAttribut('Uhrzeit'),
                          'Text' => $E->zeigeAttribut('Text'),
                          'Nachname' => $E->zeigeAttribut('Nachname'),
                          'Vorname' => $E->zeigeAttribut('Vorname'),
                          'Strasse' => $E->zeigeAttribut('Strasse'),
                          'PLZ' => $E->zeigeAttribut('PLZ'),
                          'Ort' => $E->zeigeAttribut('Ort'),
                          'Tel' => $E->zeigeAttribut('Tel'),
                          'Fax' => $E->zeigeAttribut('Fax'),
                          'EMail' => $E->zeigeAttribut('EMail'),
                          'Anhang' => $E->zeigeAttribut('Anhang')
                         );
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $SQL->executeStatement('modules::schwarzesbrett','datensatz_speichern',$Inhalte);

       // end function
      }


      /**
      *  @module ladeEintrag()
      *  @public
      *
      *  Läd einen Eintrag.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      *  Version 0.2, 05.08.2006<br />
      */
      function ladeEintrag($Eintrag){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $result = $SQL->executeStatement('modules::schwarzesbrett','datensatz_per_id_laden',array('ID' => $Eintrag));
         $data = $SQL->fetchData($result);

         return $this->__mappeInDomainObjekt($data);

       // end function
      }


      /**
      *  @module loescheAlteEintraege()
      *  @public
      *
      *  Löscht beim Laden diejenigen Einträge, die vor dem $AblaufDatum eingetragen wurden.<br />
      *  Diese werden jedoch nur logisch per Flag gelöscht.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.03.2006<br />
      *  Version 0.2, 06.08.2006<br />
      */
      function loescheAlteEintraege($ExpirationDate){
         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $SQL->executeStatement('modules::schwarzesbrett','alte_daten_loeschen',array('AblaufDatum' => $ExpirationDate));
       // end function
      }


      /**
      *  @module ermittleEintraegeAnzahl()
      *  @private
      *
      *  Mappt die Datenbank-Result-Sets in ein Domain-Objekt.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      */
      function __mappeInDomainObjekt($ResultSet){

         $E = new schwarzesBrettEintrag();

         if(isset($ResultSet['Text'])){
            $E->setzeAttribut('Text',$ResultSet['Text']);
          // end if
         }
         if(isset($ResultSet['Datum'])){
            $E->setzeAttribut('Datum',$ResultSet['Datum']);
          // end if
         }
         if(isset($ResultSet['Uhrzeit'])){
            $E->setzeAttribut('Uhrzeit',$ResultSet['Uhrzeit']);
          // end if
         }
         if(isset($ResultSet['Vorname'])){
            $E->setzeAttribut('Vorname',$ResultSet['Vorname']);
          // end if
         }
         if(isset($ResultSet['Name'])){
            $E->setzeAttribut('Nachname',$ResultSet['Name']);
          // end if
         }
         if(isset($ResultSet['Strasse'])){
            $E->setzeAttribut('Strasse',$ResultSet['Strasse']);
          // end if
         }
         if(isset($ResultSet['PLZ'])){
            $E->setzeAttribut('PLZ',$ResultSet['PLZ']);
          // end if
         }
         if(isset($ResultSet['Ort'])){
            $E->setzeAttribut('Ort',$ResultSet['Ort']);
          // end if
         }
         if(isset($ResultSet['Telefon'])){
            $E->setzeAttribut('Tel',$ResultSet['Telefon']);
          // end if
         }
         if(isset($ResultSet['Fax'])){
            $E->setzeAttribut('Fax',$ResultSet['Fax']);
          // end if
         }
         if(isset($ResultSet['EMail'])){
            $E->setzeAttribut('EMail',$ResultSet['EMail']);
          // end if
         }
         if(isset($ResultSet['Link'])){
            $E->setzeAttribut('Anhang',$ResultSet['Link']);
          // end if
         }

         return $E;

       // end function
      }

    // end class
   }
?>