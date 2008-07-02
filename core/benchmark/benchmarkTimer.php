<?php
   /**
   *  @package core::benchmark
   *  @class benchmarkTimer
   *
   *  Benchmark-Timer.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 31.12.2006<br />
   *  Version 0.1, 01.01.2007<br />
   */
   class benchmarkTimer
   {

      /**
      *  @private
      *  Root-Prozess.
      */
      var $__RootProcess = null;

      /**
      *  @private
      *  Prozess-Tabelle der aktuell laufenden Prozesse (Hash-Table).
      */
      var $__RunningProcesses = array();

      /**
      *  @private
      *  Referenz auf den zuletzt erzeugten Prozess.
      */
      var $__CurrentParent = null;

      /**
      *  @private
      *  Anzahl der Prozesse
      */
      var $__CurrentProcessID = 0;


      /**
      *  @private
      *  Definiert die kritische Zeitdauer eines Prozesses (für Report).
      */
      var $__CriticalTime = 0.5;


      /**
      *  @private
      *  Zähler für die ausgegebenen Zeilen (für Report).
      */
      var $__LineCounter = 0;


      /**
      *  @public
      *
      *  Konstruktor des Benchmark-Timers.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function benchmarkTimer(){
         $RootProcess = &$this->__createRootProcess();
         $this->__addRunningProcess($RootProcess);
         $this->__setCurrentParent($RootProcess);
       // end function
      }


      /**
      *  @public
      *
      *  Setzt die kritische Zeit eines Prozesses. Wird für die Anzeige des Reports benötigt.<br />
      *
      *  @param string $Time; Kritische Zeit eines Prozesses
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function setCriticalTime($Time){
         $this->__CriticalTime = $Time;
       // end function
      }


      /**
      *  @public
      *
      *  Gibt die kritische Zeit eines Prozesses zurück.<br />
      *
      *  @return string $Time; Kritische Zeit eines Prozesses
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function getCriticalTime(){
         return $this->__CriticalTime;
       // end function
      }


      /**
      *  @public
      *
      *  Startet einen neuen Benchmark-Prozess.<br />
      *
      *  @param string $Name; Name des zu erzeugenden Prozesses
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function start($Name = ''){

         $StartTime = $this->__generateMicroTime();

         if($Name == ''){
            trigger_error('[benchmarkTimer::start()] Required parameter name is not set!');
          // end if
         }

         if($this->__getRunningProcessByName($Name)!=null){
            trigger_error('[benchmarkTimer::start()] Benchmark process with name '.$Name.' is already running! Use a different one!');
          // end if
         }
         else{

            $Parent = &$this->__getCurrentParent();
            $Process = $this->__createProcess($Name,$StartTime,$Parent);
            $NewProcess = &$Process;
            $Parent->appendProcess($NewProcess);
            $this->__setCurrentParent($NewProcess);
            $this->__addRunningProcess($NewProcess);

          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Stoppt einen Benchmark-Prozess.<br />
      *
      *  @param string $Name; Name des zu stoppenden Prozesses
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function stop($Name = ''){

         $StopTime = $this->__generateMicroTime();

         if($Name == ''){
            trigger_error('[benchmarkTimer::stop()] Required parameter name is not set!');
          // end if
         }

         if(isset($this->__RunningProcesses[$Name])){
            $currentProcess = &$this->__getRunningProcessByName($Name);
            $currentProcess->setProcessStopTime($StopTime);
            $this->__setCurrentParent($currentProcess->getParentProcess());
            $this->__removeRunningProcess($Name);
          // end if
         }
         else{
            trigger_error('[benchmarkTimer::stop()] Process with name '.$Name.' is not running yet!');
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Gibt die nächste Prozess-ID zurück.<br />
      *
      *  @return string $ID; Nächste Prozess-ID
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function __getID(){
         $this->__CurrentProcessID += 1;
         return $this->__CurrentProcessID;
       // end function
      }


      /**
      *  @private
      *
      *  Gibt die aktuelle Zeit zurück.<br />
      *
      *  @return string $MicroTime; Zeit in Millisekunden
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function __generateMicroTime(){

         if(intval(phpversion()) == 5){
            $return = microtime(true);
          // end if
         }
         else{
            list($usec, $sec) = explode(' ',microtime());
            $return = (float) $usec + (float) $sec;
          // end for
         }

         return $return;

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt einen Root-Prozess und gibt diesen zurück.<br />
      *
      *  @param string $Name; Name des Prozesses
      *  @param string $StartTime; Startzeit des Prozesses
      *  @param object $Parent; Referenz auf das Eltern-Objekt
      *  @return object $Process; Root-Prozess
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function __createProcess($Name,$StartTime,&$Parent){

         $Process = new benchmarkProcess();
         $Process->setProcessID($this->__getID());
         $Process->setProcessName($Name);
         $Process->setProcessLevel($Parent->getProcessLevel() + 1);
         $Process->setProcessStartTime($StartTime);
         $Process->setParentProcess($Parent);

         return $Process;

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt einen Root-Prozess und gibt Diesen zurück.<br />
      *
      *  @return object $Process; Root-Prozess
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function &__createRootProcess(){

         $StartTime = $this->__generateMicroTime();

         $RootProcess = new benchmarkProcess();
         $RootProcess->setProcessID($this->__getID());
         $RootProcess->setProcessName(get_class($this));
         $RootProcess->setProcessLevel(0);
         $RootProcess->setProcessStartTime($StartTime);
         $this->__RootProcess = &$RootProcess;

         return $RootProcess;

       // end function
      }


      /**
      *  @private
      *
      *  Stoppt den Root-Prozess und gibt den Prozessbaum zurück.<br />
      *
      *  @return object $RootProcess; Root-Prozess-Knoten
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function &__getRootProcess(){

         $RootProcess = &$this->__RootProcess;
         $RootProcess->setProcessStopTime($this->__generateMicroTime());
         return $RootProcess;

       // end function
      }


      /**
      *  @private
      *
      *  Fügt einen Prozess zur Liste der aktuell laufenden Prozesse hinzu.<br />
      *
      *  @param object $Process; Referenz auf ein Prozess-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function __addRunningProcess(&$Process){
         $Name = $Process->getProcessName();
         $this->__RunningProcesses[$Name] = &$Process;
       // end function
      }


      /**
      *  @private
      *
      *  Löscht einen Prozess aus der Liste der aktuell laufenden Prozesse.<br />
      *
      *  @param object $Process; Referenz auf ein Prozess-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function __removeRunningProcess($Name){
         unset($this->__RunningProcesses[$Name]);
       // end function
      }


      /**
      *  @private
      *
      *  Gibt eine Referenz auf einen Prozess zurück, der durch $Name identifiziert wird.<br />
      *
      *  @param string $Name; Name des gewünschten Prozesses
      *  @return null | object $Process; Null oder die Referenz auf den durch $Name spezifizierten Prozess
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function &__getRunningProcessByName($Name){

         if(isset($this->__RunningProcesses[$Name])){
            return $this->__RunningProcesses[$Name];
          // end if
         }
         else{
            $return = null;
            return $return;
          // end else
         }

       // end function
       }


      /**
      *  @private
      *
      *  Setzt eine Referenz auf den zuletzt erzeugten Prozess.<br />
      *
      *  @param object $Process; Referenz auf ein Prozess-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function __setCurrentParent(&$Process){
         $this->__CurrentParent = &$Process;
       // end function
       }


      /**
      *  @private
      *
      *  Setzt eine Referenz auf den zuletzt erzeugten Prozess.<br />
      *
      *  @return object $Process; Referenz auf ein Prozess-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function &__getCurrentParent(){
         return $this->__CurrentParent;
       // end function
      }



      /**
      *
      *  Generiert einen Benchmark-Report.<br />
      *
      *  @return string Report; HTML-Quelltext des Benchmark-Reports
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function createReport(){

         // Prozess-Baum holen
         $ProcessTree = $this->__getRootProcess();

         // Puffer initialisieren
         $Buffer = (string)'';

         // Header generieren
         $Buffer .= $this->__generateHeader();

         // Report rekursiv generieren
         $Buffer .= $this->__createReport4Process($ProcessTree);

         // Footer generieren
         $Buffer .= $this->__generateFooter();

         // Report zurückgeben
         return $Buffer;

       // end function
      }


      /**
      *  @private
      *
      *  Generiert einen Benchmark-Report für einen übergebenen Prozess.<br />
      *
      *  @param object $Process; Referenz auf den aktuellen Prozess
      *  @return string $Report4Line; Report-Zeile für einen Prozess
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 01.01.2007<br />
      */
      function __createReport4Process(&$Process){

         // Puffer initialisieren
         $Buffer = (string)'';


         // Zeile generieren
         $Buffer .= $this->__generateReportLine($Process->getProcessName(),$Process->getProcessLevel(),$Process->getProcessRuntime());


         // Kinder, falls vorhanden, darstellen
         if($Process->hasChildProcesses()){

            // Kind-Prozesse auslesen
            $ProcessesChildren = $Process->getProcesses();

            // Iterativ darstellen
            foreach($ProcessesChildren as $Offset => $Child){
               $Buffer .= $this->__createReport4Process($Child);
             // end foreach
            }

          // end if
         }


         // Puffer zurückgeben
         return $Buffer;

       // end function
      }


      /**
      *  @private
      *
      *  Generiert die Einrückung für einen gegebenen Level.<br />
      *
      *  @param string $Level; Level des aktuellen Prozesses
      *  @return string $Tab; Tab-String
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 01.01.2007<br />
      */
      function __generateTab($Level){

         $Tab = '&nbsp;';
         $String = (string)'';

         for($i = 0; $i < $Level; $i++){
            $String .= $Tab.$Tab.$Tab.$Tab.$Tab.$Tab.$Tab;
          // end for
         }

         return $String;

       // end function
      }


      /**
      *  @private
      *
      *  Generiert den Header der Prozes-Übersicht.<br />
      *
      *  @return string $Header; Header-Zeile des Reports
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 01.01.2007<br />
      *  Version 0.2, 24.06.2007 (Text-Ausrichtung ist nun immer links)<br />
      */
      function __generateHeader(){

         $Buffer = (string)'';
         $Buffer .= '<div style="width: 100%; background-color: white; border: 1px dashed black; margin-top: 10px; padding: 10px; font-size: 12px; font-family: Arial, Helvetica, sans-serif; text-align: left;">';
         $Buffer .= "\n";
         $Buffer .= '  <font style="font-size: 16px; font-variant: small-caps;">Benchmark - Report:</font>';
         $Buffer .= "\n";
         $Buffer .= '  <br />';
         $Buffer .= "\n";
         $Buffer .= '  <br />';
         $Buffer .= "\n";
         $Buffer .= '  <br />';
         $Buffer .= "\n";
         $Buffer .= '  <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; border-bottom: 1px solid black; background-color: #dddddd; font-size: 12px; font-family: Arial, Helvetica, sans-serif; padding: 2px;">';
         $Buffer .= "\n";
         $Buffer .= '    <tr>';
         $Buffer .= "\n";
         $Buffer .= '      <td style="width: 80%;">';
         $Buffer .= "\n";
         $Buffer .= '        <strong>Processtree</strong>';
         $Buffer .= "\n";
         $Buffer .= '      </td>';
         $Buffer .= "\n";
         $Buffer .= '      <td style="width: 20%; text-align: right; padding-right: 75px;">';
         $Buffer .= "\n";
         $Buffer .= '        <strong>Time</strong>';
         $Buffer .= "\n";
         $Buffer .= '      </td>';
         $Buffer .= '    <tr>';
         $Buffer .= "\n";

         return $Buffer;

       // end function
      }


      /**
      *  @private
      *
      *  Generiert den Footer der Prozes-Übersicht.<br />
      *
      *  @return string $Footer; Footer-Zeile des Reports
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 01.01.2007<br />
      */
      function __generateFooter(){

         $Buffer = (string)'';
         $Buffer .= '</div>';
         $Buffer .= "\n";

         return $Buffer;

       // end function
      }


      /**
      *  @private
      *
      *  Generiert eine Zeile in der Prozes-Übersicht.<br />
      *
      *  @param string $Name; Name des Prozesses
      *  @param string $Level; Level des Prozesses
      *  @param string $Time; Lauf-Zeit des Prozesses
      *  @return string $ReportLine; Zeile des Pozess-Reports
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.01.2007<br />
      *  Version 0.2, 08.03.2008 (Beschriftung geändert)<br />
      */
      function __generateReportLine($Name,$Level,$Time){

         // Vollständigen Display-Namenm generieren
         $Name = $this->__generateTab($Level).'&#187&nbsp;&nbsp;&nbsp;'.$Name;

         // Puffer initialisieren
         $Buffer = (string)'';

         // Zeile generieren
         if(($this->__LineCounter % 2) == 0 ){
            $Buffer .= '  <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 12px; font-family: Arial, Helvetica, sans-serif;">';

          // end if
         }
         else{
            $Buffer .= '  <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; background-color: #eeeeee; font-size: 12px; font-family: Arial, Helvetica, sans-serif;">';
          // end else
         }

         $Buffer .= "\n";
         $Buffer .= '    <tr>';
         $Buffer .= "\n";
         $Buffer .= '      <td style="width: 80%">';
         $Buffer .= "\n";
         $Buffer .= '        '.$Name;
         $Buffer .= "\n";
         $Buffer .= '      </td>';
         $Buffer .= "\n";
         $Buffer .= '      <td style="width: 20%; text-align: right;">';
         $Buffer .= "\n";

         // Zeitdauer ausgeben
         if($Time > $this->__CriticalTime){
            $Buffer .= '        <font style="color: red; font-weight: bold;">'.trim($Time).'&nbsp;s&nbsp;</font>';
          // end if
         }
         else{
            $Buffer .= '        <font style="color: green; font-weight: bold;">'.trim($Time).'&nbsp;s&nbsp;</font>';
          // end else
         }

         $Buffer .= "\n";
         $Buffer .= '      </td>';
         $Buffer .= "\n";
         $Buffer .= '    </tr>';
         $Buffer .= "\n";
         $Buffer .= '  </table>';
         $Buffer .= "\n";

         // Line-Counter inkrementieren
         $this->__LineCounter++;

         // Zeile zurückgeben
         return $Buffer;

       // end function
      }

    // end class
   }


   /**
   *  @package core::benchmark
   *  @class benchmarkProcess
   *
   *  Objekt eines Benchmark-Prozesses.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 31.12.2006<br />
   */
   class benchmarkProcess
   {

      /**
      *  @private
      *  ID des Prozesses.
      */
      var $__ProcessID;

      /**
      *  @private
      *  Name des Prozesses.
      */
      var $__ProcessName;

      /**
      *  @private
      *  Level des Prozesses.
      */
      var $__ProcessLevel;

      /**
      *  @private
      *  Start-Zeit des Prozesses.
      */
      var $__ProcessStartTime = null;

      /**
      *  @private
      *  Stop-Zeit des Prozesses.
      */
      var $__ProcessStopTime = null;

      /**
      *  @private
      *  Referenz auf den Eltern-Prozesses.
      */
      var $__ParentProcess = null;

      /**
      *  @private
      *  Liste der Kind-Prozesse.
      */
      var $__Processes = array();


      function benchmarkProcess(){
      }

      function setProcessID($ID){
         $this->__ProcessID = $ID;
       // end function
      }

      function getProcessID(){
         return $this->__ProcessID;
       // end function
      }

      function setProcessName($Name){
         $this->__ProcessName = $Name;
       // end function
      }

      function getProcessName(){
         return $this->__ProcessName;
       // end function
      }

      function setProcessLevel($Level){
         $this->__ProcessLevel = $Level;
       // end function
      }

      function getProcessLevel(){
         return $this->__ProcessLevel;
       // end function
      }

      function setProcessStartTime($StartTime){
         $this->__ProcessStartTime = $StartTime;
       // end function
      }

      function getProcessStartTime(){
         return $this->__ProcessStartTime;
       // end function
      }

      function setProcessStopTime($StopTime){
         $this->__ProcessStopTime = $StopTime;
       // end function
      }

      function getProcessStopTime(){
         return $this->__ProcessStopTime;
       // end function
      }

      function setParentProcess(&$Process){
         $this->__ParentProcess = &$Process;
       // end function
      }

      function &getParentProcess(){
         return $this->__ParentProcess;
       // end function
      }

      function appendProcess(&$Process){
         $ProcessID = $Process->getProcessID();
         $this->__Processes[$ProcessID] = &$Process;
       // end function
      }

      function getProcesses(){
         return $this->__Processes;

       // end function
      }

      function hasChildProcesses(){

         if(count($this->__Processes) > 0){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }

      function getProcessRuntime(){

         if($this->__ProcessStopTime == null){
            return '--------------------';
          // end if
         }

         return number_format($this->__ProcessStopTime - $this->__ProcessStartTime,10);

       // end function
      }

    // end class
   }
?>