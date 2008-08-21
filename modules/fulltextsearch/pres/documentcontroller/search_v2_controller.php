<?php
   import('modules::fulltextsearch::biz','fulltextsearchManager');
   import('tools::variablen','variablenHandler');
   import('tools::datetime','dateTimeManager');
   import('sites::demosite::biz','DemositeModel');


   /**
   *  @package modules::fulltextsearch::pres
   *  @module search_v2_controller
   *
   *  Implementiert den DocumentController f�r das Template "search.html".<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 08.03.2008<br />
   */
   class search_v2_controller extends baseController
   {

      /**
      *  @private
      *  Liste der Hosts, auf dem die Applikation ausgef�hrt werden darf.
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
      *  Version 0.2, 09.03.2008 (Business-Schicht eingef�hrt)<br />
      *  Version 0.3, 16.03.2008 (Deaktivierung f�r Offline-Version eingef�hrt)<br />
      *  Version 0.4, 02.04.2008 (Ziel der Suchseite ge�ndert, damit nicht mehr unabh�ngiges Modul!)<br />
      *  Version 0.5, 13.06.2008 (Ziellink f�r englische Ergebnisse auf deutschen Seite ge�ndert)<br />
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
                  $Link = '/'.$PageParams[$SearchResults[$i]->get('Language')].'/'.ucfirst($SearchResults[$i]->get('Name')).'/~/sites_demosite_biz-action/setLanguage/lang/'.($SearchResults[$i]->get('Language'));
                // end if
               }
               else{
                  $Link = '/Seite/'.ucfirst($SearchResults[$i]->get('Name'));
                // end else
               }

               // Titel anzeigen
               $Template__Result->setPlaceHolder('Title','<a href="'.$Link.'" title="'.($SearchResults[$i]->get('Title')).'" style="font-size: 14px; font-weight: bold;">'.($SearchResults[$i]->get('Title')).'</a>');

               // Sprache
               $Language = $Config->getValue($this->__Language,'DisplayName.'.$SearchResults[$i]->get('Language'));
               $Template__Result->setPlaceHolder('Language',$Language);

               // Letzte �nderung
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


            // Ausgabe f�r keine Ergebnisse
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