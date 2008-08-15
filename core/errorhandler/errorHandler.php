<?php
   // include dependent library
   import('core::logging','Logger');


   // Error-Reporting-Stufen festlegen
   error_reporting(E_ALL);
   ini_set('display_errors','1');


   // Fehlerbehandlungsoutine setzen
   set_error_handler('errorHandler');


   /**
   *  @package core::errorhandler
   *
   *  Wrapper für das Error-Handling.
   *
   *  @param string $ErrorNumber; Fehler-Nummer
   *  @param string $ErrorMessage; Fehler-Meldung
   *  @param string $ErrorFile; Fehler-Datei
   *  @param string $ErrorLine; Fehler-Zeile
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 30.11.2005<br />
   *  Version 0.2, 04.12.2005<br />
   *  Version 0.3, 15.01.2005<br />
   *  Version 0.4, 21.01.2007 (errorManager eingeführt)<br />
   *  Version 0.5, 20.06.2008 (Errors, that are triggered while using the @ sign are not raised anymore)<br />
   */
   function errorHandler($ErrorNumber,$ErrorMessage,$ErrorFile,$ErrorLine){

      // Don't raise error, if @ was applied
      if(error_reporting() == 0){
         return;
       // end if
      }

      // raise error and display error message
      $ErrMgr = new errorManager();
      $ErrMgr->raiseError($ErrorNumber,$ErrorMessage,$ErrorFile,$ErrorLine);

    // end function
   }


   /**
   *  @package core::errorhandler
   *  @class errorManager
   *
   *  Stellt einen globalen ErrorManager zur Verfügung.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 21.01.2007<br />
   */
   class errorManager
   {

      /**
      *  @private
      *  Fehlernummer
      */
      var $__ErrorNumber;

      /**
      *  @private
      *  Fehlermeldung
      */
      var $__ErrorMessage;

      /**
      *  @private
      *  Datei, in der der Fehler auftritt
      */
      var $__ErrorFile;

      /**
      *  @private
      *  Zeile, in der der Fehler auftritt
      */
      var $__ErrorLine;


      function errorManager(){
      }


      /**
      *  @public
      *
      *  Funktion, die von der globalen ErrorHandler-Funktion aufgerufen wird um einen Fehler zu melden.<br />
      *
      *  @param string $ErrorNumber; Fehler-Nummer
      *  @param string $ErrorMessage; Fehler-Meldung
      *  @param string $ErrorFile; Fehler-Datei
      *  @param string $ErrorLine; Fehler-Zeile
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.01.2007<br />
      */
      function raiseError($ErrorNumber,$ErrorMessage,$ErrorFile,$ErrorLine){

         // Lokale Attribute setzen
         $this->__ErrorNumber = $ErrorNumber;
         $this->__ErrorMessage = $ErrorMessage;
         $this->__ErrorFile = $ErrorFile;
         $this->__ErrorLine = $ErrorLine;


         // Fehler loggen
         $this->__logError();


         // Fehlerseite generieren
         echo $this->__buildErrorPage();

       // end function
      }


      /**
      *  @private
      *
      *  Logging-Funktion für Fehler.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.01.2007<br />
      *  Version 0.2, 29.03.2007 (Umstellung auf neuen Logger)<br />
      */
      function __logError(){

         // Fehlermeldung erzeugen
         $Message = '['.($this->__generateErrorID()).'] '.$this->__ErrorMessage.' (Number: '.$this->__ErrorNumber.', File: '.$this->__ErrorFile.', Line: '.$this->__ErrorLine.')';

         // Fehler protokollieren
         $L = &Singleton::getInstance('Logger');
         $L->logEntry('php',$Message,'ERROR');

       // end function
      }


      /**
      *  @private
      *
      *  Creates the error page.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.01.2007<br />
      *  Version 0.2, 03.03.2007 (Kompatibilität für PageController V1 implementiert)<br />
      *  Version 0.3, 04.03.2007 (Context wird nun für die ErrorPage gesetzt)<br />
      *  Version 0.4, 29.03.2007 (Kompatibilitäten zum alten PC entfernt)<br />
      *  Version 0.5, 13.08.2008 (Removed text only error page messages)<br />
      */
      function __buildErrorPage(){

         // Stacktrace zusammensetzen
         $Stacktrace = new Page('Stacktrace');
         $Stacktrace->set('Context','core::errorhandler');
         $Stacktrace->loadDesign('core::errorhandler::templates','errorpage');

         // Informationen der Meldung setzen
         $Document = & $Stacktrace->getByReference('Document');
         $Document->setAttribute('id',$this->__generateErrorID());
         $Document->setAttribute('message',$this->__ErrorMessage);
         $Document->setAttribute('number',$this->__ErrorNumber);
         $Document->setAttribute('file',$this->__ErrorFile);
         $Document->setAttribute('line',$this->__ErrorLine);

         // Fehlerseite ausgeben
         return $Stacktrace->transform();

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt die ID der Fehlermeldung.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.01.2007<br />
      */
      function __generateErrorID(){
         return md5($this->__ErrorMessage.$this->__ErrorNumber.$this->__ErrorFile.$this->__ErrorLine);
       // end function
      }

    // end class
   }
?>