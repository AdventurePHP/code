<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
   *  @namespace core::benchmark
   *  @class BenchmarkTimer
   *
   *  This class implements the benchmark timer used for measurement of the core components
   *  and your software. Must be used as a singleton to guarantee, that all benchmark tags
   *  are included within the report. Usage:
   *  <pre>
   *  $T = &Singleton::getInstance('BenchmarkTimer');
   *  $T->start('my_tag');
   *  ...
   *  $T->stop('my_tag');
   *  ...
   *  echo $T->createReport();
   *  </pre>
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.12.2006<br />
   *  Version 0.1, 01.01.2007<br />
   */
   final class BenchmarkTimer
   {

      /**
      *  @private
      *  The benchmark root process.
      */
      private $__RootProcess = null;


      /**
      *  @private
      *  The process table, that contains all running processes (hash table).
      */
      private $__RunningProcesses = array();


      /**
      *  @private
      *  References the current parent process (=last process created).
      */
      private $__CurrentParent = null;


      /**
      *  @private
      *  Stores the process count.
      */
      private $__CurrentProcessID = 0;


      /**
      *  @private
      *  Defines the critical time for the benchmark report.
      */
      private $__CriticalTime = 0.5;


      /**
      *  @private
      *  Line counter for the report.
      */
      private $__LineCounter = 0;


      /**
      *  @public
      *
      *  Constructor of the BenchmarkTimer. Initializes the root process.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function BenchmarkTimer(){
         $RootProcess = &$this->__createRootProcess();
         $this->__addRunningProcess($RootProcess);
         $this->__setCurrentParent($RootProcess);
       // end function
      }


      /**
      *  @public
      *
      *  Sets the critical time. If the critical time is reached, the time is printed in red digits.
      *
      *  @param string $time the critical time in seconds
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function setCriticalTime($time){
         $this->__CriticalTime = $time;
       // end function
      }


      /**
      *  @public
      *
      *  Returns the critical time.
      *
      *  @return string $criticalTime the critical time
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
      *  This method is used to starts a new benchmark timer.
      *
      *  @param string $name the name of the benchmark tag
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function start($name = ''){

         $StartTime = $this->__generateMicroTime();

         if($name == ''){
            trigger_error('[BenchmarkTimer::start()] Required parameter name is not set!');
          // end if
         }

         if($this->__getRunningProcessByName($name)!=null){
            trigger_error('[BenchmarkTimer::start()] Benchmark process with name '.$name.' is already running! Use a different one!');
          // end if
         }
         else{

            $Parent = &$this->__getCurrentParent();
            $Process = $this->__createProcess($name,$StartTime,$Parent);
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
      *  Stops the benchmark timer, started with start().
      *
      *  @param string $name name of the desired benchmark tag
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function stop($name){

         $stopTime = $this->__generateMicroTime();

         if(isset($this->__RunningProcesses[$name])){
            $currentProcess = &$this->__getRunningProcessByName($name);
            $currentProcess->setProcessStopTime($stopTime);
            $this->__setCurrentParent($currentProcess->getParentProcess());
            $this->__removeRunningProcess($name);
          // end if
         }
         else{
            trigger_error('[BenchmarkTimer::stop()] Process with name '.$name.' is not running yet!');
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Returns the id of the next process.
      *
      *  @return int $if the next internal process id
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      private function __getID(){
         $this->__CurrentProcessID += 1;
         return $this->__CurrentProcessID;
       // end function
      }


      /**
      *  @private
      *
      *  Returns the current timestamp in milliseconds.
      *
      *  @return string $microTime current timestamp in milliseconds
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      private function __generateMicroTime(){

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
      *  Creates the process and returns it.
      *
      *  @param string $name the name of the process
      *  @param string $startTime the start timestamp
      *  @param object $parent reference on the parent object
      *  @return object $process the process
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      private function __createProcess($name,$startTime,&$parent){

         $process = new benchmarkProcess();
         $process->setProcessID($this->__getID());
         $process->setProcessName($name);
         $process->setProcessLevel($parent->getProcessLevel() + 1);
         $process->setProcessStartTime($startTime);
         $process->setParentProcess($parent);
         return $process;

       // end function
      }


      /**
      *  @private
      *
      *  Creates the root process and returns it.
      *
      *  @return benchmarkProcess $rootProcess the root process
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      private function &__createRootProcess(){

         $startTime = $this->__generateMicroTime();
         $rootProcess = new benchmarkProcess();
         $rootProcess->setProcessID($this->__getID());
         $rootProcess->setProcessName(get_class($this));
         $rootProcess->setProcessLevel(0);
         $rootProcess->setProcessStartTime($startTime);
         $this->__RootProcess = &$rootProcess;
         return $rootProcess;

       // end function
      }


      /**
      *  @private
      *
      *  Stopps the root process and returns it.
      *
      *  @return benchmarkProcess $rootProcess the stopped root process
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      private function &__getRootProcess(){

         $rootProcess = &$this->__RootProcess;
         $rootProcess->setProcessStopTime($this->__generateMicroTime());
         return $rootProcess;

       // end function
      }


      /**
      *  @private
      *
      *  Adds a process to the list of running processes.
      *
      *  @param benchmarkProcess $process a reference on the running process
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      private function __addRunningProcess(&$process){
         $name = $process->getProcessName();
         $this->__RunningProcesses[$name] = &$process;
       // end function
      }


      /**
      *  @private
      *
      *  Deletes a running process from the hash table.
      *
      *  @param string $name the name of the process
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      private function __removeRunningProcess($name){
         unset($this->__RunningProcesses[$name]);
       // end function
      }


      /**
      *  @private
      *
      *  Returns a running process by it's name.
      *
      *  @param string $name name of the desired process
      *  @return null | benchmarkProcess $process null or the desired object reference
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      private function &__getRunningProcessByName($name){

         if(isset($this->__RunningProcesses[$name])){
            return $this->__RunningProcesses[$name];
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
      *  References the currently created process.
      *
      *  @param benchmarkProcess $process the reference on the desired benchmark process
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      private function __setCurrentParent(&$process){
         $this->__CurrentParent = &$process;
       // end function
       }


      /**
      *  @private
      *
      *  Returns the currently created process.
      *
      *  @return benchmarkProcess $process the reference on the desired benchmark process
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      private function &__getCurrentParent(){
         return $this->__CurrentParent;
       // end function
      }


      /**
      *  @public
      *
      *  Generates the report of the recorded benchmark tags.
      *
      *  @return string $report the HTML source code of the benchmark
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
      function createReport(){

         // get process tree
         $ProcessTree = $this->__getRootProcess();

         // initialize buffer
         $Buffer = (string)'';

         // generate header
         $Buffer .= $this->__generateHeader();

         // generate report recursivly
         $Buffer .= $this->__createReport4Process($ProcessTree);

         // generate footer
         $Buffer .= $this->__generateFooter();

         // return report
         return $Buffer;

       // end function
      }


      /**
      *  @private
      *
      *  Generates the report for one single process.
      *
      *  @param benchmarkProcess $process the current process
      *  @return string $report4Line the report for the current process
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 01.01.2007<br />
      */
      private function __createReport4Process(&$process){

         $buffer = (string)'';
         $buffer .= $this->__generateReportLine($process->getProcessName(),$process->getProcessLevel(),$process->getProcessRuntime());

         // display children
         if($process->hasChildProcesses()){

            $processChildren = $process->getProcesses();

            foreach($processChildren as $Offset => $Child){
               $buffer .= $this->__createReport4Process($Child);
             // end foreach
            }

          // end if
         }

         return $buffer;

       // end function
      }


      /**
      *  @private
      *
      *  Generates the indent by the level provided.
      *
      *  @param int $level the level of the current process
      *  @return string $string the tab string
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 01.01.2007<br />
      */
      private function __generateTab($level){

         $string = (string)'';

         for($i = 0; $i < $level; $i++){
            $string .= str_repeat('&nbsp;',6);
          // end for
         }

         return $string;

       // end function
      }


      /**
      *  @private
      *
      *  Generates the header.
      *
      *  @return string $buffer the report header
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 01.01.2007<br />
      *  Version 0.2, 24.06.2007 (Text align is set to left)<br />
      */
      private function __generateHeader(){

         $buffer = (string)'';
         $buffer .= '<div style="width: 100%; background-color: white; border: 1px dashed black; margin-top: 10px; padding: 10px; font-size: 12px; font-family: Arial, Helvetica, sans-serif; text-align: left;">';
         $buffer .= "\n";
         $buffer .= '  <font style="font-size: 16px; font-variant: small-caps;">Benchmark - Report:</font>';
         $buffer .= "\n";
         $buffer .= '  <br />';
         $buffer .= "\n";
         $buffer .= '  <br />';
         $buffer .= "\n";
         $buffer .= '  <br />';
         $buffer .= "\n";
         $buffer .= '  <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; border-bottom: 1px solid black; background-color: #dddddd; font-size: 12px; font-family: Arial, Helvetica, sans-serif; padding: 2px;">';
         $buffer .= "\n";
         $buffer .= '    <tr>';
         $buffer .= "\n";
         $buffer .= '      <td style="width: 80%;">';
         $buffer .= "\n";
         $buffer .= '        <strong>Processtree</strong>';
         $buffer .= "\n";
         $buffer .= '      </td>';
         $buffer .= "\n";
         $buffer .= '      <td style="width: 20%; text-align: right; padding-right: 75px;">';
         $buffer .= "\n";
         $buffer .= '        <strong>Time</strong>';
         $buffer .= "\n";
         $buffer .= '      </td>';
         $buffer .= '    <tr>';
         $buffer .= "\n";
         return $buffer;

       // end function
      }


      /**
      *  @private
      *
      *  Generates the footer.
      *
      *  @return string $buffer the footer
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 01.01.2007<br />
      */
      private function __generateFooter(){

         $buffer = (string)'</div>';
         $buffer .= "\n";
         return $buffer;

       // end function
      }


      /**
      *  @private
      *
      *  Generiert eine Zeile in der Prozes-Übersicht.<br />
      *
      *  @param string $name name of the process
      *  @param string $level level of the process
      *  @param string $time runtime of the process
      *  @return string $reportLine one line within the process view
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.01.2007<br />
      *  Version 0.2, 08.03.2008 (Changed labels)<br />
      */
      private function __generateReportLine($name,$level,$time){

         // generate display name
         $name = $this->__generateTab($level).'&#187&nbsp;&nbsp;&nbsp;'.$name;
         $buffer = (string)'';

         if(($this->__LineCounter % 2) == 0 ){
            $buffer .= '  <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-size: 12px; font-family: Arial, Helvetica, sans-serif;">';
          // end if
         }
         else{
            $buffer .= '  <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; background-color: #eeeeee; font-size: 12px; font-family: Arial, Helvetica, sans-serif;">';
          // end else
         }

         $buffer .= "\n";
         $buffer .= '    <tr>';
         $buffer .= "\n";
         $buffer .= '      <td style="width: 80%">';
         $buffer .= "\n";
         $buffer .= '        '.$name;
         $buffer .= "\n";
         $buffer .= '      </td>';
         $buffer .= "\n";
         $buffer .= '      <td style="width: 20%; text-align: right;">';
         $buffer .= "\n";

         if($time > $this->__CriticalTime){
            $buffer .= '        <font style="color: red; font-weight: bold;">'.trim($time).'&nbsp;s&nbsp;</font>';
          // end if
         }
         else{
            $buffer .= '        <font style="color: green; font-weight: bold;">'.trim($time).'&nbsp;s&nbsp;</font>';
          // end else
         }

         $buffer .= "\n";
         $buffer .= '      </td>';
         $buffer .= "\n";
         $buffer .= '    </tr>';
         $buffer .= "\n";
         $buffer .= '  </table>';
         $buffer .= "\n";

         $this->__LineCounter++;
         return $buffer;

       // end function
      }

    // end class
   }


   /**
   *  @namespace core::benchmark
   *  @class benchmarkProcess
   *
   *  Represents a benchmark process node within the benchmark tree.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 31.12.2006<br />
   */
   class benchmarkProcess
   {

      /**
      *  @private
      *  ID of the process.
      */
      private $__ProcessID;

      /**
      *  @private
      *  Name of the process.
      */
      private $__ProcessName;

      /**
      *  @private
      *  Level of the process.
      */
      private $__ProcessLevel;

      /**
      *  @private
      *  Start time of the process.
      */
      private $__ProcessStartTime = null;

      /**
      *  @private
      *  Stop time of the process.
      */
      private $__ProcessStopTime = null;

      /**
      *  @private
      *  Reference on the process' parent.
      */
      private $__ParentProcess = null;

      /**
      *  @private
      *  List of child processes.
      */
      private $__Processes = array();


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


      /**
      *  @public
      *
      *  Returns the process' runtime.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 31.12.2006<br />
      */
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