<?php
   import('modules::bilddatenbank::biz','BildDatenBankManager');
   import('modules::bilddatenbank::biz','BildDaten');


   /**
   *  @package modules::bilddatenbank::pres::documentcontroller
   *  @module bildanzeige_v2_controller
   *
   *  Implementiert den DocumentController des Templates 'bildanzeige.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 17.03.2007 (Implementierung nach PC V2)<br />
   *  Version 0.2, 28.10.2007 (Implementierung mit FrontController-Ausgabe)<br />
   */
   class bildanzeige_v2_controller extends baseController
   {

      function bildanzeige_v2_controller(){
      }


      /**
      *  @module transformContent()
      *  @public
      *
      *  Implementiert die abstrakte Methode des baseControllers.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.03.2007 (Implementierung nach PC V2)<br />
      *  Version 0.2, 27.05.2007 (Manager wird als ServiceObject erzeugt)<br />
      *  Version 0.3, 28.10.2007 (Implementierung mit FrontController-Ausgabe)<br />
      */
      function transformContent(){

         // Daten aus Document holen
         $BildID = $this->__Document->getAttribute('Bild');
         $Breite = $this->__Document->getAttribute('Breite');
         $Hoehe = $this->__Document->getAttribute('Hoehe');


         // Daten holen
         $bdbM = &$this->__getServiceObject('modules::bilddatenbank::biz','BildDatenBankManager');
         $Bild = $bdbM->loadPicture($BildID);


         // Titel
         $this->setPlaceHolder('Titel','Kultur: '.$Bild->zeigeKultur().', Ursache: '.$Bild->zeigeUrsache());


         // BildQuelle
         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');
         $Link = $URLBasePath.'/bild.php?Bild='.basename($Bild->zeigeBild()).'&Pfad=SCHADBILDER_MEDIA_PATH';
         $this->setPlaceHolder('BildQuelle',$Link);


         // Breite und Höhe des Fensters
         $this->setPlaceHolder('Breite',intval($Breite) + 10);  // Es wird jeweils noch ein Offset dazu addiert um die
         $this->setPlaceHolder('Hoehe',intval($Hoehe) + 50);    // Ränder beim Anzeigen der Bilder einhalten zu können!

       // end function
      }

    // end class
   }
?>