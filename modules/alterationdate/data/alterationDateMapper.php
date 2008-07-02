<?php
   import('core::database','MySQLHandler');


   /**
   *  @package modules::alterationdate::data
   *  @module alterationDateMapper
   *
   *  Klasse alterationDateMapper implementiert die Datenschicht zur �nderungsdatumsanzeige<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 22.06.2005<br />
   *  Version 0.2, 04.12.2004<br />
   *  Version 0.3, 15.11.2006 (Update des Codes)<br />
   *  Version 0.4, 17.03.2007 (Implementierung nach PC V2)<br />
   */
   class alterationDateMapper extends coreObject
   {

      function alterationDateMapper(){
      }


      /**
      *  @module loadDate()
      *  @public
      *
      *  L�d ein Datum an Hand eines Namens
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 22.06.2005
      *  Version 0.2, 13.11.2005
      *  Version 0.3, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function loadDate($Name){

         // Konfiguration in aktuellem Context holen
         $Config = $this->__getConfiguration('modules::alterationdate','alterationdate');

         // Konfiguration f�r die aktuelle Sektion auslesen
         $Spalte = $Config->getValue($Name,'Spalte');
         $Tabelle = $Config->getValue($Name,'Tabelle');

         // Pr�fen, ob Config g�ltige Werte enth�lt
         if($Spalte != null && $Tabelle != null){

            // Daten aus der DB beziehen
            $SQL = &$this->__getServiceObject('core::database','MySQLHandler');

            $select = 'SELECT '.$Spalte.' AS Date
                       FROM '.$Tabelle.'
                       ORDER BY '.$Spalte.' DESC';
            $result = $SQL->executeTextStatement($select);
            $data = $SQL->fetchData($result);

            return $data['Date'];

          // end if
         }
         else{
            return (string)'';
          // end else
         }

       // end function
      }

    // end class
   }
?>