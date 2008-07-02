<?php
   /**
   *  Klasse BildDaten
   *  Implementiert ein BildDaten-Objekt
   *
   *  Christian Schäfer
   *  Version 0.1, 29.05.2005
   */
   class BildDaten
   {
      var $Kultur;
      var $Ursache;
      var $Pictogramm;
      var $Bild;
      var $AnsichtFensterBreite;
      var $AnsichtFensterHoehe;
      var $Index;

      /**
      *  Methode BildDaten
      *  Konstruktor der Klasse. Initialisiert die Membervariablen
      *
      *  Christian Schäfer
      *  Version 0.1, 29.05.2005
      */
      function BildDaten(){

         $this->Kultur = (string)'';
         $this->Ursache = (string)'';
         $this->Pictogramm = (string)'';
         $this->Bild = (string)'';
         $this->AnsichtFensterBreite = (string)'';
         $this->AnsichtFensterHoehe = (string)'';
         $this->Index = (string)'';

       // end function
      }

      function setzeKultur($Kultur){
         $this->Kultur = $Kultur;
      }
      function setzeUrsache($Ursache){
         $this->Ursache = $Ursache;
      }
      function setzePictogramm($Pictogramm){
         $this->Pictogramm = $Pictogramm;
      }
      function setzeBild($Bild){
         $this->Bild = $Bild;
      }
      function setzeAnsichtFensterBreite($Breite){
         $this->AnsichtFensterBreite = $Breite;
      }
      function setzeAnsichtFensterHoehe($Hoehe){
         $this->AnsichtFensterHoehe = $Hoehe;
      }
      function setzeIndex($Index){
         $this->Index = $Index;
      }

      function zeigeKultur(){
         return $this->Kultur;
      }
      function zeigeUrsache(){
         return $this->Ursache;
      }
      function zeigePictogramm(){
         return $this->Pictogramm;
      }
      function zeigeBild(){
         return $this->Bild;
      }
      function zeigeAnsichtFensterBreite(){
         return $this->AnsichtFensterBreite;
      }
      function zeigeAnsichtFensterHoehe(){
         return $this->AnsichtFensterHoehe;
      }
      function zeigeIndex(){
         return $this->Index;
      }

    // end class
   }
?>
