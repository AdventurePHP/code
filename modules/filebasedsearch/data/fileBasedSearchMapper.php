<?php
   import('modules::filebasedsearch::biz','searchResult');
   import('core::filesystem','filesystemHandler');


   /**
   *  @package modules::filebasedsearch::data
   *  @module fileBasedSearchMapper
   *
   *  Daten-Komponente f�r die dateibasierte Suche. Hat Methoden f�r den Aufbau<br />
   *  eines Such-Indexes, dessen Laden und der Suche nach �bergebenen Begriffen.<br />
   *  Die Komponente sollte aus Performance-Gr�nden Singleton instanziiert werden. Es ist<br />
   *  deshalb ratsam diese per <em>$this->__getServiceObject()</em> zu erzeugen.<br />
   *  <br />
   *  Die Bibliothek kann per set() mit den Parametern<br />
   *  <ul>
   *    <li>SearchFolder: Ordner, in den gesucht werden soll</li>
   *    <li>FilePrefix: Datei-Pr�fix, das bei der Erzeugung der Links ersetzt werden soll</li>
   *    <li>FileExtension: Datei-Endung, die bei der Erzeugung der Links ersetzt werden soll</li>
   *    <li>SearchIndexFile: Pfad und Datei-Name des Such-Indexes</li>
   *    <li>LettersBefore: Anzahl der Buchstaben, die vor dem Suchwort im Abstract stehen sollen</li>
   *    <li>LettersAfter: Anzahl der Buchstaben, die nach dem Suchwort im Abstract stehen sollen</li>
   *  </ul>
   *  konfiguriert werden.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 16.06.2007<br />
   *  Version 0.2, 20.06.2007 (Refactoring der Methoden)<br />
   *  Version 0.3, 29.09.2007 (Blacklist wegen Mehrsprachigkeit angepasst)<br />
   */
   class fileBasedSearchMapper extends coreObject
   {

      /**
      *  @private
      *  Such-Ordner.
      */
      var $__SearchFolder = './frontend/content';


      /**
      *  @private
      *  Datei-Prefix.
      */
      var $__FilePrefix = 'c_';


      /**
      *  @private
      *  Datei-Erweiterung.
      */
      var $__FileExtension = 'html';


      /**
      *  @private
      *  Pfad und Dateiname des Index-Files.
      */
      var $__SearchIndexFile = './frontend/media/search.index';


      /**
      *  @private
      *  Such-Index.
      */
      var $__SearchIndex = array();


      /**
      *  @private
      *  Blacklist, der Dateien, die nicht indiziert werden d�rfen.
      */
      var $__IndexBlackList = array(
                                    'c_de_suche.html',
                                    'c_en_suche.html'
                                   );


      /**
      *  @private
      *  Zeichen, die vor der Fundstelle ausgegeben werden sollen.
      */
      var $__LettersBefore = 100;


      /**
      *  @private
      *  Zeichen, die vor der Fundstelle ausgegeben werden sollen.
      */
      var $__LettersAfter = 600;


      function fileBasedSearchMapper(){
      }


      /**
      *  @module __createSearchIndex()
      *  @private
      *
      *  Erzeugt den Such-Index, speichert diesen auf Platte und h�lt diesen im Mapper vor.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 20.06.2007<br />
      *  Version 0.2, 21.10.2007(Umgestellt auf neue TagLib doc:createobject)<br />
      */
      function __createSearchIndex(){

         // Timer holen
         $T = &Singleton::getInstance('benchmarkTimer');


         // Timer starten
         $T->start('fileBasedSearchMapper::__createSearchIndex()');


         // Verzeichnis auslesen
         $fH = new filesystemHandler($this->__SearchFolder);
         $Files = $fH->showDirContent();


         // Such-Index initialisieren
         $this->__SearchIndex = array();

         for($i = 0; $i < count($Files); $i++){

            if(!in_array(strtolower($Files[$i]),$this->__IndexBlackList) && !is_dir($this->__SearchFolder.'/'.$Files[$i])){

               // Dateigr��e ermitteln
               $FileSize = filesize($this->__SearchFolder.'/'.$Files[$i]);
               clearstatcache();

               // Dateiname zusammensetzen
               $FileName = str_replace($this->__FilePrefix,'',str_replace('.'.$this->__FileExtension,'',$Files[$i]));

               // Sprache erzeugen
               $TokenPos = strpos($FileName,'_');
               $Language = substr($FileName,0,$TokenPos);

               // Inhalte der Seite ziehen
               $PageName = substr($FileName,$TokenPos + 1);
               $Content = $this->__transformCurrentPage($Language,$PageName);

               // Attribute aufbereiten
               $File = $this->__SearchFolder.'/'.$Files[$i];
               $Title = $Page = ucfirst($PageName);

               // Neues Ergebnis-Objekt erzeugen
               $this->__SearchIndex[] = new searchResult($File,$Title,$Content,$FileSize);

               // Variablen unset'en
               unset($FileSize,$URL,$Content,$File,$Title);

             // end if
            }

          // end for
         }


         // Index auf Platte schreiben
         $fH = fopen($this->__SearchIndexFile,'w+');
         fwrite($fH,serialize($this->__SearchIndex));
         fclose($fH);


         // Timer stoppen
         $T->stop('fileBasedSearchMapper::__createSearchIndex()');

       // end function
      }


      /**
      *  @module __transformCurrentPage()
      *  @private
      *
      *  Methode um die Ausgabe der Seiteninhalte je Parameter f�r den Suchindex zu erstellen.<br />
      *
      *  @param string $Language Sprache der aktuellen Datei
      *  @param string $FileName Aktueller Dateiname f�r das Content-Template
      *  @return string $CurrentPageContent (HTML-)Inhalt der aktuellen Seite
      *
      *  @author Christian W. Sch�fer
      *  @version
      *  Version 0.1, 31.08.2007<br />
      *  Version 0.2, 21.10.2007(Umgestellt auf neue TagLib doc:createobject)<br />
      */
      function __transformCurrentPage($Language,$FileName){

         // Timer starten
         $T = &Singleton::getInstance('BenchmarkTimer');
         $T->start('fileBasedSearchMapper::__transformCurrentPage('.$FileName.')');

         // Neues Page-Objekt erstellen
         $CurrentPage = new Page('SearchIndex',false);

         // Seiten-Namen setzen
         $_REQUEST['CurrentPage'] = $FileName;

         // Context und Sprache setzen
         $CurrentPage->set('Context',$this->__Context);
         $CurrentPage->set('Language',$Language);

         // Indexer-Template laden
         $CurrentPage->loadDesign('modules::filebasedsearch::pres::templates','createindex');

         // Ausgabe erzeugen
         $Content = $CurrentPage->transform();

         // Timer stoppen
         $T->stop('fileBasedSearchMapper::__transformCurrentPage('.$FileName.')');

         // Ausgabe zur�ckgeben
         return $Content;

       // end function
      }


      /**
      *  @module __loadSearchIndex()
      *  @private
      *
      *  L�d den Suchindex in den Speicher oder erstellt diesen.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 20.06.2007<br />
      */
      function __loadSearchIndex(){

         // Timer holen
         $T = &Singleton::getInstance('benchmarkTimer');


         // Timer starten
         $T->start('fileBasedSearchMapper::__loadSearchIndex()');


         // Such-Index laden, falls noch nicht passiert
         if(count($this->__SearchIndex) == 0){

            // Pr�fen, ob Index-Datei existiert und falls nicht, erstellen
            if(file_exists($this->__SearchIndexFile)){
               $this->__SearchIndex = unserialize(trim(file_get_contents($this->__SearchIndexFile)));
             // end if
            }
            else{

               // Such-Index erstellen
               $this->__createSearchIndex();

             // end else
            }

          // end if
         }


         // Timer stoppen
         $T->stop('fileBasedSearchMapper::__loadSearchIndex()');

       // end function
      }


      /**
      *  @module getSearchResult()
      *  @public
      *
      *  F�hrt eine Suche nach einem Stichwort aus.<br />
      *
      *  @param string $SearchString; Such-String
      *  @return array $SearchResult; Liste von Such-Ergebnissen
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 16.06.2007<br />
      */
      function getSearchResult($SearchString){

         // Timer holen
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('fileBasedSearchMapper::getSearchResult()');


         // R�ckgabe-Array initialisieren
         $Result = array();


         // Zeichen des Suchworts auf Kleinschrift umstellen
         $SearchStringSmall = strtolower($SearchString);


         // Index laden
         $this->__loadSearchIndex();


         // Index durchsuchen
         for($i = 0; $i < count($this->__SearchIndex); $i++){

            // String finden
            $Content = strip_tags($this->__SearchIndex[$i]->get('Content'));
            $ContentSmall = strtolower($Content);


            // Vorkommen des Strings finden
            $SearchStringPos = strpos($ContentSmall,$SearchStringSmall);


            // Pr�fen, ob Suchwort vorkommt
            if($SearchStringPos === false){
             // end if
            }
            else{

               // Abstract generieren
               $Abstract = substr($Content,$SearchStringPos - $this->__LettersBefore,$this->__LettersBefore + $this->__LettersAfter + strlen($SearchString));


               // Such-String aus Text extrahieren
               $RealSearchString = substr($Content,$SearchStringPos,strlen($SearchString));


               // altuelles Objekt zur Ergebnis-Liste hinzuf�gen
               $Result[] = new searchResult(

                                            // Datei-Name
                                            $this->__SearchIndex[$i]->get('File'),

                                            // Titel
                                            $this->__SearchIndex[$i]->get('Title'),

                                            // Suchwort mit Highlight
                                            preg_replace('~'.$this->__escapePregSpecialCharacters(trim($SearchStringSmall)).'~i','<font style="background-color: yellow;">'.$RealSearchString.'</font>',$Abstract),

                                            // Dateigr��e
                                            round($this->__SearchIndex[$i]->get('Size') / 1000,2).' kB'

                                           );

             // end else
            }


            // Variablen unsetten
            unset($Content,$ContentSmall,$SearchStringPos,$Abstract,$RealSearchString);

          // end for
         }


         // Timer stoppen
         $T->stop('fileBasedSearchMapper::getSearchResult()');


         // Ergebnis-Menge zur�ckgeben
         return $Result;

       // end function
      }


      /**
      *  @module __escapePregSpecialCharacters()
      *  @private
      *
      *  Escaped Zeichen, die in einem regul�ren Ausdruck "magic" sind.<br />
      *
      *  @param string $String; String
      *  @return string $String; String
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 17.06.2007<br />
      */
      function __escapePregSpecialCharacters($String){

         $PregSpecialChars = array(
                                   '*' => '\*',
                                   '.' => '\.'
                                  );
         return strtr($String,$PregSpecialChars);

       // end function
      }

    // end class
   }
?>