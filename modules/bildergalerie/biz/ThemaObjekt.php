<?php
   /**
   *  Klasse ThemaObjekt
   *  Implementiert das Domänenobjekt ThemaObjekt
   *
   *  Christian Schäfer
   *  Version 0.1, 14.05.2005
   *  Version 0.2, 16.05.2005
   */
   class ThemaObjekt
   {
      var $Name;
      var $Datum;
      var $Galerie;
      var $GTIndex;

      var $Bilder;


      function ThemaObjekt(){
         $this->Name = (string)'';
         $this->Datum = (string)'';
         $this->Galerie = (string)'';
         $this->GTIndex = (string)'';
         $this->Bilder = array();
       // end function
      }

      function setzeName($Name){
         $this->Name = $Name;
      }
      function setzeDatum($Datum){
         $this->Datum = $Datum;
      }
      function setzeGalerie($Galerie){
         $this->Galerie = $Galerie;
      }
      function setzeGTIndex($GTIndex){
         $this->GTIndex = $GTIndex;
      }
      function setzeBild($BildObjekt){
         $this->Bilder[] = $BildObjekt;
      }
      function haengeBilderBaumEin($BilderBaum){
         $this->Bilder = array();
         $this->Bilder = $BilderBaum;
       // end function
      }

      function zeigeName(){
         return $this->Name;
      }
      function zeigeDatum(){
         return $this->Datum;
      }
      function zeigeGalerie(){
         return $this->Galerie;
      }
      function zeigeGTIndex(){
         return $this->GTIndex;
      }
      function zeigeBilder(){
         return $this->Bilder;
      }

    // end class
   }
?>
