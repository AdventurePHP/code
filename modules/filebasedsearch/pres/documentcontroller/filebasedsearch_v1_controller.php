<?php
   import('tools::variablen','variablenHandler');
   import('modules::filebasedsearch::biz','fileBasedSearchManager');


   /**
   *  @package modules::filebasedsearch::pres
   *  @module filebasedsearch_v1_controller
   *
   *  Implementiert den DocumentController für das Template 'filebasedsearch.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 16.06.2007<br />
   */
   class filebasedsearch_v1_controller extends baseController
   {

      /**
      *  @private
      *  Speichert lokal verwendete Variablen.
      */
      var $_LOCALS;


      function filebasedsearch_v1_controller(){
         $this->_LOCALS = variablenHandler::registerLocal(array('SearchString' => '','Seite' => 'Startseite'));
       // end function
      }


      /**
      *  @module transformContent()
      *  @public
      *
      *  Implementiert die abstrakte Methode transformContent() aus coreObject.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 16.06.2007<br />
      *  Version 0.2, 28.12.2007 (Auf Mehrsprachigkeit umgestellt)<br />
      */
      function transformContent(){

         // Form holen
         $Form__SearchForm = &$this->__getForm('SearchForm_'.$this->__Language);

         // Titel einsetzen
         $Template__SearchText = &$this->__getTemplate('SearchText_'.$this->__Language);
         $this->setPlaceHolder('SearchText',$Template__SearchText->transformTemplate());

         // Text einsetzen
         $Template__SearchTitle = &$this->__getTemplate('SearchTitle_'.$this->__Language);
         $this->setPlaceHolder('SearchTitle',$Template__SearchTitle->transformTemplate());

         // Prüfen, ob Formular abgeschickt und korrekt ausgefüllt
         if($Form__SearchForm->get('isValid') && $Form__SearchForm->get('isSent')){

            // Manager holen
            $fSM = &$this->__getServiceObject('modules::filebasedsearch::biz','fileBasedSearchManager');


            // Ergebnisse laden
            $SearchResult = $fSM->getSearchResult($this->_LOCALS['SearchString']);


            // Ausgabe-Puffer initialisieren
            $Buffer = (string)'';


            // Header einsetzen
            $Template__SearchResultHeader = &$this->__getTemplate('SearchResultHeader_'.$this->__Language);
            $Buffer .= $Template__SearchResultHeader->transformTemplate();

            foreach($SearchResult as $Number => $ResultObject){
               $Buffer .= $this->__buildSearchResult($ResultObject);
             // end foreach
            }


            // Ausgabe für keine Ergebnisse
            if(count($SearchResult) < 1){

               // Template holen
               $Template__NoSearchResult = &$this->__getTemplate('NoSearchResult_'.$this->__Language);

               // Meldung in Platzhalter einsetzen
               $Buffer .= $Template__NoSearchResult->transformTemplate();

             // end if
            }


            // Liste in Platzhalter einsetzen
            $this->setPlaceHolder('SearchResult',$Buffer);

          // end if
         }


         // Formular ausgeben
         $this->setPlaceHolder('SearchForm',$Form__SearchForm->transformForm());

       // end function
      }


      /**
      *  @module __buildSearchResult()
      *  @private
      *
      *  Erzeugt die HTML-Ausgabe für ein Ergebnis.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 16.06.2007<br />
      *  Version 0.2, 24.06.2007 (Titel wird nun im Ergebnis-Objekt schon mitgeliefert)<br />
      */
      function __buildSearchResult($SearchResult){

         // Template holen
         $Template__SearchResult = &$this->__getTemplate('SearchResult_'.$this->__Language);

         // Werte einsetzen
         $Title = ucfirst($SearchResult->get('Title'));
         $Template__SearchResult->setPlaceHolder('Title',$Title);

         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');
         $Template__SearchResult->setPlaceHolder('Link',$URLBasePath.'/Seite/'.$Title);

         $Template__SearchResult->setPlaceHolder('Content',$SearchResult->get('Content'));
         $Template__SearchResult->setPlaceHolder('Size',$SearchResult->get('Size'));

         // Ergebnis zurückgeben
         return $Template__SearchResult->transformTemplate();

       // end function
      }

    // end class
   }
?>