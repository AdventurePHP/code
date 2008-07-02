<?php
   import('modules::dokbib::biz','SuchErgebnis');
   import('modules::dokbib::data','DokBibMapper');
   import('tools::datetime','dateTimeManager');


   /**
   *  @package modules::dokbib::biz
   *  @module DokBibManager
   *
   *  Implementiert den Manager der DokumentenBibliothek.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 06.07.2005<br />
   *  Version 0.2, 07.07.2005<br />
   *  Version 0.3, 22.07.2005<br />
   *  Version 0.4, 04.12.2005<br />
   */
   class DokBibManager extends coreObject
   {

      /**
      *  @private
      *  Hält den DokumentenPfad.
      */
      var $__DokumentenPfad;


      function DokBibManager(){
      }


      /**
      *  @module ladeSuchErgebnisse()
      *  @public
      *
      *  Läd die Suchergebnisse aus der Datenschicht.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.07.2005<br />
      *  Version 0.2, 07.07.2005<br />
      *  Version 0.3, 18.03.2007 (Implementierung nach PC V2)<br />
      */
      function ladeSuchErgebnisse($SuchText){

         // Mapper holen
         $M = &$this->__getServiceObject('modules::dokbib::data','DokBibMapper');


         // Configuration holen
         $Config = &$this->__getConfiguration('modules::baumstruktur','baumanzeige');
         $this->__DokumentenPfad = $Config->getValue('Standard','DokumentenPfad');


         // Ergebnisse laden
         $Ergebnisse = $M->ladeSuchErgebnisse($SuchText);


         // Relevanzen ermitteln
         $Ergebnisse = $this->__ermittleRelevanz($SuchText,$Ergebnisse);


         // Datum umwandeln
         $Ergebnisse = $this->__wandleDatumUm($Ergebnisse);


         // Dokumentenpfad hinzufügen
         $Ergebnisse = $this->__setzeDokumentenPfad($Ergebnisse);


         // Daten nach Datum und Zeit absteigend sortieren
         usort($Ergebnisse,array('DokBibManager','vergleicheErgebnisse'));


         // Ergebnisse zurückgeben
         return $Ergebnisse;

       // end function
      }


      /**
      *  @module __ermittleRelevanz()
      *  @private
      *
      *  Ermittelt die Relevanz des Suchbegriffs gegen das Ergebnis.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.07.2005<br />
      *  Version 0.2, 07.07.2005<br />
      */
      function __ermittleRelevanz($SuchText,$Ergebnisse){

         for($i = 0; $i < count($Ergebnisse); $i++){
            $Relevanz = round((strlen($SuchText) / strlen($Ergebnisse[$i]->zeigeName())) * 100,0);
            $Ergebnisse[$i]->setzeRelevanz($Relevanz);
          // end function
         }

         return $Ergebnisse;

       // end function
      }


      /**
      *  @module __wandleDatumUm()
      *  @private
      *
      *  Wandelt das SQL-Format in normales Format (xx.xx.xxxx) um.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.07.2005<br />
      *  Version 0.2, 07.07.2005<br />
      */
      function __wandleDatumUm($Ergebnisse){

         for($i = 0; $i < count($Ergebnisse); $i++){
            $Ergebnisse[$i]->setzeDatum(dateTimeManager::convertDate2Normal($Ergebnisse[$i]->zeigeDatum()));
          // end function
         }

         return $Ergebnisse;

       // end function
      }


      /**
      *  @module __setzeDokumentenPfad()
      *  @private
      *
      *  Setzt den Dokumentenpfad für jedes Ergebnis.<br />
      *  Hier wird ein Trick angewendet, dass der im Domain-Objekt enthaltene String,<br />
      *  der gleichnamig einer Konstanten ist auch als String beibehalten wird und nicht<br />
      *  durch den Wert der Konstanten ersetzt wird.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 16.07.2005<br />
      */
      function __setzeDokumentenPfad($Ergebnisse){

         for($i = 0; $i < count($Ergebnisse); $i++){
            $Ergebnisse[$i]->setzeDokumentenPfad(str_replace('%','',$this->__DokumentenPfad));
          // end function
         }

         return $Ergebnisse;

       // end function
      }


      /**
      *  @module vergleicheErgebnisse()
      *  @public
      *  @static
      *
      *  Implementiert die Vergleichsfunktion, die für den Aufruf von<br />
      *  usort() benötigt wird. Verglichen werden in Integer umgewandelte<br />
      *  Relevanz-Zahlen.<br />
      *  <br />
      *  Parameter:<br />
      *    - $ObjectOne: Erstes Objekt (object)<br />
      *    - $ObjectTwo: Zweites Objekt (object)<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.07.2005<br />
      */
      static function vergleicheErgebnisse($ObjektEins,$ObjektZwei){

         // Generiere numerische Werte aus den Relevanzen
         $TestEins = (int) $ObjektEins->zeigeRelevanz();
         $TestZwei = (int) $ObjektZwei->zeigeRelevanz();

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