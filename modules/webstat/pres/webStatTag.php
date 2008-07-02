<?php
   import('modules::webstat::biz','webStatManager');
   import('core::session','sessionManager');


   /**
   *  @package modules::webstat::pres
   *  @module webStatTag
   *
   *  Erzeugt den Webstat-Tag um eine Seite zählen zu können.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 21.12.2005<br />
   *  Version 0.2, 22.12.2005<br />
   */
   class webStatTag extends coreObject
   {

      function webStatTag(){
      }


      /**
      *  @module generateWebStatTag()
      *  @public
      *
      *  Generiert einen Bild-Tag (1x1 transparentes GIF) für die Webstatistik.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.12.2005<br />
      *  Version 0.2, 05.06.2006 (Korrektur beim Erzeugen des Fliegen-Bild-Codes)<br />
      *  Version 0.3, 15.06.2006 (Bug bei der benerierung des HTML-Codes der Fliege behoben)<br />
      */
      function generateWebStatTag($Page){

         // StatParameter initialisieren
         $StatParameter = array();

         // Seite
         $StatParameter['Seite'] = $Page;

         // Benutzer
         $wSM = &$this->__getServiceObject('modules::webstat::biz','webStatManager');
         $StatParameter['Benutzer'] = $wSM->getUserName();

         // Request URI
         $StatParameter['RequestURI'] = $_SERVER['REQUEST_URI'];

         // SessionID
         $Session = new sessionManager('WebStat');
         $StatParameter['SessionID'] = $Session->getSessionID();

         // Referrer
         $StatParameter['Referrer'] = $wSM->getReferrer();

         // Bild-Tag zurückgeben
         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');
         return '<img src="'.$URLBasePath.'/webstat.php?StatParameter='.base64_encode(serialize($StatParameter)).'" style="width: 1px; height: 1px;" />';

       // end function
      }

    // end class
   }
?>