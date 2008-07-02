<?php
   import('modules::cmscontentparser::data','cmsContentMapper');
   import('modules::cmscontentparser::biz','cmsArticle');
   import('tools::variablen','variablenHandler');
   import('tools::string','bbCodeParser');
   import('core::singleton','Singleton');
   import('core::benchmark','benchmarkTimer');


   /**
   *  @package modules::contentparser::biz
   *  @module cmsContentManager
   *
   *  Implementiert den CMS-Content-Manager.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 14.08.2006<br />
   *  Version 0.2, 15.08.2006<br />
   *  Version 0.3, 17.12.2006 (Konfiguration von ContentTags V3 eingeführt;Dokumentation erweitert)<br />
   *  Version 0.4, 16.03.2007 (Neue Version für den Parser eingeführt; Konfiguration nur noch in einer Datei)<br />
   */
   class cmsContentManager extends coreObject
   {

      /**
      *  @private
      *  Information, ob nur öffentliche Seiten geladen werden dürfen.
      */
      var $__publicOnly;


      function cmsContentManager(){
      }


      /**
      *  @module getPageContent()
      *  @public
      *
      *  Gibt den Inhalt einer CMS-Seite zurück. Dabei werden alle ContentTags geparst<br />
      *  oder eingebunden.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 14.08.2006<br />
      *  Version 0.2, 15.08.2006 (Formatierung in bbCodeParser ausgelagert)<br />
      *  Version 0.3, 03.01.2008 (Umbau auf Index via Text und Nummer)<br />
      */
      function getPageContent($PageNo,$publicOnly = false){

         // Nur öffentliche Seiten anzeigen
         $this->__publicOnly = $publicOnly;


         // Seiten anhand von '.' in die einzelnen Seitennummern trennen
         $contentArray = array();
         $contentArray = split('[.]',$PageNo);


         // Inhalte aus der Datenbank lesen
         $contentBuffer = (string)'';

         $cCM = &$this->__getServiceObject('modules::cmscontentparser::data','cmsContentMapper');

         for($i = 0; $i < count($contentArray); $i++){
            $contentBuffer .= $cCM->getPageContent($contentArray[$i],$this->__publicOnly);
          // end for
         }


         // Text formatieren
         $bbCP = &$this->__getServiceObject('tools::string','bbCodeParser');
         $contentBuffer = $bbCP->parseText($contentBuffer);


         // Content-Module parsen und Inhalt ausgeben
         return $this->__parseContentTags($contentBuffer);

       // end function
      }


      /**
      *  @module __parseContentTags()
      *  @private
      *
      *  Parst alle in einem String enthaltenen Content-Module und gibt der geparsten HTML-Text zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 14.08.2006<br />
      *  Version 0.2, 15.08.2006 (Tags nach Version 1 UND 2 werden nun vollständig konfiguriert)<br />
      *  Version 0.3, 17.12.2006 (Tags nach Version 3 werden nun vollständig konfiguriert)<br />
      *  Version 0.4, 18.03.2007 (Implementierung nach PC V2)<br />
      */
      function __parseContentTags($contentBuffer){

         // Timer starten
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('__parseContentTags');


         // Konfiguration lesen
         $Config = &$this->__getConfiguration('modules::cmscontentparser','cmscontenttags');
         $contentTags = $Config->getConfiguration();


         // Konfigurierte Tags parsen
         foreach($contentTags AS $Tag => $Config){

            if($Config['ContentTag.Version'] == 2){

               //
               // Einfache String-Tags parsen
               //

               if(
                  strtolower($Config['ContentTag.Typ']) == 'string'
                  &&
                  substr_count($contentBuffer,$Config['ContentTag.TagString'])
                 ){

                  while(substr_count($contentBuffer,$Config['ContentTag.TagString']) > 0){

                     // Modul instanziieren
                     $Module = new Page($Config['ContentTag.Name'],false);

                     // Context setzen
                     $Module->set('Context',$this->__Context);

                     // Template laden
                     $Module->loadDesign($Config['ContentTag.Namespace'],$Config['ContentTag.Template']);

                     // Inhalt einsetzen
                     $contentBuffer = str_replace($Config['ContentTag.TagString'],$Module->transform(),$contentBuffer);

                     unset($Module);

                   // end while
                  }

                // end if
               }


               //
               // RegExp-Tags parsen
               //

               // Matches initialisieren
               $Matches = array();

               // String-Tags mit Parametern parsen
               if(
                  strtolower($Config['ContentTag.Typ']) == 'regexp'
                  &&
                  preg_match_all($Config['ContentTag.TagString'],$contentBuffer,$Matches,PREG_SET_ORDER)
                 ){

                  // Matches iterieren
                  for($i = 0; $i < count($Matches); $i++){

                     // Timer starten
                     $ID = $Config['ContentTag.Name'];
                     $T->start($ID);

                     // Modul instanziieren
                     $Module = new Page($Config['ContentTag.Name'],false);

                     // Context setzen
                     $Module->set('Context',$this->__Context);

                     // Template laden
                     $Module->loadDesign($Config['ContentTag.Namespace'],$Config['ContentTag.Template']);

                     // Parameter in Document einsetzen
                     $Document = &$Module->getByReference('Document');
                     $Document->setAttribute('ConfigParam',trim($Matches[$i][1]));

                     // Inhalt einsetzen
                     $SearchPattern = str_replace('{VALUE}',$Matches[$i][1],$Config['ContentTag.ReplacePattern']);
                     $contentBuffer = preg_replace($SearchPattern,$Module->transform(),$contentBuffer);

                     // Timer stoppen
                     $T->stop($ID);

                     unset($Module,$Document);

                   // end for
                  }

                // end if
               }

             // end if
            }
            else{

               // Tag-Version 1 parsen (mit Parser-Methode)
               if(
                  (
                   strtolower($Config['ContentTag.Typ']) == 'string'
                   &&
                   substr_count($contentBuffer,$Config['ContentTag.TagString'])
                  )
                  ||
                  (
                   strtolower($Config['ContentTag.Typ']) == 'regexp'
                   &&
                   preg_match($Config['ContentTag.TagString'],$contentBuffer)
                  )
                 ){

                  // Modul einbinden
                  import($Config['ContentTag.Namespace'],$Config['ContentTag.Modul']);

                  // Klasse initialisieren
                  $Class = new $Config['ContentTag.Class'];

                  // Tag ausführen
                  $contentBuffer = $Class->{$Config['ContentTag.ParserMethod']}($contentBuffer);

                  // Klasse aus Speicher löschen
                  unset($Class);

                // end if
               }

             // end else
            }

          // end foreach
         }


         // Timer stoppen
         $T->stop('__parseContentTags');


         // Geparsten Text zurückgeben
         return $contentBuffer;

       // end function
      }

    // end class
   }
?>