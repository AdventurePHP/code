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

   import('core::logging','Logger');
   import('core::database','connectionManager');
   import('core::filesystem','filesystemHandler');


   /**
   *  @namespace modules::fulltextsearch::data
   *  @module fulltextsearchIndexer
   *
   *  Implementiert den Indexer für die Volltextsuche.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 06.03.2008<br />
   *  Version 0.2, 07.06.2008 (Timer wegen Performance auskommentiert)<br />
   */
   class fulltextsearchIndexer extends coreObject
   {

      /**
      *  @private
      *  Name der Log-Datei
      */
      var $__LogFileName = 'fulltextsearchindexer';


      /**
      *  @private
      *  Pfad zu den Inhaltsdateien.
      */
      var $__ContentFolder = './frontend/content';


      function fulltextsearchIndexer(){
      }


      /**
      *  @module importArticles()
      *  @public
      *
      *  Importiert Artikel von einem Verzeichnis in die Datenbank.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 16.03.2008<br />
      */
      function importArticles(){

         // Timer holen
         $T = &Singleton::getInstance('benchmarkTimer');

         // Logger erzeugen
         $L = &Singleton::getInstance('Logger');

         // Konfiguration holen
         $Config = &$this->__getConfiguration('modules::fulltextsearch','fulltextsearch');

         // Connection holen
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $SQL = &$cM->getConnection($Config->getValue('Database','ConnectionKey'));

         // Bisherige Artikel löschen
         $L->logEntry($this->__LogFileName,'[DELETE] Delete articles ...');
         $delete = 'TRUNCATE search_articles';
         $SQL->executeTextStatement($delete);

         // Dateien auslesen
         $fH = new filesystemHandler($this->__ContentFolder);
         $Files = $fH->showDirContent();

         // Dateien importieren
         foreach($Files as $File){

            // Auf Datei prüfen
            if(!is_dir($this->__ContentFolder.'/'.$File)){

               // Log-Eintrag schreiben
               $L->logEntry($this->__LogFileName,'[START] Create article from "'.$File.'" ...');

               // Status-Cache löschen
               clearstatcache();

               // Attribute aus Datei lesen
               $Lang = substr($File,2,2);
               $Name = substr($File,5,(strlen($File) - 10));
               $ModStamp = date('Y-m-d H:i:s',filemtime($this->__ContentFolder.'/'.$File));
               $Content = file_get_contents($this->__ContentFolder.'/'.$File);
               preg_match('/<font style="font-size: 26px; font weight: bold;">([A-Za-z0-9-\(\)&;:.<\/>!\s]+)<\/font>/i',$Content,$Matches);
               unset($Content);

               if(isset($Matches[1])){
                  $Title = $Matches[1];
                // end if
               }
               else{
                  $Title = '---';
                  $L->logEntry($this->__LogFileName,'- File "'.$File.'" contains no title ...');
                // end else
               }

               // In Artikel-Datenbank einfügen
               $insert = 'INSERT INTO search_articles
                          (Title,Language,Name,ModificationTimestamp)
                          VALUES
                          (\''.$Title.'\',\''.$Lang.'\',\''.$Name.'\',\''.$ModStamp.'\')';
               $SQL->executeTextStatement($insert);

               // Log-Eintrag schreiben
               $L->logEntry($this->__LogFileName,'[FINISH] Create article from "'.$File.'" ...');
               $L->flushLogBuffer();

             // end if
            }

          // end if
         }

       // end function
      }


      /**
      *  @module createIndex()
      *  @public
      *
      *  Indiziert Artikel, die in der Datenbank gelistet sind. Falls keine Argumente übergeben<br />
      *  wird der Index komplett neu erstellt.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.03.2008<br />
      */
      function createIndex(){

         // Timer holen
         //$T = &Singleton::getInstance('benchmarkTimer');

         // Logger erzeugen
         $L = &Singleton::getInstance('Logger');

         // Konfiguration holen
         $Config = &$this->__getConfiguration('modules::fulltextsearch','fulltextsearch');

         // Connection holen
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $SQL = &$cM->getConnection($Config->getValue('Database','ConnectionKey'));

         // Bisherigen Index löschen
         $delete = 'TRUNCATE search_index';
         $SQL->executeTextStatement($delete);

         // Artikel selektieren
         $select_articles = 'SELECT * FROM search_articles';
         $result_articles = $SQL->executeTextStatement($select_articles);

         while($data_articles = $SQL->fetchData($result_articles)){

            // ArticleID festlegen
            $ArticleID = $data_articles['ArticleID'];

            // Timer starten
            //$T->start('Article: '.$ArticleID);

            // Log-Eintrag erzeugen
            $L->logEntry($this->__LogFileName,'[START] Indexing article "'.$data_articles['Name'].'" (ID: '.$ArticleID.') ...');

            // Quelltext des Artikels generieren
            $Content = $this->__createPageOutput($data_articles['Name'],$data_articles['Language']);

            // Inhalt normalisiere
            $Content = $this->__normalizeContent($Content,$data_articles['Language']);

            // Noch vorhandene Wörter in ein Array verpacken, so dass es duchlaufen werden kann
            // Trennung an Hand von Leer-, Satz- oder Sonderzeichen
            ////$T->start('Wörter trennen');
            $ContentArray = preg_split('[\s|-|,|;|:|/|!|\?|\.|\n|\r|\t]',$Content);
            //$T->stop('Wörter trennen');

            // Speicher freigeben
            unset($Content);

            // Anzahl der indizierten Wörter loggen
            $L->logEntry($this->__LogFileName,'- Words in text: '.count($ContentArray));

            // Bisherige Indizierung löschen
            //$T->start('Bisherige Indizierung löschen');
            $delete_index = 'DELETE FROM search_index WHERE ArticleID = \''.$ArticleID.'\'';
            $SQL->executeTextStatement($delete_index);
            //$T->stop('Bisherige Indizierung löschen');

            // Indizierung durchführen
            //$T->start('Indizierung durchführen');
            $Index = array();

            foreach($ContentArray as $Word){

               // Wort trimmen
               $Word = trim($Word);

               // Nur nichtleere Wörter indizieren
               if(!empty($Word)){

                  // Schlüssel des Wortes holen (evtl. implizit speichern)
                  $WordID = $this->__getWordID($Word);

                  // Indes aufbauen
                  if(isset($Index[$WordID])){
                      $Index[$WordID]['WordCount'] = $Index[$WordID]['WordCount'] + 1;
                   // end if
                  }
                  else{
                     $Index[$WordID]['WordID'] = $WordID;
                     $Index[$WordID]['WordCount'] = 1;
                   // end else
                  }

                // end if
               }

             // end else
            }
            //$T->stop('Indizierung durchführen');


            // Speicher freigeben
            unset($ContentArray);


            // Index sortieren
            //$T->start('Index sortieren');
            sort($Index);
            //$T->stop('Index sortieren');

            // Anzahl der indizierten Wörter loggen
            $L->logEntry($this->__LogFileName,'- Indexed words: '.count($Index));

            // Ergebnis persistieren
            //$T->start('Index speichern');
            foreach($Index as $WordID => $IndexValues){

               // Index-Eintrag speichern
               $insert_index = 'INSERT INTO search_index
                                (WordID,ArticleID,WordCount)
                                VALUES
                                (\''.$IndexValues['WordID'].'\',\''.$ArticleID.'\',\''.$IndexValues['WordCount'].'\')';
               $SQL->executeTextStatement($insert_index);

             // end foreach
            }
            //$T->stop('Index speichern');


            // Speicher freigeben
            unset($Index);

            // Log-Eintrag erzeugen
            $L->logEntry($this->__LogFileName,'[FINISH] Indexing article "'.$data_articles['Name'].'" (ID: '.$ArticleID.') ...');
            $L->logEntry($this->__LogFileName,'');
            $L->flushLogBuffer();

            // Timer stoppen
            //$T->stop('Article: '.$ArticleID);

          // end while
         }

       // end function
      }


      /**
      *  @module __createPageOutput()
      *  @private
      *
      *  Liefert die ID eines Suchwortes zurück. Falls das Wort noch nicht<br />
      *  in der Datenbank gespeichert ist, wird dieses gespeichert.<br />
      *
      *  @param string $Word; Wort für den Suchindex
      *  @return int $WordID; ID des Suchwortes
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 06.03.2008<br />
      */
      function __getWordID($Word){

         // Timer starten
         //$T = &Singleton::getInstance('benchmarkTimer');
         //$T->start('fulltextsearchIndexer->__getWordID('.$Word.')');

         // Konfiguration holen
         $Config = &$this->__getConfiguration('modules::fulltextsearch','fulltextsearch');

         // Connection holen
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $SQL = &$cM->getConnection($Config->getValue('Database','ConnectionKey'));

         // Wort selektieren
         $select_word = 'SELECT WordID FROM search_word WHERE Word = \''.$Word.'\'';
         $result_word = $SQL->executeTextStatement($select_word);
         $data_word = $SQL->fetchData($result_word);

         // ID auslesen
         if(!isset($data_word['WordID'])){
            $insert_word = 'INSERT INTO search_word (Word) VALUES (\''.$Word.'\')';
            $result_word = $SQL->executeTextStatement($insert_word);
            $ID = $SQL->getLastID();
          // end if
         }
         else{
            $ID = $data_word['WordID'];
          // end else
         }

         // Timer stoppen
         //$T->stop('fulltextsearchIndexer->__getWordID('.$Word.')');

         // ID zurückgeben
         return $ID;

       // end function
      }


      /**
      *  @module __createPageOutput()
      *  @private
      *
      *  Erzeugt den HTML-Code einer Seite.<br />
      *
      *  @param string $PageName; Name der Seite (Template-Name)
      *  @param string $Language; Sprache der Seite
      *  @return string $PageOutput; HTML-Code einer Content-Seite
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.03.2008<br />
      */
      function __createPageOutput($PageName,$Language){

         // Timer starten
         //$T = &Singleton::getInstance('benchmarkTimer');
         //$T->start('fulltextsearchIndexer->__createPageOutput()');

         // Seite instanziieren
         //$T->start('new Page()');
         $CurrentPage = new Page('SearchIndex',false);
         //$T->stop('new Page()');

         // Seiten-Namen setzen
         $_REQUEST['CurrentPage'] = $PageName;

         // Context und Sprache setzen
         $CurrentPage->set('Context',$this->__Context);
         $CurrentPage->set('Language',$Language);

         // Indexer-Template laden
         //$T->start('$Page->loadDesign()');
         $CurrentPage->loadDesign('modules::fulltextsearch::pres::templates::indexer','createindex');
         //$T->stop('$Page->loadDesign()');

         // Ausgabe erzeugen
         //$T->start('$Page->transform()');
         $Content = $CurrentPage->transform();
         //$T->stop('$Page->transform()');

         // Timer stoppen
         //$T->stop('fulltextsearchIndexer->__createPageOutput()');

         // Ausgabe zurückgeben
         return $Content;

       // end function
      }


      /**
      *  @module __normalizeContent()
      *  @private
      *
      *  Normalisiert den Inhalt und entfernt Stopwörter.<br />
      *
      *  @param string $Content; Inhalt einer Seite (HTML-Code)
      *  @param string $Language; Sprache der Seite
      *  @return string $NormalizedContent; Normalisierter Inhalt der Seite
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.03.2008<br />
      */
      function __normalizeContent($Content,$Language){

         // Timer starten
         //$T = &Singleton::getInstance('benchmarkTimer');
         //$T->start('fulltextsearchIndexer->__normalizeContent()');

         // Sonderzeichen ersetzen und normalisieren
         //$T->start('Sonderzeichen ersetzen');
         $locSearch[] = '/ß/i';
         $locSearch[] = '/ä/i';
         $locSearch[] = '/ö/i';
         $locSearch[] = '/ü/i';
         $locSearch[] = '/\|/i';
         $locSearch[] = '/«|»|<|>|\{|\}|\[|\]|\(|\)/i';
         $locSearch[] = '/\'|\"/';
         $locSearch[] = '/=/';

         $locReplace[] = 'ss';
         $locReplace[] = 'ae';
         $locReplace[] = 'oe';
         $locReplace[] = 'ue';
         $locReplace[] = '';
         $locReplace[] = '';
         $locReplace[] = '';
         $locReplace[] = '';

         $Content = strip_tags($Content);
         $Content = stripslashes($Content);
         $Content = html_entity_decode($Content);
         $Content = strtolower($Content);
         $Content = trim($Content);
         $Content = preg_replace($locSearch,$locReplace,$Content);
         //$T->stop('Sonderzeichen ersetzen');

         // Stopwords löschen und gegen Leerzeichen ersetzen
         //$T->start('Stopwords ersetzen');
         //$T->start('import()');
         include(APPS__PATH.'/modules/fulltextsearch/data/indexer/Stopwords.php');
         //$T->stop('import()');
         foreach($Stopwords[$Language] as $Stopword){
            $Content = preg_replace('/ '.$Stopword.' /',' ',$Content);
          // end foreach
         }
         //$T->stop('Stopwords ersetzen');

         // Wörter mit nur zwei Buchstaben entfernen
         //$T->start('Wörter mit > 2 Buchstaben ersetzen');
         $Content = preg_replace('/(\s[A-Za-z]{1,2})\s/','',$Content);
         //$T->stop('Wörter mit > 2 Buchstaben ersetzen');

         // Timer stoppen
         //$T->stop('fulltextsearchIndexer->__normalizeContent()');

         // Inhalt zurückgeben
         return $Content;

       // end function
      }

    // end class
   }
?>