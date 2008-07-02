<?php
   import('modules::suche::biz','SucheManager');
   import('tools::variablen','variablenHandler');
   import('tools::link','linkHandler');
   import('tools::string','bbCodeParser');


   /**
   *  @package modules::suche::pres::documentcontroller
   *  @module SucheTag
   *
   *  Implementiert den DocumentController des Designs "suche.html"<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 29.03.2006<br />
   *  Version 0.2, 07.05.2006<br />
   *  Version 0.3, 03.09.2006 (Verhalten bei leeren Suchbegrigg geändert)<br />
   *  Version 0.4, 03.01.2007 (Update für Funktion unter PageController V2)<br />
   */
   class suche_v2_controller extends baseController
   {

      /**
      *  @private
      */
      var $_LOCALS;


      /**
      *  @module suche_v2_controller()
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2006<br />
      *  Version 0.2, 07.05.2006<br />
      */
      function suche_v2_controller(){
         $this->_LOCALS = variablenHandler::registerLocal(array('Begriff' => ''));
       // end function
      }


      /**
      *  @module transformContent()
      *
      *  Implementiert die abstrakte Methode transformContent().<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2006<br />
      *  Version 0.2, 07.05.2006<br />
      *  Version 0.3, 03.01.2007 (Update für Funktion unter PageController V2)<br />
      *  Version 0.4, 03.01.2007 (Bug von Version 0.3 behoben)<br />
      */
      function transformContent(){

         if(empty($this->_LOCALS['Begriff'])){

            $Template = & $this->__getTemplate('KeinBegriff');
            $this->setPlaceHolder('Inhalt',$Template->transformTemplate());

          // end if
         }
         else{
            $this->setPlaceHolder('Inhalt',$this->__erzeugeSuchAusgabe());
          // end else
         }

       // end function
      }


      /**
      *  @module __erzeugeSuchAusgabe()
      *  @private
      *
      *  Erzeugt die Ergebnis-Liste der Suche.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2006<br />
      *  Version 0.2, 07.05.2006<br />
      *  Version 0.3, 03.01.2007 (Update für Funktion unter PageController V2)<br />
      */
      function __erzeugeSuchAusgabe(){

         if(strlen($this->_LOCALS['Begriff']) > 0){

            // Manager instanzieren
            $M = &Singleton::getInstance('SucheManager');
            $Liste = $M->ladeSuchergebnisse($this->_LOCALS['Begriff']);
            $bbCP = &$this->__getServiceObject('tools::string','bbCodeParser');


            // Ausgabe erzeugen
            $Ausgabe = (string)'';


            // Text in Ausgabe einsetzen
            $Template__HeaderText = $this->__getTemplate('HeaderText');
            $Template__HeaderText->setPlaceHolder('Begriff',$this->_LOCALS['Begriff']);
            $Ausgabe .= $Template__HeaderText->transformTemplate();


            // Ausgabe, wenn kein Ergebnis gefunden wurde
            if(count($Liste) < 1){

               $Template__KeinErgebnis = $this->__getTemplate('KeinErgebnis');
               $Ausgabe .= $Template__KeinErgebnis->transformTemplate();

             // end if
            }


            // Referenz des Templates 'Ergebnis' holen
            $Template__Ergebnis = & $this->__getTemplate('ErgebnisEintrag');


            // Ergebnisliste generieren
            $Reg = &Singleton::getInstance('Registry');
            $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');

            for($i = 0; $i < count($Liste); $i++){

               // Name
               $Template__Ergebnis->setPlaceHolder('Name',$Liste[$i]->zeige('Name'));

               // Relevanz
               $Template__Ergebnis->setPlaceHolder('Relevanz',$Liste[$i]->zeige('Relevanz').'%');

               // Link
               $Template__Ergebnis->setPlaceHolder('Link',$URLBasePath.'/?Seite='.$Liste[$i]->zeige('ID'));
               $Link = linkHandler::generateLink($URLBasePath.'/',array('Seite' => $Liste[$i]->zeige('ID')));
               $Template__Ergebnis->setPlaceHolder('Link',$Link);

               // Text der Seite aufgereiten
               $Text = $Liste[$i]->zeige('Inhalt'); // Inhalt aus Objekt ziehen
               $Text = $bbCP->parseText($Text); // Formatierungs-Tags parsen
               $Text = strip_tags($Text); // HTML aus String entfernen
               $Text = $this->__loescheContentModulTags($Text); // Module-Tags löschen
               $Text = substr($Text,0,200); // Ersten 200 Zeichen extrahieren
               $Template__Ergebnis->setPlaceHolder('Text',$Text.'...');

               // Template transformieren
               $Ausgabe .= $Template__Ergebnis->transformTemplate();

             // end for
            }

            return $Ausgabe;

          // end if
         }
         else{

            // Meldung 'KeinBegriff' ausgeben
            $Template__KeinBegriff = &$this->__getTemplate('KeinBegriff');
            return $Template__KeinBegriff->tranformTemplate();

          // end else
         }

       // end function
      }


      /**
      *  @module __loescheContentModulTags()
      *  @private
      *
      *  Entfernt Content-Modul-Tags aus einem String.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.05.2006<br />
      */
      function __loescheContentModulTags($Text){
         return preg_replace("=\[([A-Za-z0-9\= ]+)\]=","",$Text);
       // end function
      }

    // end class
   }
?>