<?php
   import('modules::shoutbox::biz','ShoutboxEintragObjekt');
   import('core::database','MySQLHandler');
   import('core::singleton','Singleton');


   /**
   *  Klasse ShoutboxMapper
   *  Implementiert den Mapper der Daten-Schicht
   *
   *  Christian Schäfer
   *  Version 0.1, 05.05.2005
   */
   class ShoutboxMapper extends coreObject
   {

      function ShoutboxMapper(){
      }


      function speichereEintrag($ShoutboxEintragObjekt){

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $insert = "INSERT INTO shoutbox (Text, Datum, Uhrzeit) VALUES ('".htmlspecialchars($ShoutboxEintragObjekt->zeigeText())."', '".$ShoutboxEintragObjekt->zeigeDatum()."', '".$ShoutboxEintragObjekt->zeigeUhrzeit()."')";
         $SQL->executeTextStatement($insert);

       // end function
      }


      function ladeShoutboxEintragePerLimit($Start = '0',$Anzahl = '1'){

         $Eintraege = array();

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT Text,Datum,Uhrzeit FROM shoutbox ORDER BY Datum DESC, Uhrzeit DESC LIMIT ".$Start.",".$Anzahl;
         $result = $SQL->executeTextStatement($select);

         $EintraegePuffer = (string)'';

         while($data = $SQL->fetchData($result)){
            $Eintraege[] = $this->mappeDatenAlsDomainObjekt($data);
          // end while
         }

         return $Eintraege;

       // end function
      }


      function ladeAlleShoutboxEintrage(){

         $Eintraege = array();

         $SQL = &$this->__getServiceObject('core::database','MySQLHandler');
         $select = "SELECT Text,Datum,Uhrzeit FROM shoutbox ORDER BY Datum DESC, Uhrzeit DESC";
         $result = $SQL->executeTextStatement($select);

         $EintraegePuffer = (string)'';

         while($data = $SQL->fetchData($result)){
            $Eintraege[] = $this->mappeDatenAlsDomainObjekt($data);
          // end while
         }

         return $Eintraege;

       // end function
      }


      function mappeDatenAlsDomainObjekt($ShoutboxResultSet){

         $ShoutboxEintragObjekt = new ShoutboxEintragObjekt();

         if(isset($ShoutboxResultSet['Text'])){
            $ShoutboxEintragObjekt->setzeText($ShoutboxResultSet['Text']);
          // end if
         }
         if(isset($ShoutboxResultSet['Datum'])){
            $ShoutboxEintragObjekt->setzeDatum($ShoutboxResultSet['Datum']);
          // end if
         }
         if(isset($ShoutboxResultSet['Uhrzeit'])){
            $ShoutboxEintragObjekt->setzeUhrzeit($ShoutboxResultSet['Uhrzeit']);
          // end if
         }
         if(isset($ShoutboxResultSet['TIndex'])){
            $ShoutboxEintragObjekt->setzeSBIndex($ShoutboxResultSet['SBIndex']);
          // end if
         }

         return $ShoutboxEintragObjekt;

       // end function
      }

    // end class
   }
?>
