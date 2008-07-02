<?php
   /**
   *  Klasse BildObjekt
   *  Implementiert das Domänenobjekt BildObjekt
   *
   *  Christian Schäfer
   *  Version 0.1, 16.05.2005
   */
   class BildObjekt
   {
      var $Name;
      var $Text;
      var $Thema;
      var $Pictogramm;
      var $Bild;
      var $GBIndex;


      function BildObjekt(){
         $this->Name = (string)'';
         $this->Text = (string)'';
         $this->Thema = (string)'';
         $this->Pictogramm = (string)'';
         $this->Bild = (string)'';
         $this->GBIndex = (string)'';
       // end function
      }

      function setzeName($Name){
         $this->Name = $Name;
      }
      function setzeText($Text){
         $this->Text = $Text;
      }
      function setzeThema($Thema){
         $this->Thema = $Thema;
      }
      function setzePictogramm($Pictogramm){
         $this->Pictogramm = $Pictogramm;
      }
      function setzeBild($Bild){
         $this->Bild = $Bild;
      }
      function setzeGBIndex($GBIndex){
         $this->GBIndex = $GBIndex;
      }

      function zeigeName(){
         return $this->Name;
      }
      function zeigeText(){
         return $this->Text;
      }
      function zeigeThema(){
         return $this->Thema;
      }
      function zeigePictogramm(){
         return $this->Pictogramm;
      }
      function zeigeBild(){
         return $this->Bild;
      }
      function zeigeGBIndex(){
         return $this->GBIndex;
      }

    // end class
   }
?>
