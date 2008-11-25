<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::filebasedsearch::data','fileBasedSearchMapper');
   import('modules::filebasedsearch::biz','searchResult');


   /**
   *  @namespace modules::filebasedsearch::biz
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