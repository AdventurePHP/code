<?php
   import('tools::variablen','variablenHandler');


   /**
   *  Package modules::dateianzeige::pres::documentcontroller<br />
   *  Klasse dateianzeige_v1_controller<br />
   *  Implementiert den DocumentController für das Template 'dateianzeige'.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, xx.07.2006<br />
   *  Version 0.2, 01.09.2006 (Bugfix für Download bei RewriteURL = 1)<br />
   *  Version 0.3, 02.09.2006 (Lösung für das Dateianzeige-Problem, Beseitigung des Bugfixes)<br />
   */
   class dateianzeige_v1_controller extends baseController
   {
      var $_LOCALS;


      function dateianzeige_v1_controller(){

         // WORKAROUND:
         // Standard-Wert von 'Datei' wird auf $_GET['Datei'] gesetzt, da bei aktivierter
         // RewriteURL-Option das REQUEST-Array nicht richtig gefüllt wird.
         //
         // LÖSUNG:
         // Page wird IMMER ohne URLRewriting gestartet, damit die übergebenen URL-Parameter
         // richtig aus der URL-Parametern geparst werden
         $this->_LOCALS = variablenHandler::registerLocal(array('Datei','Pfad' => 'MEDIA_PATH'));

       // end function
      }


      function transformContent(){

         // Daten aufbereiten
         $DateiName = basename(trim($this->_LOCALS['Datei']));
         $PfadAngabe = trim($this->_LOCALS['Pfad']);

         // Logeintrag für fehlende Konfiguration des Pfades
         if(!defined($PfadAngabe)){
            trigger_error('Konfiguration für '.$PfadAngabe.' nicht in der config.php definiert! ('.$_SERVER['SCRIPT_NAME'].')');
            exit();
          // end if
         }

         // Definierte Konstanten ermitteln
         $Konstanten = get_defined_constants();


         // Pfad des Bildes ermitteln
         $Pfad = $Konstanten[trim($PfadAngabe)];


         // Bild anzeigen
         if(substr_count(strtolower($DateiName),'.gif') > 0 || substr_count(strtolower($DateiName),'.jpg') > 0 || substr_count(strtolower($DateiName),'.jpeg') > 0){

            $Template__Bild = & $this->__getTemplate('Bild');

            $Reg = &Singleton::getInstance('Registry');
            $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');

            $Template__Bild->setPlaceHolder('BildLink',$URLBasePath.'/bild.php?Bild='.$DateiName.'&Pfad='.$PfadAngabe);
            $this->setPlaceHolder('Inhalt',$Template__Bild->transformTemplate());

          // end if
         }
         else{

            if(substr_count(strtolower($DateiName),'.pdf')){
               header('Content-Type: application/pdf');
             // end if
            }
            elseif(substr_count(strtolower($DateiName),'.doc')){
               header('Content-Type: application/msword');
             // end elseif
            }
            elseif(substr_count(strtolower($DateiName),'.xls')){
               header('Content-Type: application/vnd.ms-excel');
             // end elseif
            }
            else{
               header('Content-Type: application/octet-stream');
               header('Content-Disposition: attachment; filename='.$DateiName);
             // end if
            }

            // Datei ausgeben
            @readfile($Pfad.'/'.$DateiName);

          // end else
         }

       // end function
      }

    // end class
   }
?>