<?php
   /**
   *  @package tools::variablen
   *  @class variablenHandler
   *
   *  Stellt Methoden zur Extraktion von Request-Variablen bereit.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, xx.02.2005<br />
   *  Version 0.2, 20.02.2005<br />
   *  Version 0.3, 09.04.2005<br />
   *  Version 0.4, 21.01.2006<br />
   */
   class variablenHandler
   {

      function variablenHandler(){
      }


      /**
      *  @public
      *  @static
      *
      *  Registriert im $_REQUEST-Array befindliche KEY->VALUE-Paare<br />
      *  in einem lokalen Array. Meist wird dazu $_LOCALS verwendet.<br />
      *  Übergeben wird ein gemischtes Array in der Form<br />
      *  <br />
      *    array('what','Name' => 'Test-Name').<br />
      *  <br />
      *  Die hier angegebenen Array-Werte werden, falls normal<br />
      *  übergeben ('what') in einen KEY $_['what'] mit je-<br />
      *  weiligen Werten aus dem REQUEST-Array übersetzt. Falls<br />
      *  es sich um ein KEY=>VALUE-Paar handelt, dann wird VALUE<br />
      *  als Standard-Wert behandelt, falls es keinen Offset im<br />
      *  REQUEST-Array vorhanden ist.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, xx.02.2005<br />
      *  Version 0.1, 20.02.2005<br />
      *  Version 0.2, 09.04.2005<br />
      */
      function registerLocal($Variablen = array()){

         $LokaleVariablen = array();

         foreach($Variablen as $Key => $Wert){

            if(is_int($Key)){

               $Inhalt = (string)'';

               if(isset($_REQUEST[$Wert])){
                  $Inhalt = $_REQUEST[$Wert];
                // end if
               }
               else{
                  $Inhalt = '';
                // end if
               }
               $LokaleVariablen[$Wert] = $Inhalt;
             // end if
            }
            else{

               $Inhalt = (string)'';

               if(isset($_REQUEST[$Key])){
                  $Inhalt = $_REQUEST[$Key];
                // end if
               }
               else{

                  if(isset($Wert) || !empty($Wert)){   // Vorsicht an dieser Stelle. Vorher war nur !empty($Wert).
                     $Inhalt = $Wert;                  // Wegen Probleme beim initialisieren mit Wert '0' wurde ein
                   // end if                           // isset($Wert) angehängt um auch das zu ermöglichen!
                  }
                  else{
                     $Inhalt = '';
                   // end else
                  }

                // end if
               }
               $LokaleVariablen[$Key] = $Inhalt;
             // end else
            }

          // end foreach
         }

         return $LokaleVariablen;

       // end function
      }

    // end class
   }
?>