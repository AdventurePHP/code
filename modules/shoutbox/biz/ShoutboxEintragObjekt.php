<?php
   /**
   *  Klasse ShoutboxEintragObjekt
   *  Implementiert das Dom�nenobjekt ShoutboxEintrag
   *
   *  Christian Sch�fer
   *  Version 0.1, 03.05.2005
   */ 
   class ShoutboxEintragObjekt
   {
      var $Text;
      var $Datum;
      var $Uhrzeit;

      function ShoutboxEintragObjekt(){
         $this->Text = (string)'';
         $this->Datum = (string)'';
         $this->Uhrzeit = (string)'0';
       // end function
      }

      function setzeText($Text){
         $this->Text = $Text;
      }
      function setzeDatum($Datum){
         $this->Datum = $Datum;
      }
      function setzeUhrzeit($Uhrzeit){
         $this->Uhrzeit = $Uhrzeit;
      }

      function zeigeText(){
         return $this->Text;
      }
      function zeigeDatum(){
         return $this->Datum;
      }
      function zeigeUhrzeit(){
         return $this->Uhrzeit;
      }

    // end class
   }
?>
