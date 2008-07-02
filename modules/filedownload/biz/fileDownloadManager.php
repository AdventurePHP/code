<?php
   import('tools::image','imageManager');


   /**
   *  Package modules::filedownload::biz
   *  Klasse fileDownloadManager
   *  Implementiert den fileDownloadManager.
   *
   *  Christian Schäfer
   *  Version 0.1, 12.02.2006
   */
   class fileDownloadManager
   {

      var $__mediaPfad;


      function fileDownloadManager($MediaPfad){
         $Konstanten = get_defined_constants();
         $this->__mediaPfad = $Konstanten[trim($MediaPfad)];
       // end function
      }


      /**
      *  Funktion erzeugeDateiPfad()  [public/nonstatic]
      *  Erzeugt den Datei-Pfad. Gibt einen leeren Datei-Pfad zurück,
      *  falls versucht wird nicht erlaubte Inhalte anzuzeigen.
      *
      *  Christian Schäfer
      *  Version 0.1, 12.02.2006
      */
      function erzeugeDateiPfad($Datei){

         $return = $this->__mediaPfad.'/'.$Datei;

         $NichtErlaubt = array('admin',
                               '.php',
                               '.css',
                               '.htaccess',
                               '.htpasswd',
                               'apps'
                              );

         // Auf nicht erlaubte Inhalte prüfen
         for($i = 0; $i < count($NichtErlaubt); $i++){
            if(substr_count($return,$NichtErlaubt[$i])){
               $return = (string)'';
             // end if
            }
          // end if
         }

         return $return;

       // end function
      }

    // end class
   }
?>