<?php
   import('tools::variablen','variablenHandler');
   import('modules::teilnehmerliste::biz','teilnehmerlisteManager');
   import('modules::teilnehmerliste::biz','teilnehmerObjekt');
   import('tools::link','linkHandler');


   /**
   *  @package modules::teilnehmerliste::pres
   *  @module teilnehmer_v2_controller
   *
   *  Implementiert den DocumentController für das Template 'teilnehmer.html'.<br />
   *  Generiert sowohl Druck- als auch Anzeige-Ausgabe.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 2006<br />
   *  Version 0.2, 11.03.2006<br />
   *  Version 0.3, 17.03.2007 (Verwendung für Print und Ausgabe implementiert)<br />
   *  Version 0.4, 28.10.2007 (Umstellung auf FrontController für Druckausgabe, Konstruktur bereinigt)<br />
   */
   class teilnehmer_v2_controller extends baseController
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen.
      */
      var $_LOCALS;


      function teilnehmer_v2_controller(){
      }


      /**
      *  @module transformContent()
      *  @public
      *
      *  Generiert das Auswahlmenü für die Region.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 2006<br />
      *  Version 0.2, 11.03.2006<br />
      *  Version 0.3, 17.03.2007 (Methode in transformContent() umbenannt)<br />
      *  Version 0.4, 17.03.2007 (Verwendung für Print und Ausgabe implementiert)<br />
      *  Version 0.5, 28.10.2007 (Umstellung auf FrontController für Druckausgabe)<br />
      */
      function transformContent(){

         // Lokal verwendete Variablen registrieren
         if($this->__Document->getAttribute('Bezirk') != null){
            $this->_LOCALS = variablenHandler::registerLocal(array(
                                                                   'Bezirk' => $this->__Document->getAttribute('Bezirk'),
                                                                   'Seite'
                                                                  )
                                                            );

          // end if
         }
         else{
            $this->_LOCALS = variablenHandler::registerLocal(array(
                                                                   'Bezirk' => 'uf',
                                                                   'Seite'
                                                                  )
                                                            );

          // end else
         }


         // Referenz auf Template holen
         if(isset($this->__Attributes['Print']) && $this->__Attributes['Print'] == 'true'){
            $Template__TeilnehmerListe = &$this->__getTemplate('DruckAnsicht');
          // end if
         }
         else{
            $Template__TeilnehmerListe = &$this->__getTemplate('TeilnehmerListe');
          // end else
         }


         // Bezirk einsetzen
         if($this->_LOCALS['Bezirk'] == 'uf'){
            $Bezirk = 'Unterfranken';
          // end if
         }
         if($this->_LOCALS['Bezirk'] == 'mf'){
            $Bezirk = 'Mittelfranken';
          // end if
         }
         $Template__TeilnehmerListe->setPlaceHolder('Bezirk',$Bezirk);


         // Menü einsetzen
         if($this->__templatePlaceholderExists($Template__TeilnehmerListe,'Menu')){
            $Template__TeilnehmerListe->setPlaceHolder('Menu',$this->__generiereMenu());
          // end if
         }


         // Drucken einsetzen
         if($this->__templatePlaceholderExists($Template__TeilnehmerListe,'Drucken')){
            $Template__TeilnehmerListe->setPlaceHolder('Drucken',$this->__generiereDruckMenu());
          // end if
         }


         // Header
         $Template__TeilnehmerListe->setPlaceHolder('Header',$this->__generiereTabellenHeader());


         // Liste einsetzen
         $Template__TeilnehmerListe->setPlaceHolder('Liste',$this->__generiereListe());


         // Ausgabe in Document einsetzen
         $this->setPlaceHolder('Content',$Template__TeilnehmerListe->transformTemplate());

       // end function
      }


      /**
      *  @modules __generiereTabellenHeader()
      *  @private
      *
      *  Generiert den Tabellen-Header der Anzeige.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.03.2006<br />
      */
      function __generiereTabellenHeader(){
         $Template__Header = &$this->__getTemplate('Header');
         return $Template__Header->transformTemplate();
       // end function
      }


      /**
      *  @modules __generiereDruckMenu()
      *  @private
      *
      *  Generiert das Auswahlmenü für die Region.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.03.2006<br />
      *  Version 0.2, 28.10.2007 (Drucken-Link auf FrontController-Action umgestellt)<br />
      */
      function __generiereDruckMenu(){

         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');

         $Template__DruckenMenu = &$this->__getTemplate('DruckenMenu');
         $Template__DruckenMenu->setPlaceHolder('BildPfad',$URLBasePath.'/bild.php?Bild=drucker.gif');
         $Template__DruckenMenu->setPlaceHolder('ListeDruckenLink',$URLBasePath.'/modules_teilnehmerliste-action/printParticipants/Bezirk/'.$this->_LOCALS['Bezirk']);
         return $Template__DruckenMenu->transformTemplate();

       // end function
      }


      /**
      *  @module __generiereMenu()
      *  @private
      *
      *  Generiert das Auswahlmenü für die Region.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.03.2006<br />
      *  Version 0.2, 19.08.2006 (Formular-URL wurde umgestellt, Formular wird jetzt mit POST abgesendet um URL-Rewriting sauber zu unterstützen)<br />
      *  Version 0.3, 30.08.2006 (Menü auf HTML statt FORM umgestellt, damit Caching unterstützt wird)<br />
      */
      function __generiereMenu(){

         // Referenz auf Template holen
         $Template__Menu = &$this->__getTemplate('Menu');

         // LinkUfr
         $Template__Menu->setPlaceHolder('LinkUfr',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Seite' => $this->_LOCALS['Seite'],'Bezirk' => 'uf')));

         // LinkMfr
         $Template__Menu->setPlaceHolder('LinkMfr',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Seite' => $this->_LOCALS['Seite'],'Bezirk' => 'mf')));

         // NameUfr + NameMfr
         if($this->_LOCALS['Bezirk'] == 'uf'){
            $Template__Menu->setPlaceHolder('NameUfr','<u><strong>Unterfranken</strong></u>');
            $Template__Menu->setPlaceHolder('NameMfr','Mittelfranken');
          // end if
         }
         else{
            $Template__Menu->setPlaceHolder('NameUfr','Unterfranken');
            $Template__Menu->setPlaceHolder('NameMfr','<u><strong>Mittelfranken</strong></u>');
          // end else
         }

         return $Template__Menu->transformTemplate();

       // end function
      }


      /**
      *  @module __generiereListe()
      *  @private
      *
      *  Generiert eine Teilnehmer-Liste.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.03.2006<br />
      */
      function __generiereListe(){

         // Referenz auf Template holen
         $Template__Liste = &$this->__getTemplate('Liste');


         // Liste laden
         $TLMgr = &$this->__getServiceObject('modules::teilnehmerliste::biz','teilnehmerlisteManager');
         $Liste = $TLMgr->ladeTeilnehmerListe($this->_LOCALS['Bezirk']);


         // Liste zusammensetzen
         $Puffer = (string)'';

         for($i = 0; $i < count($Liste); $i++){

            $Template__Liste->setPlaceHolder('Inhaber',$Liste[$i]->zeigeAttribut('Betrieb'));
            $Template__Liste->setPlaceHolder('Strasse',$Liste[$i]->zeigeAttribut('Strasse'));
            $Template__Liste->setPlaceHolder('Ort',$Liste[$i]->zeigeAttribut('Ort'));
            $Template__Liste->setPlaceHolder('Tel',$Liste[$i]->zeigeAttribut('Telefon'));
            $Template__Liste->setPlaceHolder('Fax',$Liste[$i]->zeigeAttribut('Fax'));
            $Template__Liste->setPlaceHolder('Mobil',$Liste[$i]->zeigeAttribut('Mobil'));
            $Template__Liste->setPlaceHolder('EMail',$Liste[$i]->zeigeAttribut('Email'));
            $Template__Liste->setPlaceHolder('Web',$Liste[$i]->zeigeAttribut('Homepage'));

            $Puffer .= $Template__Liste->transformTemplate();

          // end for
         }

         return $Puffer;

       // end function
      }

    // end class
   }
?>