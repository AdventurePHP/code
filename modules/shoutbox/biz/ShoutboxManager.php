<?php
   import('modules::shoutbox::biz','ShoutboxEintragObjekt');
   import('modules::shoutbox::data','ShoutboxMapper');
   import('tools::cache','cacheV4Manager');
   import('tools::variablen','variablenHandler');
   import('tools::link','linkHandler');


   /**
   *  Klasse ShoutboxManager
   *  Implementiert den Manager der Business-Schicht
   *
   *  Christian Schäfer
   *  Version 0.1, 05.05.2005
   *  Version 0.2, 22.05.2005
   */
   class ShoutboxManager extends coreObject
   {

      var $_LOCALS;


      function ShoutboxManager(){
         $this->_LOCALS = variablenHandler::registerLocal(array('Seite'));
       // end function
      }


      function erzeugeEintrag($ShoutboxEintragObjekt){

         // Eintrag in Datenbank schreiben
         $ShoutboxMapper = new ShoutboxMapper();
         $ShoutboxMapper->speichereEintrag($ShoutboxEintragObjekt);


         // Cache löschen
         $cM = &$this->__getAndInitServiceObject('tools::cache','cacheV4Manager','cms');
         $cM->clearCacheWherePageIsContained($this->_LOCALS['Seite']);


         // Auf Ausgabe-Seite weiterleiten
         $Link = linkHandler::generateLink($_SERVER['REQUEST_URI'],array('Aktion' => 'anzeigen'));
         header('Location: '.$Link);

       // end function
      }


      function ladeDatenPerLimit($Start,$Anzahl){

         $ShoutboxMapper = new ShoutboxMapper();
         return $ShoutboxMapper->ladeShoutboxEintragePerLimit($Start,$Anzahl);

       // end function
      }

    // end class
   }
?>
