<?php
   /**
   *  Klasse GalerieObjekt
   *  Implementiert das Domänenobjekt GalerieObjekt
   *
   *  Christian Schäfer
   *  Version 0.1, 15.05.2005
   *  Version 0.2, 16.05.2005
   *  Version 0.3, 11.03.2006
   */
   class GalerieObjekt
   {
      var $Name;
      var $Beschreibung;
      var $Datum;
      var $GNIndex;

      var $Themen;


      function GalerieObjekt(){
         $this->Name = (string)'';
         $this->Beschreibung = (string)'';
         $this->Datum = (string)'';
         $this->GNIndex = (string)'';
         $this->Themen = array();
       // end function
      }

      function setzeName($Name){
         $this->Name = $Name;
      }
      function setzeBeschreibung($Beschreibung){
         $this->Beschreibung = $Beschreibung;
      }
      function setzeDatum($Datum){
         $this->Datum = $Datum;
      }
      function setzeGNIndex($GNIndex){
         $this->GNIndex = $GNIndex;
      }
      function setzeThema($ThemaObjekt){
         $this->Themen[] = $ThemaObjekt;
      }
      function haengeThemaBaumEin($ThemaBaum){
         $this->Themen = array();
         $this->Themen = $ThemaBaum;
       // end function
      }

      function zeigeName(){
         return $this->Name;
      }
      function zeigeBeschreibung(){
         return $this->Beschreibung;
      }
      function zeigeDatum(){
         return $this->Datum;
      }
      function zeigeGNIndex(){
         return $this->GNIndex;
      }
      function zeigeThemen(){
         return $this->Themen;
      }

    // end class
   }
?>