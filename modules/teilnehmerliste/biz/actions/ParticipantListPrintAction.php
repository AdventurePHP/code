<?php
   /**
   *  @package modules::teilnehmerliste::biz::actions
   *  @module ParticipantListPrintAction
   *
   *  Implementiert die FrontControllerAction zur Ausgabe der Druckanzeige.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.10.2007
   */
   class ParticipantListPrintAction extends AbstractFrontcontrollerAction
   {

      function ParticipantListPrintAction(){
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
         $Page = new Page('MitgliederDrucken',false);

         // Seite konfigurieren
         $Page->set('Context',$this->__Context);
         $Page->loadDesign('modules::teilnehmerliste::pres::templates','teilnehmer');

         // Parameter für die Druckausgabe setzen
         $Document = &$Page->getByReference('Document');
         $Document->setAttribute('Print','true');
         $Document->setAttribute('Bezirk',$this->__Input->getAttribute('Bezirk'));

         // Ausgabe erzeugen
         echo $Page->transform();

         // Aussteigen
         exit();

       // end function
      }

    // end class
   }
?>