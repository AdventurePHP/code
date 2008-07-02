<?php
   import('modules::teilnehmerliste::data','teilnehmerlisteMapper');
   import('modules::teilnehmerliste::biz','teilnehmerObjekt');


   /**
   *  Package modules::teilnehmerliste
   *  Klasse teilnehmerlisteManager
   *  Implementiert den Manager.
   *
   *  Christian Schäfer
   *  Version 0.1, 11.03.2006
   */
   class teilnehmerlisteManager extends coreObject
   {

      function teilnehmerlisteManager(){
      }


      function ladeTeilnehmerListe($Region){
         $M = &$this->__getServiceObject('modules::teilnehmerliste::data','teilnehmerlisteMapper');
         return $M->ladeTeilnehmerListe($Region);
       // end function
      }

    // end class
   }
?>