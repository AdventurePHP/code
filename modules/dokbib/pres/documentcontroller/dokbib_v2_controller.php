<?php
   import('modules::baumstruktur::biz','BaumManager');
   import('modules::baumstruktur::pres','BaumAnzeige');
   import('modules::dokbib::biz','DokBibManager');
   import('tools::validator','myValidator');
   import('tools::variablen','variablenHandler');
   import('tools::link','linkHandler');
   import('tools::cache','cacheV4Manager');
   import('tools::string','stringAssistant');


   /**
   *  @package modules::baumstruktur::pres
   *  @module dokbib_v2_controller
   *
   *  Implementiert den DocumentController des Templates 'dokbib.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 04.07.2005<br />
   *  Version 0.2, 17.03.2007 (Implementierung nach PC V2)<br />
   */
   class dokbib_v2_controller extends baseController
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen vor.
      */
      var $_LOCALS;


      /**
      *  @module dokbib_v2_controller()
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.07.2005<br />
      *  Version 0.2, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function dokbib_v2_controller(){
         $this->_LOCALS = variablenHandler::registerLocal(array('SuchText','Seite'));
       // end function
      }


      /**
      *  @module transformContent()
      *  @public
      *
      *  Implementiert die abstrakte Methode des baseControllers.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.07.2005<br />
      *  Version 0.2, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function transformContent(){

         // Ausgabe erzeugen
         $Template__Anzeige = &$this->__getTemplate('Anzeige');


         // Baum einsetzen
         $Template__Anzeige->setPlaceHolder('Baum',$this->__erzeugeBaumAusgabe());


         // SuchEingabe einsetzen
         $Template__Anzeige->setPlaceHolder('SuchEingabe',$this->__erzeugeSuchenFormular());


         // SuchAusgabe einsetzen
         if(myValidator::validateText($this->_LOCALS['SuchText']) && isset($_REQUEST['Suche'])){
            $Template__Anzeige->setPlaceHolder('SuchAusgabe',$this->__erzeugeSuchAusgabe());
          // end if
         }


         // Ausgabe erzeugen
         $this->setPlaceHolder('Content',$Template__Anzeige->transformTemplate());

       // end function
      }


      /**
      *  @module __erzeugeBaumAusgabe()
      *  @private
      *
      *  Erzeugt die Ausgabe des Baumes.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.07.2005<br />
      *  Version 0.2, 28.03.2007 (Änderung wegen Neu-Implementierung des cacheV4Managers)<br />
      */
      function __erzeugeBaumAusgabe(){

         $BaumAusgabe = (string)'';

         // Baum-Manager instanzieren
         $BM = &$this->__getServiceObject('modules::baumstruktur::biz','BaumManager');
         $BM->ladePfadeInSession();


         // Baum laden
         $Baum = $BM->ladeBaum();


         // Seiten-Cache löschen
         $cM = &$this->__getAndInitServiceObject('tools::cache','cacheV4Manager','cms');
         $cM->clearPageCache($this->_LOCALS['Seite']);


         // Baum-Anzeige genereiren
         $BA = &$this->__getServiceObject('modules::baumstruktur::pres','BaumAnzeige');
         $BaumAusgabe = $BA->zeigeBaum($Baum);


         // Baum zurückgeben
         return $BaumAusgabe;

       // end function
      }


      /**
      *  @module __erzeugeSuchAusgabe()
      *  @private
      *
      *  Erzeugt die Ausgabe der Suche.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.07.2005<br />
      *  Version 0.2, 22.07.2005<br />
      *  Version 0.3, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function __erzeugeSuchAusgabe(){

         // Ergebnisse laden
         $M = &$this->__getServiceObject('modules::dokbib::biz','DokBibManager');
         $Ergebnisse = $M->ladeSuchErgebnisse($this->_LOCALS['SuchText']);


         // Ergebnis-Puffer initialisieren
         $SuchAusgabe = (string)'';


         // Falls Suchausgabe leer
         if(count($Ergebnisse) < 1){

            $Template__SuchErgebnisLeer = &$this->__getTemplate('SuchErgebnisLeer');
            $SuchAusgabe = $Template__SuchErgebnisLeer->transformTemplate();

          // end if
         }
         else{

            // SuchErgebnis
            $Template__SuchErgebnis = &$this->__getTemplate('SuchErgebnis');


            // SuchAusgabe
            $Template__SuchAusgabe = &$this->__getTemplate('SuchAusgabe');


            // initialize base path
            $Reg = &Singleton::getInstance('Registry');
            $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');


            // BildPfad
            $Template__SuchErgebnis->setPlaceHolder('BildPfad',$URLBasePath.'/bild.php?Bild=balken.gif');


            // Ausgabepuffer initialisieren
            $Ausgabe = (string)'';

            for($i = 0; $i < count($Ergebnisse); $i++){

               // Pfad darstellen
               $PfadNameTemp = array();
               $PfadLinkTemp = array();
               $Pfad = $Ergebnisse[$i]->zeigePfad();

               for($j = 0; $j < count($Pfad); $j++){
                  $PfadNameTemp[] = $Pfad[$j]['Name'];
                  $PfadLinkTemp[] = $Pfad[$j]['ID'];
                // end for
               }


               // Pfad einsetzen
               $Template__SuchErgebnis->setPlaceHolder('PfadName',implode(' > ',$PfadNameTemp));


               // Link einsetzen
               $PfadLink = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Pfad' => implode(';',$PfadLinkTemp),'BaumAktion' => 'Oeffnen'));
               $Template__SuchErgebnis->setPlaceHolder('PfadLink',$PfadLink);


               // Datum und Relevanz anzeigen
               $Template__SuchErgebnis->setPlaceHolder('Datum',$Ergebnisse[$i]->zeigeDatum());
               $Template__SuchErgebnis->setPlaceHolder('Relevanz',$Ergebnisse[$i]->zeigeRelevanz());


               // Titel des Ergebnisses als Link, oder einfachen Titel darstellen (eigenes Template)
               if($Ergebnisse[$i]->zeigeTyp() == 'file'){

                  // Template für Link laden
                  $Template__Titel = &$this->__getTemplate('Ergebnis_Datei');

                  // Link einsetzen
                  $Link = $URLBasePath.'/datei.php?Datei='.$Ergebnisse[$i]->zeigeLink().'&Pfad='.$Ergebnisse[$i]->zeigeDokumentenPfad();
                  $Template__Titel->setPlaceHolder('DateiLink',$Link);

                // end if
               }
               else{

                  // Template für einfachen Titel laden
                  $Template__Titel = &$this->__getTemplate('Ergebnis_Ordner');

                // end else
               }


               // Titel einsetzen
               $Template__Titel->setPlaceHolder('Titel',stringAssistant::escapeSpecialCharacters($Ergebnisse[$i]->zeigeName()));


               // Titel-Template in SuchErgebnis einsetzen
               $Template__SuchErgebnis->setPlaceHolder('Titel',$Template__Titel->transformTemplate());


               // Ausgabe des Suchergebnisses erzeugen
               $Ausgabe .= $Template__SuchErgebnis->transformTemplate();

             // end for
            }


            // SuchErgebnisse in SuchAusgabe einsetzen
            $Template__SuchAusgabe->setPlaceHolder('SuchErgebnisse',$Ausgabe);


            // Suchausgabe zusammensetzen
            $SuchAusgabe = $Template__SuchAusgabe->transformTemplate();

          // end else
         }


         // Suchergebniss zurückgeben
         return $SuchAusgabe;

       // end function
      }


      /**
      *  @module __erzeugeSuchenFormular()
      *  @private
      *
      *  Erzeugt die Ausgabe des Suchformulars.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 04.07.2005<br />
      *  Version 0.2, 17.03.2007 (Implementierung nach PC V2; Form neu erstellt)<br />
      */
      function __erzeugeSuchenFormular(){

         // Referenz auf Template holen
         $Template__Suche = &$this->__getTemplate('Suche');


         // Referenz auf Formular holen
         $Form__KulturInfoSuche = &$this->__getForm('KulturInfoSuche');


         // Action setzen
         $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Pfad' => '','BaumAktion' => '','BaumID' => '','SuchText' => '','Suche' => ''));
         $Form__KulturInfoSuche->setAttribute('action',$Link);


         // Hidden
         $Hidden = &$Form__KulturInfoSuche->getFormElementByName('Seite');
         $Hidden->setAttribute('value',$this->_LOCALS['Seite']);


         // Formular zurückgeben
         return $Form__KulturInfoSuche->transformForm();

       // end function
      }

    // end class
   }
?>