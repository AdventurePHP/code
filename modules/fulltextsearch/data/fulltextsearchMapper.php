<?php
   import('core::database','connectionManager');


   /**
   *  @package modules::fulltextsearch::data
   *  @module fulltextsearchMapper
   *
   *  Implementiert die Datenschicht für die Volltextsuche.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 10.03.2008<br />
   */
   class fulltextsearchMapper extends coreObject
   {

      function fulltextsearchMapper(){
      }


      /**
      *  @module loadSearchResult()
      *  @public
      *
      *  Läd Ergebnis-Objekte gemäß einem Suchwort.<br />
      *
      *  @param string $SearchString; Suchwort, oder mehrere Wörter per Space getrennt
      *  @return array $SearchResults; Liste von Such-Ergebnis-Objekten
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.03.2008<br />
      */
      function loadSearchResult($SearchString){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('fulltextsearchMapper::loadSearchResult()');

         // Konfiguration holen
         $Config = &$this->__getConfiguration('modules::fulltextsearch','fulltextsearch');

         // Connection holen
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $SQL = &$cM->getConnection($Config->getValue('Database','ConnectionKey'));

         // WHERE-Bedingung erzeugen
         $SearchStringArray = split(' ',$SearchString);
         $count = count($SearchStringArray);
         $WHERE = array();
         if($count > 1){

            for($i = 0; $i < $count; $i++){
               $WHERE[] = 'search_word.Word LIKE \'%'.strtolower($SearchStringArray[$i]).'%\'';
             // end for
            }

          // end if
         }
         else{
            $WHERE[] = 'search_word.Word LIKE \'%'.strtolower($SearchString).'%\'';
          // end else
         }

         // Statement erzeugen
         $select = 'SELECT search_articles.*, search_index.*, search_word.* FROM search_articles
                    INNER JOIN search_index ON search_articles.ArticleID = search_index.ArticleID
                    INNER JOIN search_word ON search_index.WordID = search_word.WordID
                    WHERE '.implode('OR ',$WHERE).'
                    GROUP BY search_articles.ArticleID
                    ORDER BY search_index.WordCount DESC, search_articles.ModificationTimestamp DESC
                    LIMIT 20';

         // Abfrage ausführen
         $result = $SQL->executeTextStatement($select);

         // Ergebnisse in DomainObjekte mappen
         $Results = array();

         while($data = $SQL->fetchData($result)){
            $Results[] = $this->__mapSearchResult2DomainObject($data);
          // end while
         }

         // Timer stoppen
         $T->stop('fulltextsearchMapper::loadSearchResult()');

         // Ergebnisse laden
         return $Results;

       // end function
      }


      /**
      *  @module loadPages()
      *  @public
      *
      *  Läd alle Seiten im Index für eine bestimmte Sprache.<br />
      *
      *  @param string $Language; Sprache der zu ladenen Seiten
      *  @return array $Pages; Liste von Seiten-Objekten
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.03.2008<br />
      */
      function loadPages($Language = 'de'){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('fulltextsearchMapper::loadPages()');

         // Konfiguration holen
         $Config = &$this->__getConfiguration('modules::fulltextsearch','fulltextsearch');

         // Connection holen
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $SQL = &$cM->getConnection($Config->getValue('Database','ConnectionKey'));

         // Statement erzeugen
         $select = 'SELECT * FROM search_articles
                    WHERE
                       Language = \''.$Language.'\'
                       AND
                       Name != \'404\'
                    ORDER BY Title ASC';

         // Abfrage ausführen
         $result = $SQL->executeTextStatement($select);

         // Ergebnisse in DomainObjekte mappen
         $Pages = array();

         while($data = $SQL->fetchData($result)){
            $Pages[] = $this->__mapSearchResult2DomainObject($data);
          // end while
         }

         // Timer stoppen
         $T->stop('fulltextsearchMapper::loadPages()');

         // Ergebnisse laden
         return $Pages;

       // end function
      }


      /**
      *  @module getPageTags()
      *  @public
      *
      *  Läd eine Liste der in der Seite vorhandenen Tags.<br />
      *
      *  @param string $PageID; ID der aktuellen Seite
      *  @return array $Tags; Liste von Tags
      *
      *  @param string $Language; Sprache der zu ladenen Seiten
      *  @return array $Pages; Liste von Seiten-Objekten
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.03.2008<br />
      */
      function getPageTags($PageID){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('fulltextsearchMapper::getPageTags()');

         // Konfiguration holen
         $Config = &$this->__getConfiguration('modules::fulltextsearch','fulltextsearch');

         // Connection holen
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $SQL = &$cM->getConnection($Config->getValue('Database','ConnectionKey'));

         // Statement erzeugen
         $select = 'SELECT search_word.Word AS Word, search_index.WordCount as Count FROM search_word
                    INNER JOIN search_index ON search_word.WordID = search_index.IndexID
                    INNER JOIN search_articles ON search_index.ArticleID = search_articles.ArticleID
                    WHERE
                       search_articles.Name LIKE \''.$PageID.'%\'
                       AND search_articles.Language = \''.$this->__Language.'\'
                    ORDER BY search_index.WordCount DESC
                    LIMIT 20;';

         // Abfrage ausführen
         $result = $SQL->executeTextStatement($select);

         // Ergebnisse in DomainObjekte mappen
         $Tags = array();

         while($data = $SQL->fetchData($result)){
            $Tags[$data['Word']] = $data['Count'];
          // end while
         }

         // Timer stoppen
         $T->stop('fulltextsearchMapper::getPageTags()');

         // Ergebnisse laden
         return $Tags;

       // end function
      }


      /**
      *  @module __mapSearchResult2DomainObject()
      *  @private
      *
      *  Mappt ein Result-Array in ein Ergebnis-Objekte.<br />
      *
      *  @param array $ResultSet; Datenbank-Result-Set
      *  @return object $SearchResult; Such-Ergebnis-Objekt
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.03.2008<br />
      */
      function __mapSearchResult2DomainObject($ResultSet){

         // Objekt erstellen
         $SearchResult = new searchResult();

         if(isset($ResultSet['Name'])){
            $SearchResult->set('Name',$ResultSet['Name']);
          // end if
         }

         if(isset($ResultSet['Title'])){
            $SearchResult->set('Title',$ResultSet['Title']);
          // end if
         }

         if(isset($ResultSet['Language'])){
            $SearchResult->set('Language',$ResultSet['Language']);
          // end if
         }

         if(isset($ResultSet['ModificationTimestamp'])){
            $SearchResult->set('LastMod',$ResultSet['ModificationTimestamp']);
          // end if
         }

         if(isset($ResultSet['WordCount'])){
            $SearchResult->set('WordCount',$ResultSet['WordCount']);
          // end if
         }

         if(isset($ResultSet['Word'])){
            $SearchResult->set('IndexWord',$ResultSet['Word']);
          // end if
         }

         // Objekt zurückliefern
         return $SearchResult;

       // end function
      }

    // end class
   }
?>