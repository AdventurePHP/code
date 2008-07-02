<?php
   import('tools::link','linkHandler');
   import('tools::variablen','variablenHandler');
   import('tools::validator','myValidator');
   import('tools::datetime','dateTimeManager');
   import('modules::schwarzesbrett::biz','schwarzesBrettEintrag');
   import('modules::schwarzesbrett::biz','schwarzesBrettManager');
   import('tools::string','bbCodeParser');
   import('tools::form','formManager');
   import('modules::pager::biz','pagerManager');


   /**
   *  @package modules::schwarzesbrett
   *  @module schwarzesbrett_v3_controller
   *
   *  Implementiert den DocumentController des Designs 'schwarzesbrett'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 2002<br />
   *  Version 0.2, 2003<br />
   *  Version 0.3, 2004<br />
   *  Version 0.4, 20.04.2005<br />
   *  Version 0.5, 24.12.2005 (Als Modul implementiert und optimiert)<br />
   *  Version 0.6, 11.03.2006<br />
   *  Version 0.7. 04.08.2006 (Implementierung nach PageController-Modell)<br />
   *  Version 0.8, 03.09.2006 (History-Anzeige hinzugefügt und Pager-Konfiguration upgedatet)<br />
   */
   class schwarzesbrett_v3_controller extends baseController
   {

      /**
      *  @public
      *  Hält lokale Variablen.
      */
      var $_LOCALS;
      var $__URLBasePath;


      /**
      *  @module schwarzesbrett_v3_controller()
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1. 04.08.2006<br />
      *  Version 0.2, 29.03.2007<br />
      */
      function schwarzesbrett_v3_controller(){

         $this->_LOCALS = variablenHandler::registerLocal(array('Aktion' => 'anzeigen',
                                                                'Text',
                                                                'Vorname',
                                                                'Name',
                                                                'Strasse',
                                                                'PLZ',
                                                                'Ort',
                                                                'Telefon',
                                                                'Fax',
                                                                'EMail',
                                                                'Anhang',
                                                                'History' => '0'
                                                               )
                                                         );

         $Reg = &Singleton::getInstance('Registry');
         $this->__URLBasePath = $Reg->retrieve('apf::core','URLBasePath');

       // end function
      }


      /**
      *  @module parseSchwarzesBrettTag()
      *  @public
      *
      *  Implementiert den Aktion-Handler für das schwarze Brett.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 24.12.2005<br />
      *  Version 0.2, 11.03.2006<br />
      *  Version 0.3, 04.08.2006 (Implementierung nach PageController-Modell)<br />
      */
      function transformContent(){

         // Ausgabepuffer
         $Ausgabe = (string)'';


         // Ausgabe erzeugen
         if($this->_LOCALS['Aktion'] == 'anzeigen'){
            $Ausgabe = $this->__erzeugeAusgabeAnsicht();
          // end if
         }


         // Neuen Eintrag verfassen
         if($this->_LOCALS['Aktion'] == 'erstellen'){

            if(myValidator::validateText($this->_LOCALS['Vorname']) && myValidator::validateText($this->_LOCALS['Name']) && myValidator::validateTelefon($this->_LOCALS['Telefon']) && myValidator::validateTelefon($this->_LOCALS['Fax']) && myValidator::validateText($this->_LOCALS['PLZ']) && myValidator::validateText($this->_LOCALS['Strasse'])){

               // Eintragsobjekt erzeugen
               $E = new schwarzesBrettEintrag();

               $E->setzeAttribut('Text',$this->_LOCALS['Text']);
               $E->setzeAttribut('Datum',dateTimeManager::generateDate());
               $E->setzeAttribut('Uhrzeit',dateTimeManager::generateTime());
               $E->setzeAttribut('Vorname',$this->_LOCALS['Vorname']);
               $E->setzeAttribut('Nachname',$this->_LOCALS['Name']);
               $E->setzeAttribut('Strasse',$this->_LOCALS['Strasse']);
               $E->setzeAttribut('PLZ',$this->_LOCALS['PLZ']);
               $E->setzeAttribut('Ort',$this->_LOCALS['Ort']);
               $E->setzeAttribut('Tel',$this->_LOCALS['Telefon']);
               $E->setzeAttribut('Fax',$this->_LOCALS['Fax']);
               $E->setzeAttribut('EMail',$this->_LOCALS['EMail']);

               // Eintrag speichern
               $M = &$this->__getServiceObject('modules::schwarzesbrett::biz','schwarzesBrettManager');
               $M->speichereEintrag($E);

             // end if
            }
            else{
               $Ausgabe = $this->__erzeugeVerfassenAnsicht();
             // end else
            }

          // end if
         }

         // Inhalt einsetzen
         $this->setPlaceHolder('Inhalt',$Ausgabe);

       // end function
      }


      /**
      *  @module __erzeugeAusgabeAnsicht()
      *  @private
      *
      *  Erzeugt die Ausgabe-Ansicht des schwarzen Brettes.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 24.12.2005<br />
      *  Version 0.2, 04.08.2006 (Implementierung nach PageController-Modell)<br />
      *  Version 0.3, 08.10.2006 (History-Parameter wird nun beim Wechsel zur Anzeige der aktuellen Einträge aus der URI gelöscht, was das Chaching verbessert)<br />
      */
      function __erzeugeAusgabeAnsicht(){

         // Referenz auf das Template holen
         $Template = & $this->__getTemplate('Ausgabe');


         // EintragenBild
         $Template->setPlaceHolder('EintragenBild',$this->__URLBasePath.'/bild.php?Bild=schwarzes_brett_eintrag_neu.gif');

         // Manager holen
         $M = &$this->__getServiceObject('modules::schwarzesbrett::biz','schwarzesBrettManager');

         // EintragenLink
         $Template->setPlaceHolder('EintragenLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array($M->getPagerStartName() => '', $M->getPagerCountName() => '','Aktion' => 'erstellen','History' => '')));

         // HistoryText + HistoryBild + HistoryKommentar
         if($this->_LOCALS['History'] == '0'){
            $Template->setPlaceHolder('HistoryBild',$this->__URLBasePath.'/bild.php?Bild=schwarzes_brett_history_off.png');
            $Template->setPlaceHolder('HistoryLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array($M->getPagerStartName() => '', $M->getPagerCountName() => '','History' => '1')));
            $Template->setPlaceHolder('HistoryKommentar','Klicken Sie hier, um archivierte Einträge anzuzeigen!');
          // end if
         }
         else{
            $Template->setPlaceHolder('HistoryBild',$this->__URLBasePath.'/bild.php?Bild=schwarzes_brett_history_on.png');
            $Template->setPlaceHolder('HistoryLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array($M->getPagerStartName() => '', $M->getPagerCountName() => '','History' => '')));
            $Template->setPlaceHolder('HistoryKommentar','Klicken Sie hier, um zur Anzeige der aktuellen Einträge zu wechseln!');
          // end if
         }


         // Pager erzeugen
         $Template->setPlaceHolder('Pager',$M->generatePager());


         // Einträge
         $Template->setPlaceHolder('Eintraege',$this->__erzeugeEintraegeAnsicht());


         // Ausgabe erzeugen
         return $Template->transformTemplate();

       // end function
      }


      /**
      *  @module __erzeugeEintraegeAnsicht()
      *  @private
      *
      *  Erzeugt die Ansicht der Einträge.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 24.12.2005<br />
      *  Version 0.2, 11.03.2006<br />
      *  Version 0.3, 04.08.2006 (Implementierung nach PageController-Modell)<br />
      */
      function __erzeugeEintraegeAnsicht(){

         $Ausgabe = (string)'';
         $bbCP = &$this->__getServiceObject('tools::string','bbCodeParser');


         // Referenz auf Template holen
         $Template = &$this->__getTemplate('Eintrag');


         // Daten laden
         $M = &$this->__getServiceObject('modules::schwarzesbrett::biz','schwarzesBrettManager');
         $Daten = $M->ladeEintraegeZurAnzeige();


         // Meldung zurückgeben, falls keine Einträge vorhanden sind
         if(count($Daten) == 0){

            $Template = &$this->__getTemplate('KeinEintrag');
            return $Template->transformTemplate();

          // end if
         }


         // Ausgabe zusammensetzen
         for($i = 0; $i < count($Daten); $i++){

            // HintergrundBild
            $Template->setPlaceHolder('HintergrundBild',$this->__URLBasePath.'/bild.php?Bild=schwarzes_brett_eintrag_darstellen.gif');

            // DatumUhrzeit
            $Template->setPlaceHolder('DatumUhrzeit',dateTimeManager::convertDate2Normal($Daten[$i]->zeigeAttribut('Datum')).', '.$Daten[$i]->zeigeAttribut('Uhrzeit'));

            // Text
            $Text = (string)'';
            if(($Daten[$i]->zeigeAttribut('Datum')) >= dateTimeManager::calculateDate(dateTimeManager::generateDate(),array('Jahr' => '0', 'Monat' => '0', 'Tag' => '3'))){
               $Text .= "<img src=\"".$this->__URLBasePath."/bild.php?Bild=schwarzes_brett_achtung.gif\" align=\"absmiddle\" />&nbsp;&nbsp;";
             // end if
            }
            $Template->setPlaceHolder('Text',$Text.$bbCP->parseText($Daten[$i]->zeigeAttribut('Text')));

            // VornameName
            $Template->setPlaceHolder('VornameName',$Daten[$i]->zeigeAttribut('Vorname')." ".$Daten[$i]->zeigeAttribut('Nachname'));

            // Strasse
            $Template->setPlaceHolder('Strasse',$Daten[$i]->zeigeAttribut('Strasse'));

            // PLZOrt
            $Template->setPlaceHolder('PLZOrt',$Daten[$i]->zeigeAttribut('PLZ')." ".$Daten[$i]->zeigeAttribut('Ort'));

            // Tel
            $Template->setPlaceHolder('Tel',$Daten[$i]->zeigeAttribut('Tel'));

            // Fax
            $Template->setPlaceHolder('Fax',$Daten[$i]->zeigeAttribut('Fax'));

            // EMail
            $EMail = $Daten[$i]->zeigeAttribut('EMail');
            if(!empty($EMail)){
               $Template->setPlaceHolder('EMail',"<br />\n<br />\n<strong>E-Mail:</strong> ".$EMail);
             // end if
            }

            // Anhang
            $Anhang = $Daten[$i]->zeigeAttribut('Anhang');

            if(!empty($Anhang)){
               $DateiName = basename($Anhang);
               $Template->setPlaceHolder('Anhang',"<br />\n<br />\n<strong>Anhang:</strong>&nbsp;\n<a href=\"#\" onClick=\"window.open('".$this->__URLBasePath."/datei.php?Datei=".basename($Anhang)."','Anhang','toolbar=no,location=no,directories=no,status=no,menubar=no,closed=no,scrollbars=yes,resizable=yes,copyhistory=yes,width=600,height=500')\">\n<img src=\"".$this->__URLBasePath."/bild.php?Bild=schwarzes_brett_eintrag_details.gif\" align=\"absmiddle\" border=\"0\" alt=\"Anhang zur Anzeige mit Klick &ouml;ffnen!\" />\n</a>\n");
             // end if
            }
            else{
               $Template->setPlaceHolder('Anhang','');
             // end else
            }


            // Template transformieren
            $Ausgabe .= $Template->transformTemplate();

          // end for
         }

         return $Ausgabe;

       // end function
      }


      /**
      *  @module __erzeugeVerfassenAnsicht()
      *  @private
      *
      *  Erzeugt die Ansicht zum Verfassen des neuen Eintrags.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 24.12.2005<br />
      *  Version 0.2, 04.08.2006 (Implementierung nach PageController-Modell)<br />
      */
      function __erzeugeVerfassenAnsicht(){

         $FORM = new formManager();

         // Referenz auf das Template holen
         $Template = & $this->__getTemplate('Formular');


         // FormBeginn
         $Template->setPlaceHolder('Form',$FORM->FormBeginn('SchwarzesBrettEintrag',$_SERVER['REQUEST_URI'],'post','multipart/form-data'));


         // Text
         $FORM->TextArea('Text',$this->_LOCALS['Text'],'eingabe_feld','height: 200px; width: 400px; overflow: auto;','0');
         $FORM->TextArea['Text']->setzeValidator('Eintragen');
         $Template->setPlaceHolder('Text',$FORM->TextArea['Text']->generiereTextArea());


         // Vorname
         $FORM->TextFeld('Vorname',$this->_LOCALS['Vorname'],'eingabe_feld','width: 280px; margin-left: 64px; margin-bottom: 1px;','0');
         $FORM->TextFeld['Vorname']->setzeValidator('Eintragen');
         $FORM->TextFeld['Vorname']->setzeZusatzTag('maxlength','20');
         $Template->setPlaceHolder('Vorname',$FORM->TextFeld['Vorname']->generiereTextFeld());


         // Nachname
         $FORM->TextFeld('Name',$this->_LOCALS['Name'],'eingabe_feld','width: 280px; margin-left: 80px; margin-bottom: 1px;','0');
         $FORM->TextFeld['Name']->setzeValidator('Eintragen');
         $FORM->TextFeld['Name']->setzeZusatzTag('maxlength','20');
         $Template->setPlaceHolder('Nachname',$FORM->TextFeld['Name']->generiereTextFeld());


         // Strasse
         $FORM->TextFeld('Strasse',$this->_LOCALS['Strasse'],'eingabe_feld','width: 280px; margin-left: 77px; margin-bottom: 1px;','0');
         $FORM->TextFeld['Strasse']->setzeValidator('Eintragen');
         $FORM->TextFeld['Strasse']->setzeZusatzTag('maxlength','40');
         $Template->setPlaceHolder('Strasse',$FORM->TextFeld['Strasse']->generiereTextFeld());


         // PLZ
         $FORM->TextFeld('PLZ',$this->_LOCALS['PLZ'],'eingabe_feld','width: 45px; margin-left: 67px; margin-bottom: 1px;','0');
         $FORM->TextFeld['PLZ']->setzeValidator('Eintragen');
         $FORM->TextFeld['PLZ']->setzeZusatzTag('maxlength','5');
         $Template->setPlaceHolder('PLZ',$FORM->TextFeld['PLZ']->generiereTextFeld());


         // Ort
         $FORM->TextFeld('Ort',$this->_LOCALS['Ort'],'eingabe_feld','width: 220px; margin-left: 14px; margin-bottom: 1px;','0');
         $FORM->TextFeld['Ort']->setzeValidator('Eintragen');
         $FORM->TextFeld['Ort']->setzeZusatzTag('maxlength','40');
         $Template->setPlaceHolder('Ort',$FORM->TextFeld['Ort']->generiereTextFeld());


         // Tel
         $FORM->TextFeld('Telefon',$this->_LOCALS['Telefon'],'eingabe_feld','width: 280px; margin-left: 73px; margin-bottom: 1px;','0');
         $FORM->TextFeld['Telefon']->setzeValidator('Eintragen','Telefon');
         $FORM->TextFeld['Telefon']->setzeZusatzTag('maxlength','30');
         $Template->setPlaceHolder('Tel',$FORM->TextFeld['Telefon']->generiereTextFeld());


         // Fax
         $FORM->TextFeld('Fax',$this->_LOCALS['Fax'],'eingabe_feld','width: 280px; margin-left: 95px; margin-bottom: 1px;','0');
         $FORM->TextFeld['Fax']->setzeValidator('Eintragen','Fax');
         $FORM->TextFeld['Fax']->setzeZusatzTag('maxlength','30');
         $Template->setPlaceHolder('Fax',$FORM->TextFeld['Fax']->generiereTextFeld());


         // EMail
         $FORM->TextFeld('EMail',$this->_LOCALS['EMail'],'eingabe_feld','width: 398px;','0');
         //$FORM->TextFeld['EMail']->setzeValidator('Eintragen','EMail');
         $FORM->TextFeld['EMail']->setzeZusatzTag('maxlength','80');
         $Template->setPlaceHolder('EMail',$FORM->TextFeld['EMail']->generiereTextFeld());


         // Anhang
         $Template->setPlaceHolder('Anhang',$FORM->DateiFeld('Anhang',$this->_LOCALS['Anhang'],'eingabe_feld','width: 400px;'));


         // Ausgabe erzeugen
         return $Template->transformTemplate();

       // end function
      }

    // end class
   }
?>