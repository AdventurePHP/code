<?php
   /**
   *  @package modules::teilnehmerliste::biz::actions
   *  @module ParticipantListPrintInput
   *
   *  Input-Objekt für die Druckanzeige-Action.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.10.2007
   */
   class ParticipantListPrintInput extends FrontcontrollerInput
   {

      /**
      *  @module ParticipantListPrintInput()
      *  @public
      *
      *  Konstruktor der Klasse. Inizialisiert die Attribute der Action.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.10.2007
      */
      function ParticipantListPrintInput(){
         $this->__Attributes['Bezirk'] = 'uf';
       // end function
      }

    // end class
   }
?>