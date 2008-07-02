<?php
   /**
   *  Klasse BaumKnoten
   *  Stellt das DomainObjekt "BaumKnoten" dar.
   *
   *  Christian Schäfer
   *  Version 0.1, 28.06.2005
   *  Version 0.2, 27.07.2005 
   */
   class BaumKnoten
   {
      var $Name;
      var $VaterID;
      var $Typ;
      var $Link;
      var $Datum;
      var $Uhrzeit;
      var $Index;

      var $Kinder;


      function BaumKnoten(){

         $this->Name = (string)'';     // Name
         $this->VaterID = (string)'';  // VaterID
         $this->Typ = (string)'';      // Typ des Knotens
         $this->Link = (string)'';     // Dokument-Link
         $this->Datum = (string)'';    // Datum
         $this->Uhrzeit = (string)'';  // Uhrzeit
         $this->Groesse = (string)'';   // Größe
         $this->Index = (string)'';    // Index des Knotens

         $this->Kinder = array();      // Komposition der Kinder-Objekte

       // end function
      }


      function setzeName($Name){
         $this->Name = $Name;
      }
      function setzeVaterID($VaterID){
         $this->VaterID = $VaterID;
      }
      function setzeTyp($Typ){
         $this->Typ = $Typ;
      }
      function setzeLink($Link){
         $this->Link = $Link;
      }
      function setzeDatum($Datum){
         $this->Datum = $Datum;
      }
      function setzeUhrzeit($Uhrzeit){
         $this->Uhrzeit = $Uhrzeit;
      }
      function setzeIndex($Index){
         $this->Index = $Index;
      }
      function setzeKind($Kind){
         $this->Kinder[] = $Kind;
      }
      function haengeKinderEin($Kinder){
         $this->Kinder = $Kinder;
      }

      function zeigeName(){
         return $this->Name;
      }
      function zeigeVaterID(){
         return $this->VaterID;
      }
      function zeigeTyp(){
         return $this->Typ;
      }
      function zeigeLink(){
         return $this->Link;
      }
      function zeigeDatum(){
         return $this->Datum;
      }
      function zeigeUhrzeit(){
         return $this->Uhrzeit;
      }
      function zeigeIndex(){
         return $this->Index;
      }
      function zeigeKinder(){
         return $this->Kinder;
      }

    // end class
   }
?>
