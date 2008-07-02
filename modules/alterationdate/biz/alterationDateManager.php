<?php
   import('tools::datetime','dateTimeManager');
   import('modules::alterationdate::data','alterationDateMapper');


   /**
   *  @package modules::alterationdate::biz
   *  @module alterationDateManager
   *
   *  Klasse alterationDateManager implementiert die Businessschicht zur Änderungsdatumanzeige<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 22.06.2005<br />
   *  Version 0.2, 15.11.2006 (Update des Codes, Datum wird, falls Terminkalender leer als "--.--.----" zurückgegeben)<br />
   *  Version 0.3, 17.03.2007 (Implementierung nach PC V2)<br />
   */
   class alterationDateManager extends coreObject
   {

      function alterationDateManager(){
      }


      /**
      *  @module loadDate()
      *  @public
      *
      *  Service-Methode für die Präsentations-Schicht.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.06.2005<br />
      *  Version 0.2, 15.11.2006 (Update des Codes, Datum wird, falls Terminkalender leer, als "--.--.----" zurückgegeben)<br />
      *  Version 0.3, 17.03.2007 (Implementierung nach PC V2)<br />
      */
      function loadDate($Name){

         // Mapper im aktuellen Context holen
         $ADM = $this->__getServiceObject('modules::alterationdate::data','alterationDateMapper');
         $Date = $ADM->loadDate($Name);

         if(strlen($Date) > 0){
            return dateTimeManager::convertDate2Normal($Date);
          // end if
         }
         else{
            return '--.--.----';
          // end else
         }

       // end function
      }

    // end class
   }
?>