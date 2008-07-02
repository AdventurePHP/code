<?php
   import('core::database','MySQLHandler');
   import('core::singleton','Singleton');
   import('modules::teilnehmerliste::biz','teilnehmerObjekt');


   /**
   *  Package modules::teilnehmerliste
   *  Klasse teilnehmerlisteMapper
   *  Implementiert den DataMapper.
   *
   *  Christian Schäfer
   *  Version 0.1, 11.03.2006
   */
   class teilnehmerlisteMapper extends coreObject
   {

      function teilnehmerlisteMapper(){
      }


      /**
      *  Klasse ladeTeilnehmerListe
      *  Läd eine Liste von teilnehmerObjekt'en.
      *
      *  Christian Schäfer
      *  Version 0.1, 11.03.2006
      */
      function ladeTeilnehmerListe($Region){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

         $select = "SELECT * FROM mitglieder
                    WHERE Region = '".$Region."'
                    ORDER BY Betrieb ASC;";
         $result = $SQL->executeTextStatement($select);

         $teilnehmerListe = array();

         while($data = $SQL->fetchData($result)){
            $teilnehmerListe[] = $this->__mappeInDomainObjekt($data);
          // end while
         }

         return $teilnehmerListe;

       // end function
      }


      /**
      *  Klasse __mappeInDomainObjekt
      *  Mappt ein Datenbank-Result-Set in ein teilnehmerObjekt.
      *
      *  Christian Schäfer
      *  Version 0.1, 11.03.2006
      */
      function __mappeInDomainObjekt($Result){

         $T = new teilnehmerObjekt();

         if(isset($Result['Betrieb'])){
             $T->setzeAttribut('Betrieb',$Result['Betrieb']);
          // end if
         }
         if(isset($Result['Strasse'])){
             $T->setzeAttribut('Strasse',$Result['Strasse']);
          // end if
         }
         if(isset($Result['Ort'])){
             $T->setzeAttribut('Ort',$Result['Ort']);
          // end if
         }
         if(isset($Result['Telefon'])){
             $T->setzeAttribut('Telefon',$Result['Telefon']);
          // end if
         }
         if(isset($Result['Fax'])){
             $T->setzeAttribut('Fax',$Result['Fax']);
          // end if
         }
         if(isset($Result['Mobil'])){
             $T->setzeAttribut('Mobil',$Result['Mobil']);
          // end if
         }
         if(isset($Result['Email'])){
             $T->setzeAttribut('Email',$Result['Email']);
          // end if
         }
         if(isset($Result['Homepage'])){
             $T->setzeAttribut('Homepage',$Result['Homepage']);
          // end if
         }
         if(isset($Result['Region'])){
             $T->setzeAttribut('Region',$Result['Region']);
          // end if
         }

         return $T;

       // end function
      }

    // end class
   }
?>