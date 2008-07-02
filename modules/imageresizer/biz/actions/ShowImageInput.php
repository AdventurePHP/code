<?php
   /**
   *  @package modules::imageresizer::biz::actions
   *  @module ShowImageInput
   *
   *  Input-Objekt für den ImageResizer.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 29.10.2007
   */
   class ShowImageInput extends FrontcontrollerInput
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
      function ShowImageInput(){
         $this->__Attributes['Groesse'] = 100;
       // end function
      }

    // end class
   }
?>