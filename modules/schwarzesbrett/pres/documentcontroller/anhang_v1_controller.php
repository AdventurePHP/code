<?php
   import('tools::variablen','variablenHandler');


   class anhang_v1_controller extends baseController
   {
      var $_LOCALS;


      function anhang_v1_controller(){
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
         $Reg = &Singleton::getInstance('Registry');
         $URLBasePath = $Reg->retrieve('apf::core','URLBasePath');
         $this->__Templates['Bild']->setPlaceHolder('BildLink',$URLBasePath.'/bild.php?Bild='.$DateiName.'&Pfad='.$PfadAngabe);
         $this->setPlaceHolder('Inhalt',$this->__Templates['Bild']->transform());

       // end function
      }

    // end class
   }
?>
