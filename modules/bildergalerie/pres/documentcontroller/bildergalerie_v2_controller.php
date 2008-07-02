<?php
   import('modules::bildergalerie::biz','BilderGalerieManager');
   import('modules::bildergalerie::biz','BildObjekt');
   import('modules::bildergalerie::biz','ThemaObjekt');
   import('modules::bildergalerie::biz','GalerieObjekt');
   import('tools::variablen','variablenHandler');
   import('tools::link','linkHandler');
   import('tools::string','bbCodeParser');


   /**
   *  @package modules::bildergalerie::pres
   *  @module bildergalerie_v2_controller
   *
   *  Implementiert den DocumentController für das Template 'bildergalerie.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 13.05.2005<br />
   *  Version 0.2, 15.05.2005<br />
   *  Version 0.3, 16.05.2005<br />
   *  Version 0.4, 09.11.2005<br />
   *  Version 0.5, 04.12.2005 (Parameter Galerie wird nicht mehr in der URI angezeigt)<br />
   *  Version 0.6, 17.03.2007 (Implementierung nach PC V2)<br />
   */
   class bildergalerie_v2_controller extends baseController
   {

      var $_LOCALS;


      /**
      *  @module bildergalerie_v2_controller()
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.05.2005<br />
      *  Version 0.2, 15.05.2005<br />
      *  Version 0.3, 16.05.2005<br />
      *  Version 0.4, 09.11.2005<br />
      *  Version 0.5, 12.03.2006 (Name der Galerie wird als Überschrift, die Beschreibung als Text angezeigt)<br />
      *  Version 0.6, 01.09.2006 (Diverse Anpassungen für RewriteURL)<br />
      *  Version 0.7, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function bildergalerie_v2_controller(){
         $this->_LOCALS = variablenHandler::registerLocal(array('Galerie','Ansicht' => 'Uebersicht','GTIndex','GBIndex'));
       // end function
      }


      /**
      *  @module transformContent()
      *  @public
      *
      *  Implementiert die abstrakte Methode transformContent des baseControllers.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.05.2005<br />
      *  Version 0.2, 15.05.2005<br />
      *  Version 0.3, 16.05.2005<br />
      *  Version 0.4, 09.11.2005<br />
      *  Version 0.5, 12.03.2006 (Name der Galerie wird als Überschrift, die Beschreibung als Text angezeigt)<br />
      *  Version 0.6, 01.09.2006 (Diverse Anpassungen für RewriteURL)<br />
      *  Version 0.7, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function transformContent(){

         // Galerie-Nummer setzen, falls nicht in der URL enthalten
         if(empty($this->_LOCALS['Galerie'])){
            $this->_LOCALS['Galerie'] = $this->__Attributes['ConfigParam'];
          // end if
         }


         // BilderGalerie-Template
         $Template__BilderGalerie = &$this->__getTemplate('BilderGalerie');


         // Galerie-Baum laden
         $M = &$this->__getServiceObject('modules::bildergalerie::biz','BilderGalerieManager');
         $Galerie = $M->ladeGalerie($this->_LOCALS['Galerie']);


         // Ausgabe-Puffer
         $ContentPuffer = (string)'';


         // Übersicht
         if($this->_LOCALS['Ansicht'] == 'Uebersicht'){
            $ContentPuffer = $this->__erzeugeUebersicht($Galerie);
          // end if
         }

         // Thema
         if($this->_LOCALS['Ansicht'] == 'Thema'){
            $ContentPuffer = $this->__erzeugeThemaAnsicht($Galerie);
          // end if
         }


         // Details
         if($this->_LOCALS['Ansicht'] == 'Details'){
            $ContentPuffer = $this->__erzeugeDetailAnsicht($Galerie);
          // end if
         }


         // Ausgabe erzeugen (Name, Beschreibung und View-Inhalt einsetzen)
         $Template__BilderGalerie->setPlaceHolder('Name',$Galerie->zeigeName());

         $bbCP = &$this->__getServiceObject('tools::string','bbCodeParser');
         $Template__BilderGalerie->setPlaceHolder('Beschreibung',$bbCP->parseText($Galerie->zeigeBeschreibung()));

         $Template__BilderGalerie->setPlaceHolder('Inhalt',$ContentPuffer);

         $this->setPlaceHolder('Content',$Template__BilderGalerie->transformTemplate());

       // end function
      }


      /**
      *  @module __erzeugeUebersicht()
      *  @private
      *
      *  Erzeugt die Übersicht.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 13.05.2005<br />
      *  Version 0.2, 15.05.2005<br />
      *  Version 0.3, 16.05.2005<br />
      *  Version 0.4, 09.11.2005<br />
      *  Version 0.5, 11.03.2006<br />
      *  Version 0.6, 12.03.2006<br />
      *  Version 0.7, 01.09.2006 (Link-Generierung angepasst)<br />
      *  Version 0.8, 17.03.2007 (Implementierung nach PC V2)<br />
      *  Version 0.9, 29.10.2007 (Bildausgabe auf FC-Action umgestellt)<br />
      *  Version 1.0, 04.01.2007 (Bildausgabe für nicht URL Rewrite Betrieb geändert)<br />
      */
      function __erzeugeUebersicht(&$Galerie){

         // Uebersicht-Template
         $Template__Uebersicht = &$this->__getTemplate('Uebersicht');

         // Ordner-Template
         $Template__Ordner = &$this->__getTemplate('Ordner');

         // Ordner-Bild
         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');
         $URLRewriting = $Reg->retrieve('apf::core','URLRewriting');
         //$Template__Ordner->setPlaceHolder('Ordner',$URLBasePath.'/bild.php?Bild=ordner_xp.gif');
         if($URLRewriting == true){
            $Template__Ordner->setPlaceHolder('Ordner',$URLBasePath.'/~/modules_imageresizer-action/showImage/Bild/ordner_xp.gif/Pfad/MEDIA_PATH');
          // end if
         }
         else{
            $Template__Ordner->setPlaceHolder('Ordner',$URLBasePath.'/?modules_imageresizer-action:showImage=Bild:ordner_xp.gif|Pfad:MEDIA_PATH');
          // end else
         }

         // Themen als Referenz zu den Themen der Galerie extrahieren
         $Themen = $Galerie->zeigeThemen();

         $Puffer = (string)'';

         // Ordner-Struktur der Themen in der aktuellen Galerie anzeigen
         for($i = 0; $i < count($Themen); $i++){

            // Link
            $Template__Ordner->setPlaceHolder('Link',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Galerie' => '','Ansicht' => 'Thema','GTIndex' => $Themen[$i]->zeigeGTIndex())));

            // Name
            $Template__Ordner->setPlaceHolder('Name',$Themen[$i]->zeigeName());

            // Ausgabe puffern
            $Puffer .= $Template__Ordner->transformTemplate();

          // end for
         }

         // Ordner in Übersicht einsetzen
         $Template__Uebersicht->setPlaceHolder('Ordner',$Puffer);
         return $Template__Uebersicht->transformTemplate();

       // end function
      }


      /**
      *  @module __erzeugeThemaAnsicht()
      *  @private
      *
      *  Erzeugt die Thema-Übersicht.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 13.05.2005<br />
      *  Version 0.2, 15.05.2005<br />
      *  Version 0.3, 16.05.2005<br />
      *  Version 0.4, 09.11.2005<br />
      *  Version 0.5, 11.03.2006<br />
      *  Version 0.6, 12.03.2006<br />
      *  Version 0.7, 01.09.2006 (Link-Generierung angepasst)<br />
      *  Version 0.8, 17.03.2007 (Implementierung nach PC V2)<br />
      *  Version 0.9, 29.10.2007 (Bildausgabe auf FC-Action umgestellt)<br />
      *  Version 1.0, 04.01.2007 (Bildausgabe für nicht URL Rewrite Betrieb geändert)<br />
      */
      function __erzeugeThemaAnsicht(&$Galerie){

         // Thema-Template
         $Template__Thema = &$this->__getTemplate('Thema');


         // Bild-Template
         $Template__Bild = &$this->__getTemplate('Bild');


         // Bilder zum Thema laden
         $M = &$this->__getServiceObject('modules::bildergalerie::biz','BilderGalerieManager');
         $Themen = $Galerie->zeigeThemen();
         $AktuellesThema = $Themen[$M->zeigeThemenOffsetZuGTIndex($Galerie,$this->_LOCALS['GTIndex'])];
         $Bilder = $AktuellesThema->zeigeBilder();


         // Galerie im Thema-Template setzen
         $Template__Thema->setPlaceHolder('GalerieLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Galerie' => '','Ansicht' => 'Uebersicht','GTIndex' => '','GBIndex' => '')));
         $Template__Thema->setPlaceHolder('GalerieName',$Galerie->zeigeName());


         // Thema in Thema-Tamplete setzen
         $Template__Thema->setPlaceHolder('ThemaName',$AktuellesThema->zeigeName());


         // Bilder des Themas selektieren
         $Puffer = (string)'';

         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');
         $URLRewriting = $Reg->retrieve('apf::core','URLRewriting');

         for($i = 0; $i < count($Bilder); $i++){

            $Template__Bild->setPlaceHolder('BildLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Galerie' => '','Ansicht' => 'Details','GTIndex' => $this->_LOCALS['GTIndex'],'GBIndex' => $Bilder[$i]->zeigeGBIndex())));

            //$Template__Bild->setPlaceHolder('BildPfad',$URLBasePath.'/bild.php?Bild='.$Bilder[$i]->zeigePictogramm().'&Pfad=BILDERGALERIE_MEDIA_PATH');
            if($URLRewriting == true){
               $Template__Bild->setPlaceHolder('BildPfad',$URLBasePath.'/~/modules_imageresizer-action/showImage/Bild/'.$Bilder[$i]->zeigePictogramm().'/Pfad/BILDERGALERIE_MEDIA_PATH');
             // end else
            }
            else{
               $Template__Bild->setPlaceHolder('BildPfad',$URLBasePath.'/?modules_imageresizer-action:showImage=Bild:'.$Bilder[$i]->zeigePictogramm().'|Pfad:BILDERGALERIE_MEDIA_PATH');
             // end else
            }


            $Template__Bild->setPlaceHolder('BildName',$Bilder[$i]->zeigeName());

            $Puffer .= $Template__Bild->transformTemplate();

          // end for
         }

         // Bilder in Thema einsetzen
         $Template__Thema->setPlaceHolder('Bilder',$Puffer);
         return $Template__Thema->transformTemplate();

       // end function
      }


      /**
      *  @module __erzeugeDetailAnsicht()
      *  @private
      *
      *  Erzeugt die Bild-Detail-Übersicht.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 13.05.2005<br />
      *  Version 0.2, 15.05.2005<br />
      *  Version 0.3, 16.05.2005<br />
      *  Version 0.4, 09.11.2005<br />
      *  Version 0.5, 11.03.2006<br />
      *  Version 0.6, 12.03.2006<br />
      *  Version 0.7, 01.09.2006 (Link-Generierung angepasst)<br />
      *  Version 0.8, 08.10.2006 (Bug behoben, dass Link zur Übersicht der Galerie wurde fehlerhaft erzeugt wurde)<br />
      *  Version 0.9, 17.03.2007 (Implementierung nach PC V2)<br />
      *  Version 1.0, 29.10.2007 (Bildausgabe auf FC-Action umgestellt)<br />
      *  Version 1.1, 04.01.2007 (Bildausgabe für nicht URL Rewrite Betrieb geändert)<br />
      */
      function __erzeugeDetailAnsicht(&$Galerie){

         // Details-Template
         $Template__Details = &$this->__getTemplate('Details');

         // PagerVor-Template
         $Template__PagerVor = &$this->__getTemplate('PagerVor');

         // PagerZurueck-Template
         $Template__PagerZurueck = &$this->__getTemplate('PagerZurueck');

         // PagerAnfang-Template
         $Template__PagerAnfang = &$this->__getTemplate('PagerAnfang');

         // PagerEnde-Template
         $Template__PagerEnde = &$this->__getTemplate('PagerEnde');

         // PagerUebersicht-Template
         $Template__PagerUebersicht= &$this->__getTemplate('PagerUebersicht');


         // Bild zu Bildern laden
         $M = &$this->__getServiceObject('modules::bildergalerie::biz','BilderGalerieManager');
         $Themen = $Galerie->zeigeThemen();
         $AktuellesThema = &$Themen[$M->zeigeThemenOffsetZuGTIndex($Galerie,$this->_LOCALS['GTIndex'])];
         $Bilder = $AktuellesThema->zeigeBilder();
         $AktuellesBild = &$Bilder[$M->zeigeBildOffsetZuGBIndex($AktuellesThema,$this->_LOCALS['GBIndex'])];
         $AktuellerIndex = $M->zeigeBildOffsetZuGBIndex($AktuellesThema,$this->_LOCALS['GBIndex']);

         //
         //   Details des ausgewählten Bildes zeigen und die Möglichkeiten
         //    -  < zurück
         //    - << Anfang
         //    - vor >
         //    - Ende >>
         //    - < Übersicht >
         //

         // Galerie im Detail-Template setzen
         $Template__Details->setPlaceHolder('GalerieLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Galerie' => '','Ansicht' => 'Uebersicht','GTIndex' => '','GBIndex' => '')));
         $Template__Details->setPlaceHolder('GalerieName',$GalerieName = $Galerie->zeigeName());

         // Thema in Detail-Tamplete setzen
         $Template__Details->setPlaceHolder('ThemaLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Galerie' => '','Ansicht' => 'Thema','GTIndex' => $this->_LOCALS['GTIndex'],'GBIndex' => '')));
         $Template__Details->setPlaceHolder('ThemaName',$ThemenName = $AktuellesThema->zeigeName());

         // Bild in Detail-Template setzen
         $Name = $AktuellesBild->zeigeName();
         if(empty($Name)){
            $DisplayName = $AktuellesBild->zeigeBild();
          // end if
         }
         else{
            $DisplayName = $AktuellesBild->zeigeName();
          // end else
         }
         $Template__Details->setPlaceHolder('BildName',$DisplayName);


         // Bild in Bild-Template einsetzen
         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');
         $URLRewriting = $Reg->retrieve('apf::core','URLRewriting');
         //$Template__Details->setPlaceHolder('GrossBildPfad',$URLBasePath.'/bild.php?Bild='.$AktuellesBild->zeigeBild().'&Pfad=BILDERGALERIE_MEDIA_PATH');
         if($URLRewriting == true){
            $Template__Details->setPlaceHolder('GrossBildPfad',$URLBasePath.'/~/modules_imageresizer-action/showImage/Bild/'.$AktuellesBild->zeigeBild().'/Pfad/BILDERGALERIE_MEDIA_PATH');
          // end if
         }
         else{
            $Template__Details->setPlaceHolder('GrossBildPfad',$URLBasePath.'/?modules_imageresizer-action:showImage=Bild:'.$AktuellesBild->zeigeBild().'|Pfad:BILDERGALERIE_MEDIA_PATH');
          // end else
         }


         // BildText in Bild-Template einsetzen
         $bbCP = &$this->__getServiceObject('tools::string','bbCodeParser');
         $Template__Details->setPlaceHolder('BildText',$bbCP->parseText($AktuellesBild->zeigeText()));


         // PagerVor setzen
         if($AktuellerIndex >= 0 && $AktuellerIndex < (count($Bilder)-1)){
            $Template__PagerVor->setPlaceHolder('PagerVorLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Galerie' => '','Ansicht' => 'Details','GTIndex' => $this->_LOCALS['GTIndex'],'GBIndex' => $Bilder[$AktuellerIndex+1]->zeigeGBIndex())));
            $Template__Details->setPlaceHolder('PagerVor',$Template__PagerVor->transformTemplate());
          // end else
         }


         // PagerZurueck setzen
         if($AktuellerIndex > 0){
            $Template__PagerZurueck->setPlaceHolder('PagerZurueckLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Galerie' => '','Ansicht' => 'Details','GTIndex' => $this->_LOCALS['GTIndex'],'GBIndex' => $Bilder[$AktuellerIndex-1]->zeigeGBIndex())));
            $Template__Details->setPlaceHolder('PagerZurueck',$Template__PagerZurueck->transformTemplate());
          // end else
         }


         // PagerAnfang setzen
         if($AktuellerIndex > 0 && $AktuellerIndex <= (count($Bilder)-1)){
            $Template__PagerAnfang->setPlaceHolder('PagerAnfangLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Galerie' => '','Ansicht' => 'Details','GTIndex' => $this->_LOCALS['GTIndex'],'GBIndex' => $Bilder[0]->zeigeGBIndex())));
            $Template__Details->setPlaceHolder('PagerAnfang',$Template__PagerAnfang->transformTemplate());
          // end else
         }


         // PagerEnde setzen
         if($AktuellerIndex < (count($Bilder)-1)){
            $Template__PagerEnde->setPlaceHolder('PagerEndeLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Galerie' => '','Ansicht' => 'Details','GTIndex' => $this->_LOCALS['GTIndex'],'GBIndex' => $Bilder[count($Bilder)-1]->zeigeGBIndex())));
            $Template__Details->setPlaceHolder('PagerEnde',$Template__PagerEnde->transformTemplate());
          // end else
         }


         // PagerUebersicht setzen
         $Template__PagerUebersicht->setPlaceHolder('PagerUebersichtLink',linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Galerie' => '','Ansicht' => 'Thema','GTIndex' => $this->_LOCALS['GTIndex'],'GBIndex' => '')));
         $Template__Details->setPlaceHolder('PagerUebersicht',$Template__PagerUebersicht->transformTemplate());

         // Ausgabe erzeugen
         return $Template__Details->transformTemplate();

       // end function
      }

    // end class
   }
?>