<?php
   import('modules::filebasedsearch::data','fileBasedSearchMapper');
   import('modules::filebasedsearch::biz','searchResult');


   /**
   *  @package modules::filebasedsearch::biz
   *  @module fileBasedSearchManager
   *
   *  Business-Komponente für die dateibasierte Suche.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 16.06.2007<br />
   */
   class fileBasedSearchManager extends coreObject
   {

      function fileBasedSearchManager(){
      }


      /**
      *  @module getSearchResult()
      *  @public
      *
      *  Führt eine Suche nach einem Stichwort aus.<br />
      *
      *  @param string $SearchString; Such-String
      *  @return array $SearchResult; Liste von Such-Ergebnissen
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 16.06.2007<br />
      *  Version 0.2, 17.06.2007 (Erweiterung und größerer Umbau)<br />
      */
      function getSearchResult($SearchString){

         // Timer holen
         $T = &Singleton::getInstance('benchmarkTimer');


         // Timer starten
         $T->start('fileBasedSearchManager::getSearchResult()');


         // SpecialChars im Suchwort decoden
         $SearchString = html_entity_decode($SearchString);


         // Suchworte anhand eines Lerzeichens trennen
         $SearchStrings = split(' ',$SearchString);


         // Manager holen
         $fSM = &$this->__getServiceObject('modules::filebasedsearch::data','fileBasedSearchMapper');


         // Ergebnis inistialisieren
         $SearchResult = array();


         // Ergebnisse laden
         for($i = 0; $i < count($SearchStrings); $i++){
            $SearchResult = array_merge($SearchResult,$fSM->getSearchResult($SearchStrings[$i]));
          // end for
         }


         // Timer stoppen
         $T->stop('fileBasedSearchManager::getSearchResult()');


         // Ergebnisse zurückgeben
         return $SearchResult;

       // end function
      }

    // end class
   }
?>