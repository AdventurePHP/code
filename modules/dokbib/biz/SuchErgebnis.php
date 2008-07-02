<?php
   import('modules::baumstruktur::biz','BaumKnoten');


   /**
   *  Klasse SuchErgebnis<br />
   *  Stellt das DomainObjekt "SuchErgebnis" dar.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 01.07.2005<br />
   *  Version 0.2, 22.07.2005<br />
   */
   class SuchErgebnis extends BaumKnoten
   {

      var $Relevanz;
      var $Pfad;
      var $DokumentenPfad;


      function SuchErgebnis(){

         $this->Relevanz = (string)'';
         $this->Pfad = array();
         $this->DokumentenPfad = (string)'';

       // end function
      }


      function setzeRelevanz($Relevanz){
         $this->Relevanz = $Relevanz;
      }
      function setzePfad($Pfad){
         $this->Pfad = $Pfad;
      }
      function setzeDokumentenPfad($Pfad){
         $this->DokumentenPfad = $Pfad;
      }


      function zeigeRelevanz(){
         return $this->Relevanz;
      }
      function zeigePfad(){
         return $this->Pfad;
      }
      function zeigeDokumentenPfad(){
         return $this->DokumentenPfad;
      }

    // end class
   }
?>
