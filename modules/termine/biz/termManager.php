<?php
   import('modules::termine::biz','termObject');
   import('modules::termine::data','termDataMapper');
   import('core::singleton','Singleton');


   /**
   *  Package modules::termine::biz<br />
   *  Klasse TerminDatenManager<br />
   *  Implementiert den Datenmanager für TerminDaten<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 17.04.2005<br />
   *  Version 0.2, 01.09.2006 (Mapper wird nun singleton instanziert)<br />
   *  Version 0.3, 17.03.2007 (Klasse in termManager umbenannt)<br />
   */
   class termManager extends coreObject
   {


      function termManager(){
      }


      /**
      *  Funktion ladeTerminDaten() [public/nonstatic]<br />
      *  Gibt Termin-Daten an die pres-Schicht zurück.<br />
      *  <br />
      *  Christian Schäfer<br />
      *  Version 0.1, 17.04.2005<br />
      *  Version 0.2, 01.09.2006 (Verwendung des Mappers umgestellt)<br />
      */
      function loadTerms(){
         $tdM = &$this->__getServiceObject('modules::termine::data','termDataMapper');
         return $tdM->loadTerms();
       // end function
      }

    // end class
   }
?>