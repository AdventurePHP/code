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

   import('modules::fulltextsearch::biz','fulltextsearchManager');
   import('tools::variablen','variablenHandler');
   import('tools::datetime','dateTimeManager');
   import('sites::demosite::biz','DemositeModel');


   /**
   *  @namespace modules::fulltextsearch::pres
   *  @module search_v2_controller
   *
   *  Implementiert den DocumentController für das Template "search.html".<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 08.03.2008<br />
   */
   class search_v2_controller extends baseController
   {

      /**
      *  @private
      *  Liste der Hosts, auf dem die Applikation ausgeführt werden darf.
      */
      var $__AllowedServers = array(
                                    'dev.adventure-php-framework.org',
                                    'stage.adventure-php-framework.org',
                                    'www.adventure-php-framework.org',
                                    'adventure-php-framework.org'
                                   );


      /**
      *  @private
      *  Name der Suche-Seite.
      */
      var $__SearchPageName = array(
                                    'de' => '044-Suche',
                                    'en' => '044-Search'
                                   );


      function search_v2_controller(){
      }


      /**
      *  @module transformContent()
      *  @public
      *
      *  Implementiert die abstrakte Methode des baseControllers.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.03.2008<br />
      *  Version 0.2, 09.03.2008 (Business-Schicht eingeführt)<br />
      *  Version 0.3, 16.03.2008 (Deaktivierung für Offline-Version eingeführt)<br />
      *  Version 0.4, 02.04.2008 (Ziel der Suchseite geändert, damit nicht mehr unabhängiges Modul!)<br />
      *  Version 0.5, 13.06.2008 (Ziellink für englische Ergebnisse auf deutschen Seite geändert)<br />
      */
      function transformContent(){

         // Suchwort entgegen nehmen
         $_LOCALS = variablenHandler::registerLocal(array('SearchString' => ''));


         // Model holen
         $Model = &$this->__getServiceObject('sites::demosite::biz','DemositeModel');
         $PageParams = $Model->getAttribute('ReqParamName');
         $RequestParameter = $PageParams[$this->__Language];


         // Formular anzeigen
         $Form__SearchV2 = &$this->__getForm('SearchV2');
         $Form__SearchV2->setAttribute('action','/'.$RequestParameter.'/'.$this->__SearchPageName[$this->__Language]);
         $this->setPlaceHolder('SearchV2',$Form__SearchV2->transformForm());


         // Ergebnisse anzeigen
         if(strlen($_LOCALS['SearchString']) >= 3){

            // Manager holen
            $M = &$this->__getServiceObject('modules::fulltextsearch::biz','fulltextsearchManager');

            // Ergebnisse laden
            $SearchResults = $M->loadSearchResult($_LOCALS['SearchString']);

            // Sprachmapping-Config laden
            $Config = &$this->__getConfiguration('modules::fulltextsearch','language');

            // Puffer initialisieren
            $Buffer = (string)'';

            // Referenz auf Template holen
            $Template__Result = &$this->__getTemplate('Result');

            // Ausgabe erzeugen
            $Reg = &Singleton::getInstance('Registry');
            $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');

            $count = count($SearchResults);
            for($i = 0; $i < $count; $i++){

               // Link erzeugen
               if($SearchResults[$i]->get('Language') != $this->__Language){
                  $Link = '/'.$PageParams[$SearchResults[$i]->get('Language')].'/'.$SearchResults[$i]->get('PageID').'-'.$SearchResults[$i]->get('URLName').'/~/sites_demosite_biz-action/setLanguage/lang/'.($SearchResults[$i]->get('Language'));
                // end if
               }
               else{
                  $Link = '/'.$PageParams[$SearchResults[$i]->get('Language')].'/'.$SearchResults[$i]->get('PageID').'-'.$SearchResults[$i]->get('URLName');
                // end else
               }

               // Titel anzeigen
               $Template__Result->setPlaceHolder('Title','<a href="'.$Link.'" title="'.($SearchResults[$i]->get('Title')).'" style="font-size: 14px; font-weight: bold;">'.($SearchResults[$i]->get('Title')).'</a>');

               // Sprache
               $Language = $Config->getValue($this->__Language,'DisplayName.'.$SearchResults[$i]->get('Language'));
               $Template__Result->setPlaceHolder('Language',$Language);

               // Letzte Änderung
               $Date = dateTimeManager::convertDate2Normal(substr($SearchResults[$i]->get('LastMod'),0,10));
               $Time = substr($SearchResults[$i]->get('LastMod'),11);
               $Template__Result->setPlaceHolder('LastMod',$Date.', '.$Time);

               // Direktlink anzeigen
               $Template__Result->setPlaceHolder('PermaLink',$URLBasePath.$Link);

               // Wortanzahl
               $Template__Result->setPlaceHolder('WordCount',$SearchResults[$i]->get('WordCount'));

               // Wort
               $Template__Result->setPlaceHolder('IndexWord',$SearchResults[$i]->get('IndexWord'));

               // Leerzeile einbauen
               $Buffer .= $Template__Result->transformTemplate();

             // end for
            }


            // Ausgabe für keine Ergebnisse
            if($count < 1){

               // Template holen
               $Template__NoSearchResult = &$this->__getTemplate('NoSearchResult_'.$this->__Language);

               // Meldung in Platzhalter einsetzen
               $Buffer .= $Template__NoSearchResult->transformTemplate();

             // end if
            }


            // Ergebnis einsetzen
            $this->setPlaceHolder('Result',$Buffer);

          // end if
         }

       // end function
      }

    // end class
   }
?>