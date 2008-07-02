<?php
   import('modules::suche::data','SucheMapper');


   /**
   *  @package modules::suche::biz
   *  @module SucheManager
   *
   *  Implementiert den Datenmanager für die Webseiten-Suche<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 29.03.2006<br />
   *  Version 0.2, 07.05.2006<br />
   */
   class SucheManager
   {

      function SucheManager(){
      }


      /**
      *  @module ladeSuchergebnisse()
      *  @public
      *
      *  Läd eine Liste von Suchergebnissen und ordnet diese nach Relevanz.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2006<br />
      *  Version 0.2, 07.05.2006 (Es werden nun nur noch 10 Ergebnisse an die Präsentations-Schicht übergeben)<br />
      *  Version 0.3, 03.03.2007 (Calltime-Pass-Reference-Problem aufgelöst)<br />
      */
      function ladeSuchergebnisse($Begriff){

         $M = new SucheMapper();
         $Liste = $M->ladeSuchErgebnisPerBegriff($Begriff);
         $Liste = $this->__ermittleRelevanz($Begriff,$Liste);
         usort($Liste,array('SucheManager','vergleicheTreffer'));
         return array_slice($Liste,0,10);

       // end function
      }


      /**
      *  @module ermittleRelevanz()
      *  @private
      *
      *  Ermittelt die Relevanz des Suchbegriffs gegen das Ergebnis.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2006<br />
      *  Version 0.2, 07.05.2006 (Relevanz-Ermittlung angepasst)<br />
      */
      function __ermittleRelevanz($SuchText,$Ergebnisse){

         for($i = 0; $i < count($Ergebnisse); $i++){

            // Titelrelevanz berechnen
            $TitelRelevanz = round((strlen($SuchText) / strlen($Ergebnisse[$i]->zeige('Name'))) * 100,0);


            // Textrelevanz berechnen
            if(strlen($Ergebnisse[$i]->zeige('Inhalt')) == strlen(str_replace($SuchText,'',$Ergebnisse[$i]->zeige('Inhalt')))){
               $TextRelevanz = 0;
             // end if
            }
            else{
               $TextRelevanz = ceil((strlen($SuchText) / strlen($Ergebnisse[$i]->zeige('Inhalt'))) * 100);
             // end else
            }


            // Relevanzen per gewichtetem arithmetischen Mittel kombinieren
            $Relevanz = ($TitelRelevanz  + $TextRelevanz) / (strlen($Ergebnisse[$i]->zeige('Name')) + strlen($Ergebnisse[$i]->zeige('Inhalt')));


            // Relevanz für aktuelles Objekt in % einsetzen
            $Ergebnisse[$i]->setze('Relevanz',ceil($TitelRelevanz));

          // end function
         }

         return $Ergebnisse;

       // end function
      }


      /**
      *  @module vergleicheTreffer()
      *  @public
      *  @static
      *
      *  Statische Vergleichs-Methode für den Einsatz unter usort()<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.05.2006<br />
      */
      function vergleicheTreffer($ObjektEins,$ObjektZwei){

         // Generiere numerische Werte aus den Relevanzen
         $TestEins = (int) $ObjektEins->zeige('Relevanz');
         $TestZwei = (int) $ObjektZwei->zeige('Relevanz');

         if($TestEins == $TestZwei){
            return 0;
          // end if
         }

         return ($TestEins > $TestZwei) ? -1 : +1;

       // end function
      }

    // end class
   }
?>