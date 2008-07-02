<?php
   /**
   *  @package modules::bilddatenbank::biz::actions
   *  @module DisplayImageAction
   *
   *  Implementiert die FrontControllerAction zur Ausgabe der Fullsize-Bilder.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.10.2007
   */
   class DisplayImageAction extends AbstractFrontcontrollerAction
   {

      function DisplayImageAction(){
      }


      /**
      *  @module run()
      *  @public
      *
      *  Implementiert die abstrakte Methode run().<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.10.2007
      */
      function run(){

         // PageController-Seite erzeugen
         $Page = new Page('BildAnzeige',false);

         // Seite konfigurieren
         $Page->set('Context',$this->__Context);
         $Page->loadDesign('modules::bilddatenbank::pres::templates','bildanzeige');

         // Konfigurationsparameter setzen
         $Document = &$Page->getByReference('Document');
         $Document->setAttribute('Bild',$this->__Input->getAttribute('Bild'));
         $Document->setAttribute('Breite',$this->__Input->getAttribute('Breite'));
         $Document->setAttribute('Hoehe',$this->__Input->getAttribute('Hoehe'));

         // Ausgabe erzeugen
         echo $Page->transform();

         // Aussteigen
         exit();

       // end function
      }

    // end class
   }
?>