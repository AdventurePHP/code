<?php
   // flushLogger als Shutdown-Function registrieren
   register_shutdown_function('flushLogger');


   /**
   *  @package core::logging
   *
   *  Wrapper für fas Flushen der Log-Entries auf Platte.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 29.03.2007<br />
   */
   function flushLogger(){
      $L = &Singleton::getInstance('Logger');
      $L->flushLogBuffer();
    // end function
   }


   /**
   *  @package core::logging
   *  @class logEntry
   *
   *  Implementiert ein logEntry-Objekt.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 29.03.2007<br />
   */
   class logEntry
   {

      /**
      *  @private
      *  Datum der Meldung.
      */
      var $__Date;


      /**
      *  @private
      *  Uhrzeit der Meldung.
      */
      var $__Time;


      /**
      *  @private
      *  Text der Meldung.
      */
      var $__Message;


      /**
      *  @private
      *  Typ der Meldung.
      */
      var $__Type;


      /**
      *  @public
      *
      *  Konstruktor der Klasse. Erstellt ein neues logEntry-Objekt.<br />
      *
      *  @param string $Message; Meldung
      *  @param string $Type; Type der Meldung
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function logEntry($Message,$Type){

         $this->__Date = date('Y-m-d');
         $this->__Time = date('H:i:s');
         $this->__Message = $Message;
         $this->__Type = $Type;

       // end function
      }


      /**
      *  @public
      *
      *  Liefert den Message-String, der für Logging und Ausgabe verwendet wird zurück.<br />
      *
      *  @return string $Message; Komplette Meldung
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function toString(){
         return '['.$this->__Date.' '.$this->__Time.'] ['.$this->__Type.'] '.$this->__Message;
       // end function
      }

    // end class
   }


   /**
   *  @package core::logging
   *  @class Logger
   *
   *  Implementiert einen generischen Logger für das Logging in<br />
   *  Programmteilen und Modulen. Muss Singleton instanziiert werden!<br />
   *  Das Flushen der Inhalte wird zum Ende eines Requests automatisch<br />
   *  erledigt.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 29.03.2007<br />
   */
   class Logger
   {

      /**
      *  @private
      *  Array, dessen Keys die Logfile-Namen und dessen Offset die zugehörigen
      *  logEntry-Objekte sind.
      */
      var $__LogEntries = array();


      /**
      *  @private
      *  Pfad, in den Log-Dateien abgelegt werden sollen.
      */
      var $__LogDir;


      /**
      *  @private
      *  Ordner-Rechte, mit denen Log-Ordner angelegt werden.
      */
      var $__logFolderPermissions = 0777;


      /**
      *  @private
      *  Ordner-Rechte, mit denen Log-Ordner angelegt werden.
      */
      var $__CRLF = PHP_EOL;



      /**
      *  @public
      *
      *  Constructor of the Logger. Initializes the LogDir.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.03.2007<br />
      *  Version 0.2, 02.04.2007 (Fehler beim Anlegen des Log-Verzeichnisses behoben)<br />
      *  Version 0.3, 21.062.008 (Replaced APPS__LOG_PATH with a value from the registry)<br />
      */
      function Logger(){

         // initialize log directory
         $Reg = &Singleton::getInstance('Registry');
         $this->__LogDir = $Reg->retrieve('apf::core','LogDir');

         // check if lock dir exists
         if(!is_dir($this->__LogDir)){

            // try to create non existing log dir
            if(!mkdir($this->__LogDir,$this->__logFolderPermissions)){
               trigger_error('[Logger->Logger()] The log directory "'.$this->__LogDir.'" cannot be created du to permission restrictions! Please check config an specify the "LogDir" (namespace: "apf::core") parameter in the registry!');
               exit();
             // end if
            }

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Erzeugt einen Log-Eintrag.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function logEntry($LogFileName,$Message,$Type = 'INFO'){
         $this->__LogEntries[$LogFileName][] = new logEntry($Message,$Type);
       // end function
      }


      /**
      *  @public
      *
      *  Leert den Log-Puffer und schreibt die Einträge auf Platte.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function flushLogBuffer(){

         foreach($this->__LogEntries as $LogFileName => $LogEntries){

            // Kompletten Dateinamen generieren
            $LogFileName = $this->__getLogFileName($LogFileName);

            // Kompletten Dateinamen incl. Pfad generieren
            $LogFile = $this->__LogDir.'/'.$LogFileName;

            // Entries auf Platte flushen
            if(count($LogEntries) > 0){

               // Datei zum appenden öffnen
               $lFH = fopen($LogFile,'a+');

               for($i = 0; $i < count($LogEntries); $i++){
                  fwrite($lFH,$LogEntries[$i]->toString().$this->__CRLF);
                // end for
               }

               // Datei schließen
               fclose($lFH);

             // end if
            }

          // end foreach
         }

       // end function
      }


      /**
      *  @private
      *
      *  Gibt den Namen einer LogDatei anhand des Body's des Namens zurück.<br />
      *  Log-Dateien haben das Format jjjj_mm_dd__{filename}.log.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function __getLogFileName($FileName){
         return date('Y_m_d').'__'.str_replace('-','_',strtolower($FileName)).'.log';
       // end function
      }

    // end class
   }
?>