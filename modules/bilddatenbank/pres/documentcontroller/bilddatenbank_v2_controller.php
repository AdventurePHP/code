<?php
   import('modules::bilddatenbank::biz','BildDatenBankManager');
   import('modules::bilddatenbank::biz','BildDaten');
   import('tools::variablen','variablenHandler');
   import('tools::link','linkHandler');


   /**
   *  @package modules::bilddatenbank::pres::documentcontroller
   *  @module bilddatenbank_v2_controller
   *
   *  Implementiert den DocumentController des Templates 'bilddatenbank.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 17.03.2007 (Implementierung nach PC V2)<br />
   */
   class bilddatenbank_v2_controller extends baseController
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen vor.
      */
      var $_LOCALS;


      /**
      *  @module bilddatenbank_v2_controller()
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function bilddatenbank_v2_controller(){
         $this->_LOCALS = variablenHandler::registerLocal(array('Kriterium' => 'Kultur','Buchstabe' => 'a', 'Breite', 'Hoehe'));
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
      *  Version 0.1, 29.05.2005<br />
      *  Version 0.2, 04.12.2005<br />
      *  Version 0.3, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function transformContent(){
         $this->setPlaceHolder('Content',$this->__erzeugePictogrammAnsicht());
       // end function
      }


      /**
      *  @module __erzeugePictogrammAnsicht()
      *  @private
      *
      *  Erzeugt die Pictogramm-Ansicht der Schadbilder.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.05.2005<br />
      *  Version 0.2, 04.12.2005<br />
      *  Version 0.3, 16.08.2006 (Anpassung auf Grund von Änderungen in der Business-Schicht (Einführung der neuen Pager-Komponente))<br />
      *  Version 0.4, 03.09.2006 (Pager wird nun nur noch angezeigt, wenn Einträge vorhanden sind)<br />
      *  Version 0.5, 17.03.2007 (Implementierung nach PC V2)<br />
      *  Version 0.6, 29.10.2007 (FrontController-Implementierung für Bild-Anzeige eingeführt)<br />
      */
      function __erzeugePictogrammAnsicht(){

         // Daten holen
         $bdbM = &$this->__getServiceObject('modules::bilddatenbank::biz','BildDatenBankManager');
         $Daten = $bdbM->loadPictures();


         // Ausgabe-Template
         $Template__Ausgabe = &$this->__getTemplate('Ausgabe');
         $Template__Ausgabe->setPlaceHolder('Menu',$this->__erzeugeOrdnungsMenu());
         $Template__Ausgabe->setPlaceHolder('ABCMenu',$this->__erzeugeABCMenu());


         // Pager
         if(count($Daten) > 0){
            $Template__Ausgabe->setPlaceHolder('Pager',$bdbM->getPagerOutput());
          // end if
         }
         else{
            $Template__Ausgabe->setPlaceHolder('Pager','<br /><br />');
          // end else
         }


         // Bild-Template
         $Template__Bild = &$this->__getTemplate('Bild');


         // KulturText
         $Template__KulturText = &$this->__getTemplate('KulturText');


         // UrsacheText
         $Template__UrsacheText = &$this->__getTemplate('UrsacheText');


         // Bilder-Puffer
         $Bild = (string)'';

         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');

         for($i = 0; $i < count($Daten); $i++){

            // BildanzeigeLink
            $Template__Bild->setPlaceHolder('BildanzeigeLink',$URLBasePath.'/~/modules_bilddatenbank-action/showFullImage/Bild/'.$Daten[$i]->zeigeIndex().'/Breite/'.$Daten[$i]->zeigeAnsichtFensterBreite().'/Hoehe/'.$Daten[$i]->zeigeAnsichtFensterHoehe());

            // Breite und Hoehe
            $Template__Bild->setPlaceHolder('Breite',$Daten[$i]->zeigeAnsichtFensterBreite());
            $Template__Bild->setPlaceHolder('Hoehe',$Daten[$i]->zeigeAnsichtFensterHoehe());

            // BildQuelle
            $Template__Bild->setPlaceHolder('BildQuelle',$URLBasePath.'/~/modules_imageresizer-action/showImage/Bild/'.basename($Daten[$i]->zeigePictogramm()).'/Pfad/SCHADBILDER_MEDIA_PATH');

            // AltText
            $Template__Bild->setPlaceHolder('AltText',"Ursache: ".$Daten[$i]->zeigeUrsache()."\nKultur: ".$Daten[$i]->zeigeKultur());


            // Text
            $Text = (string)'';
            if($this->_LOCALS['Kriterium'] == 'Ursache'){
               $Template__UrsacheText->setPlaceHolder('Kultur',$Daten[$i]->zeigeKultur());
               $Template__UrsacheText->setPlaceHolder('Ursache',$Daten[$i]->zeigeUrsache());
               $Text = $Template__UrsacheText->transformTemplate();
             // end if
            }
            else{
               $Template__KulturText->setPlaceHolder('Kultur',$Daten[$i]->zeigeKultur());
               $Template__KulturText->setPlaceHolder('Ursache',$Daten[$i]->zeigeUrsache());
               $Text = $Template__KulturText->transformTemplate();
             // end else
            }
            $Template__Bild->setPlaceHolder('Text',$Text);


            $Bild .= $Template__Bild->transformTemplate()."\n";

          // end for
         }


         // Bilder in Ausgabe einsetzen
         if(empty($Bild)){
            $Template__Meldung = &$this->__getTemplate('Meldung');
            $Template__Meldung->setPlaceHolder('Buchstabe',ucfirst($this->_LOCALS['Buchstabe']));
            $Template__Ausgabe->setPlaceHolder('Ausgabe',$Template__Meldung->transformTemplate());
          // end if
         }
         else{
            $Template__Ausgabe->setPlaceHolder('Ausgabe',$Bild);
          // end else
         }

         return $Template__Ausgabe->transformTemplate();

       // end function
      }


      /**
      *  @module __erzeugeABCMenu()
      *  @private
      *
      *  Erzeugt das ABC-Menü für die Sortierung nach Alphabet.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.05.2005<br />
      *  Version 0.2, 04.12.2005<br />
      *  Version 0.3, 16.08.2006 (Anpassung auf Grund von Änderungen in der Business-Schicht (Einführung der neuen Pager-Komponente))<br />
      *  Version 0.4, 08.10.2006 (Es werden jetzt auch große Buchstaben in der URL akzeptiert)<br />
      *  Version 0.5, 15.11.2006 (Anpassung, dass der Link-Generierung damit der Startpunkt beim Wechsel eines Buchstabens die Anzeige passt)<br />
      *  Version 0.6, 17.03.2007 (Implementierung nach PC V2)<br />
      *  Version 0.7, 17.03.2007 (PagerURLParameter werden nun vom Pager gezogen)<br />
      */
      function __erzeugeABCMenu(){

         $ABC = array(
                      'a',
                      'b',
                      'c',
                      'd',
                      'e',
                      'f',
                      'g',
                      'h',
                      'i',
                      'j',
                      'k',
                      'l',
                      'm',
                      'n',
                      'o',
                      'p',
                      'q',
                      'r',
                      's',
                      't',
                      'u',
                      'v',
                      'w',
                      'x',
                      'y',
                      'z'
                      );

         // ABCMenu-Template
         $Template__ABCMenu = &$this->__getTemplate('ABCMenu');


         // Parameter vom Pager beziehen
         $bdbM = &$this->__getServiceObject('modules::bilddatenbank::biz','BildDatenBankManager');
         $StartName = $bdbM->getPagerStartName();
         $CountName = $bdbM->getPagerCountName();


         // Menu erzeugen
         $Menu = (string)'';

         for($i = 0; $i < count($ABC); $i++){

            // Link (pgrStart und pgrAnz müssen gelöscht werden, da die Anzeige bei Buchstaben-Wechsel sonst Probleme macht)
            $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array($StartName => '',$CountName => ''));

            if(isset($_REQUEST[$StartName])){
               $Link = linkHandler::generateLink($Link,array('Kriterium' => $this->_LOCALS['Kriterium'],'Buchstabe' => $ABC[$i],$StartName => '0'));
             // end if
            }
            else{
               $Link = linkHandler::generateLink($Link,array('Kriterium' => $this->_LOCALS['Kriterium'],'Buchstabe' => $ABC[$i]));
             // end else
            }

            $Link = linkHandler::generateLink($Link,array('Kriterium' => $this->_LOCALS['Kriterium'],'Buchstabe' => $ABC[$i],));
            $Template__ABCMenu->setPlaceHolder('Link',$Link);

            // Buchstabe
            $Template__ABCMenu->setPlaceHolder('Buchstabe',$ABC[$i]);

            // LinkText
            $LinkText = (string)'';
            if(strtolower($this->_LOCALS['Buchstabe']) == $ABC[$i]){
               $LinkText = "<font style=\"font-size: 18px; font-weight: bold;\">".ucfirst($ABC[$i])."</font>";
             // end if
            }
            else{
               $LinkText = ucfirst($ABC[$i]);
             // end else
            }
            $Template__ABCMenu->setPlaceHolder('LinkText',$LinkText);

            $Menu .= $Template__ABCMenu->transformTemplate()."\n";

          // end for
         }

         return $Menu;

       // end function
      }


      /**
      *  @module __erzeugeOrdnungsMenu()
      *  @private
      *
      *  Erzeugt das Sortier-Menü für die Sortierung nach 'Kultur' oder 'Ursache'.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.05.2005<br />
      *  Version 0.2, 04.12.2005<br />
      *  Version 0.3, 16.08.2006 (Anpassung auf Grund von Änderungen in der Business-Schicht (Einführung der neuen Pager-Komponente))<br />
      *  Version 0.4, 03.09.2006 (Sortier-Kriterium auf GET umgestellt; Ausgabe-Design geändert)<br />
      *  Version 0.5, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function __erzeugeOrdnungsMenu(){

         // Menu-Template
         $Template__Menu = &$this->__getTemplate('Menu');

         // Kultur_Link + Ursache_Link
         $Template__Menu->setPlaceHolder('Kultur_Link',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Kriterium' => 'Kultur')));
         $Template__Menu->setPlaceHolder('Ursache_Link',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Kriterium' => 'Ursache')));


         // Kultur + Ursache
         if($this->_LOCALS['Kriterium'] == 'Kultur'){
            $Template__Menu->setPlaceHolder('Kultur','<u><strong>Kultur</strong></u>');
            $Template__Menu->setPlaceHolder('Ursache','Ursache');
          // end if
         }
         else{
            $Template__Menu->setPlaceHolder('Kultur','Kultur');
            $Template__Menu->setPlaceHolder('Ursache','<u><strong>Ursache</strong></u>');
          // end else
         }

         return $Template__Menu->transformTemplate();

       // end function
      }

    // end class
   }
?>