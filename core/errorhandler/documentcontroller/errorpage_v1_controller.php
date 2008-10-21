<?php
   /**
   *  @package core::errorhandler::documentcontroller
   *  @class errorpage_v1_controller
   *
   *  Implementiert den DocumentController der Fehlerseite.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 21.01.2007<br />
   */
   class errorpage_v1_controller extends baseController
   {

      function errorpage_v1_controller(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "transformContent" für die Fehlerseite.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.01.2007<br />
      */
      function transformContent(){

         $Errors = $this->__buildStackTrace();
         $Buffer = (string)'';


         // Template holen
         $Template__ErrorEntry = & $this->__getTemplate('ErrorEntry');


         // Stacktrace ausgeben
         for($i = 0; $i < count($Errors); $i++){

            // Aktuelle Funktion
            if(isset($Errors[$i]['function'])){
               $Template__ErrorEntry->setPlaceHolder('Function',$Errors[$i]['function']);
             // end if
            }


            // Fehler-Zeile
            if(isset($Errors[$i]['line'])){
               $Template__ErrorEntry->setPlaceHolder('Line',$Errors[$i]['line']);
             // end if
            }


            // Aktuelle Datei
            if(isset($Errors[$i]['file'])){
               $Template__ErrorEntry->setPlaceHolder('File',$Errors[$i]['file']);
             // end if
            }


            // Aktuelle Klasse
            if(isset($Errors[$i]['class'])){
               $Template__ErrorEntry->setPlaceHolder('Class',$Errors[$i]['class']);
             // end if
            }


            // Aktuelles Objekt
            if(isset($Errors[$i]['object'])){
               //$Template__ErrorEntry->setPlaceHolder('Object',$Errors[$i]['object']);
             // end if
            }


            // Aktueller Typ
            if(isset($Errors[$i]['type'])){
               $Template__ErrorEntry->setPlaceHolder('Type',$Errors[$i]['type']);
             // end if
            }


            // Aktuelle Argument-Liste
            if(isset($Errors[$i]['args'])){
               //$Template__ErrorEntry->setPlaceHolder('Arguments',$Errors[$i]['args']);
             // end if
            }


            // Element in den Puffer einsetzen
            $Buffer .= $Template__ErrorEntry->transformTemplate();

          // end for
         }


         // Liste ausgeben
         $this->setPlaceHolder('Stacktrace',$Buffer);


         // Attribute der aktuellen Meldung setzen
         $this->setPlaceHolder('ErrorID',$this->__Attributes['id']);
         $this->setPlaceHolder('ErrorMessage',$this->__Attributes['message']);
         $this->setPlaceHolder('ErrorNumber',$this->__Attributes['number']);
         $this->setPlaceHolder('ErrorFile',$this->__Attributes['file']);
         $this->setPlaceHolder('ErrorLine',$this->__Attributes['line']);

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt den Stacktrace.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.01.2007<br />
      */
      function __buildStackTrace(){
         return array_reverse(debug_backtrace());
       // end function
      }

    // end class
   }
?>