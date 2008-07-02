<?php
   /**
   *  @package modules::termine::biz
   *  @module termObject
   *
   *  Implementiert ein Termin-Objekt.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 17.04.2005<br />
   *  Version 0.2, 01.06.2005<br />
   *  Version 0.3, 17.03.2007 (Klasse in termObject umbenannt)<br />
   */
   class termObject
   {

      var $__Datum;
      var $__Text;
      var $__Link;
      var $__DetailText;
      var $__TIndex;


      /**
      *  @module termObject
      *  @public
      *
      *  Konstruktor der Klasse. Initialisiert die Membervariablen.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.04.2005
      *  Version 0.2, 01.06.2005
      */
      function termObject(){
         $this->__Datum = (string)'';
         $this->__Text = (string)'';
         $this->__Link = (string)'';
         $this->__DetailText = (string)'';
         $this->__TIndex = (string)'';
       // end function
      }

      function setzeDatum($Datum){
         $this->__Datum = $Datum;
      }
      function setzeText($Text){
         $this->__Text = $Text;
      }
      function setzeLink($Link){
         $this->__Link = $Link;
      }
      function setzeDetailText($DetailText){
         $this->__DetailText = $DetailText;
      }
      function setzeTIndex($TIndex){
         $this->__TIndex = $TIndex;
      }

      function zeigeDatum(){
         return $this->__Datum;
      }
      function zeigeText(){
         return $this->__Text;
      }
      function zeigeLink(){
         return $this->__Link;
      }
      function zeigeDetailText(){
         return $this->__DetailText;
      }
      function zeigeTIndex(){
         return $this->__TIndex;
      }

    // end class
   }
?>