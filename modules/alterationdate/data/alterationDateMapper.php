<?php
   import('core::database','MySQLHandler');


   /**
   *  @package modules::alterationdate::data
   *  @module alterationDateMapper
   *
   *  Klasse alterationDateMapper implementiert die Datenschicht zur Änderungsdatumsanzeige<br />
   *
   *  @author Christian Schäfer
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
      *  Läd ein Datum an Hand eines Namens
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.06.2005
      *  Version 0.2, 13.11.2005
      *  Version 0.3, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function loadDate($Name){

         // Konfiguration in aktuellem Context holen
         $Config = $this->__getConfiguration('modules::alterationdate','alterationdate');

         // Konfiguration für die aktuelle Sektion auslesen
         $Spalte = $Config->getValue($Name,'Spalte');
         $Tabelle = $Config->getValue($Name,'Tabelle');

         // Prüfen, ob Config gültige Werte enthält
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