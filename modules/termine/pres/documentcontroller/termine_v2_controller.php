<?php
   import('core::singleton','Singleton');
   import('tools::datetime','dateTimeManager');
   import('tools::link','linkHandler');
   import('tools::string','bbCodeParser');
   import('tools::variablen','variablenHandler');
   import('modules::termine::biz','termManager');
   import('modules::termine::biz','termObject');


   /**
   *  @package modules::termine::pres
   *  @module termine_v2_controller
   *
   *  Implementiert den DocumentController für das Template 'termin.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 17.03.2007 (Migration des Termintags auf PC V2)<br />
   */
   class termine_v2_controller extends baseController
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen.
      */
      var $_LOCALS;

      var $__URLBasePath;


      /**
      *  @module termine_v2_controller
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.03.2007 (Migration des Termintags auf PC V2)<br />
      */
      function termine_v2_controller(){
         $this->_LOCALS = variablenHandler::registerLocal(array('TID' => ''));
         $Reg = &Singleton::getInstance('Registry');
         $this->__URLBasePath = $Reg->retrieve('apf::core','URLBasePath');
       // end function
      }


      /**
      *  @module termine_v2_controller
      *  @public
      *
      *  Implementiert die abstrakte Methode des baseControllers.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.03.2007 (Migration des Termintags auf PC V2)<br />
      */
      function transformContent(){
         $this->setPlaceHolder('Content',$this->__erzeugeTerminListe());
       // end function
      }


      /**
      *  @module __erzeugeNormaleZeile()
      *  @private
      *
      *  Die Funktion erzeugt eine normale Zeile im Terminkalender.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 2005<br />
      *  Version 0.2, 2005<br />
      *  Version 0.3, 16.05.2005<br />
      *  Version 0.4, 01.06.2005<br />
      *  Version 0.5, 13.11.2005<br />
      *  Version 0.6. 22.01.2006<br />
      *  Version 0.7, 03.03.2007 (Calltime-Pass-Reference-Problem aufgelöst)<br />
      *  Version 0.8, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function __erzeugeNormaleZeile(&$TerminDatenObjekt){

         // Template für Zeilenausgabe
         $Template__Zeile_Normal = &$this->__getTemplate('Zeile_Normal');


         // Platzhalter setzen
         $bbCP = &$this->__getServiceObject('tools::string','bbCodeParser');
         $Template__Zeile_Normal->setPlaceHolder('Datum',dateTimeManager::convertDate2Normal($TerminDatenObjekt->zeigeDatum()));
         $Template__Zeile_Normal->setPlaceHolder('Text',$bbCP->parseText($TerminDatenObjekt->zeigeText()));
         $Template__Zeile_Normal->setPlaceHolder('Link',$this->__erzeugeDetailLink($TerminDatenObjekt));


         // AufklappLink einsetzen
         $DetailText = trim($TerminDatenObjekt->zeigeDetailText());
         if(!empty($DetailText)){
            $Template__Zeile_Normal->setPlaceHolder('AufklappLink',$this->__erzeugeAufklappLink($TerminDatenObjekt));
          // end if
         }
         else{
            $Template__Zeile_Normal->setPlaceHolder('AufklappLink','');
          // end else
         }


         // Ausgabe für Zeile erzeugen
         return $Template__Zeile_Normal->transformTemplate();

       // end function
      }


      /**
      *  @module __erzeugeAufklappLink()
      *  @private
      *
      *  Die Funktion erzeugt einen Aufklapp-Link.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 2005<br />
      *  Version 0.2, 2005<br />
      *  Version 0.3, 16.05.2005<br />
      *  Version 0.4, 01.06.2005<br />
      *  Version 0.5, 13.11.1005<br />
      *  Version 0.6, 03.03.2007 (Calltime-Pass-Reference-Problem aufgelöst)<br />
      *  Version 0.7, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function __erzeugeAufklappLink($TerminDatenObjekt){

         // AufklappDesign-Template holen
         $Template__AufklappDesign = &$this->__getTemplate('AufklappLink');

         // Link
         $AufklappLink = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('TID' => $TerminDatenObjekt->zeigeTIndex()));
         $Template__AufklappDesign->setPlaceHolder('Link',$AufklappLink);

         // Titel
         $Template__AufklappDesign->setPlaceHolder('Titel','Detailinformationen anzeigen');

         // Bild
         $Template__AufklappDesign->setPlaceHolder('Bild',$this->__URLBasePath.'/bild.php?Bild=termine_details_aufrufen.gif');

         // Ausgabe erzeugen
         return $Template__AufklappDesign->transformTemplate();

       // end function
      }


      /**
      *  @module __erzeugeAufgeklappteZeile()
      *  @private
      *
      *  Die Funktion erzeugt eine aufgeklappte Zeile im Terminkalender.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 2005<br />
      *  Version 0.2, 2005<br />
      *  Version 0.3, 16.05.2005<br />
      *  Version 0.4, 01.06.2005<br />
      *  Version 0.5, 13.11.1005<br />
      *  Version 0.6, 03.03.2007 (Calltime-Pass-Reference-Problem aufgelöst)<br />
      *  Version 0.7, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function __erzeugeAufgeklappteZeile(&$TerminDatenObjekt){

         // Template für Zeilenausgabe
         $Template__Zeile_Aufgeklappt = &$this->__getTemplate('Zeile_Aufgeklappt');


         // Platzhalter für Hauptdesign ersetzen
         $bbCP = &$this->__getServiceObject('tools::string','bbCodeParser');
         $Template__Zeile_Aufgeklappt->setPlaceHolder('Datum',dateTimeManager::convertDate2Normal($TerminDatenObjekt->zeigeDatum()));
         $Template__Zeile_Aufgeklappt->setPlaceHolder('Text',$bbCP->parseText($TerminDatenObjekt->zeigeText()));


         // Link
         $Template__Zeile_Aufgeklappt->setPlaceHolder('Link',$this->__erzeugeDetailLink($TerminDatenObjekt));


         // DetailText
         $Template__Zeile_Aufgeklappt->setPlaceHolder('DetailText',$bbCP->parseText($TerminDatenObjekt->zeigeDetailText()));


         // AufklappLink
         $Template__Zeile_Aufgeklappt->setPlaceHolder('AufklappLink',$this->__erzeugeZuklappLink());


         // Ausgabe für Zeile erzeugen
         return $Template__Zeile_Aufgeklappt->transformTemplate();

       // end function
      }


      /**
      *  @module __erzeugeZuklappLink()
      *  @private
      *
      *  Die Funktion erzeugt einen Zuklapp-Link.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 2005<br />
      *  Version 0.2, 2005<br />
      *  Version 0.3, 16.05.2005<br />
      *  Version 0.4, 01.06.2005<br />
      *  Version 0.5, 13.11.1005<br />
      *  Version 0.6, 01.09.2006 (Link-Generierung angepasst)<br />
      *  Version 0.7, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function __erzeugeZuklappLink(){

         // AufklappDesign
         $Template__AufklappDesign = &$this->__getTemplate('AufklappLink');

         // Link
         $AufklappLink = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('TID' => ''));
         $Template__AufklappDesign->setPlaceHolder('Link',$AufklappLink);

         // Titel
         $Template__AufklappDesign->setPlaceHolder('Titel','Detailinformationen schließen');

         // Bild
         $Template__AufklappDesign->setPlaceHolder('Bild',$this->__URLBasePath.'/bild.php?Bild=termine_details_aufrufen_2.gif');

         // Ausgabe erzeugen
         return $Template__AufklappDesign->transformTemplate();

       // end function
      }


      /**
      *  @module __erzeugeDetailLink()
      *  @private
      *
      *  Die Funktion erzeugt einen Detail-Link.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 2005<br />
      *  Version 0.2, 2005<br />
      *  Version 0.3, 16.05.2005<br />
      *  Version 0.4, 01.06.2005<br />
      *  Version 0.5, 13.11.1005<br />
      *  Version 0.6, 23.07.2006<br />
      *  Version 0.7, 03.03.2007 (Calltime-Pass-Reference-Problem aufgelöst)<br />
      *  Version 0.8, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function __erzeugeDetailLink(&$TerminDatenObjekt){

         // Link anzeigen, falls vorhanden
         $Link = (string)'';

         $TerminDokument = $TerminDatenObjekt->zeigeLink();

         if(!empty($TerminDokument) && $TerminDokument != ''){

            // Template für Detaillink holen
            $Template__DetailLink = &$this->__getTemplate('DetailLink');

            // Template füllen
            $Template__DetailLink->setPlaceHolder('DetailLink',$this->__URLBasePath.'/datei.php?Datei='.basename($TerminDatenObjekt->zeigeLink()));
            $Template__DetailLink->setPlaceHolder('DetailBild',$this->__URLBasePath.'/bild.php?Bild=termine_details.gif');

            $Link = $Template__DetailLink->transformTemplate();

          // end if
         }
         else{
            $Link = '&nbsp;';
          // end else
         }

         // Link zurückgeben
         return $Link;

       // end function
      }


      /**
      *  @module __erzeugeTerminListe()
      *  @private
      *
      *  Die Funktion erzeugt die Termin-Liste.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 2005<br />
      *  Version 0.2, 2005<br />
      *  Version 0.3, 16.05.2005<br />
      *  Version 0.4, 01.06.2005<br />
      *  Version 0.5, 13.11.1005<br />
      *  Version 0.6, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function __erzeugeTerminListe(){

         // Template für gesamte Ausgabe
         $Template__Termine = &$this->__getTemplate('Termine');


         // Inhalte aus Datenmanager ziehen
         $tM = &$this->__getServiceObject('modules::termine::biz','termManager');
         $TerminDaten = $tM->loadTerms();


         // Ausgabepuffer
         $Ausgabe = (string)'';

         if(count($TerminDaten) <= 0){
            $Template__KeinTermin = &$this->__getTemplate('KeinTermin');
            $Ausgabe .= $Template__KeinTermin->transformTemplate();
          // end if
         }


         // Ausgabe zusammensetzen
         for($i = 0; $i < count($TerminDaten); $i++){

            if($this->_LOCALS['TID'] == $TerminDaten[$i]->zeigeTIndex()){
               $Ausgabe .= $this->__erzeugeAufgeklappteZeile($TerminDaten[$i])."\n";
             // end if
            }
            else{
               $Ausgabe .= $this->__erzeugeNormaleZeile($TerminDaten[$i])."\n";
             // end if
            }

          // end for
         }

         // Inhalte in Termine-Template einsetzen
         $Template__Termine->setPlaceHolder('TerminInhalte',$Ausgabe);
         return $Template__Termine->transformTemplate();

       // end function
      }

    // end class
   }
?>