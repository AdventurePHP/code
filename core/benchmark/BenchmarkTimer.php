<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   /**
    * @package core::benchmark
    * @class BenchmarkTimer
    *
    * This class implements the benchmark timer used for measurement of the core components
    * and your software. Must be used as a singleton to guarantee, that all benchmark tags
    * are included within the report. Usage (for each time!):
    * <pre>
    * $t = &Singleton::getInstance('BenchmarkTimer');
    * $t->start('my_tag');
    * ...
    * $t->stop('my_tag');
    * </pre>
    * In order to create a benchmark report (typically at the end of your bootstrap file,
    * please note the following:
    * <pre>
    * $t = &Singleton::getInstance('BenchmarkTimer');
    * echo $t->createReport();
    * </pre>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 31.12.2006<br />
    * Version 0.2, 01.01.2007<br />
    * Version 0.3, 29.12.2009 (Refeactoring due to new HTML markup for the process report.)<br />
    */
   final class BenchmarkTimer {

      /**
       * @private
       * @var BenchmarkProcess The benchmark root process.
       */
      private $__RootProcess = null;

      /**
       * @private
       * @var BenchmarkProcess[] The process table, that contains all running processes (hash table).
       */
      private $__RunningProcesses = array();

      /**
       * @private
       * @var BenchmarkProcess References the current parent process (=last process created).
       */
      private $__CurrentParent = null;

      /**
       * @private
       * @var int Stores the process count.
       */
      private $__CurrentProcessID = 0;

      /**
       * @private
       * @var float Defines the critical time for the benchmark report.
       */
      private $__CriticalTime = 0.5;

      /**
       * @private
       * @var int Line counter for the report.
       */
      private $__LineCounter = 0;

      private static $NEWLINE = "\n";

      /**
       * @public
       *
       * Constructor of the BenchmarkTimer. Initializes the root process.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      public function BenchmarkTimer(){
         $rootProcess = &$this->__createRootProcess();
         $this->__addRunningProcess($rootProcess);
         $this->__setCurrentParent($rootProcess);
       // end function
      }

      /**
       * @public
       *
       * Sets the critical time. If the critical time is reached, the time is printed in red digits.
       *
       * @param float $time the critical time in seconds.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      public function setCriticalTime($time){
         $this->__CriticalTime = $time;
       // end function
      }

      /**
       * @public
       *
       * Returns the critical time.
       *
       * @return float The critical time.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      public function getCriticalTime(){
         return $this->__CriticalTime;
       // end function
      }

      /**
       * @public
       *
       * This method is used to starts a new benchmark timer.
       *
       * @param string $name The (unique!) name of the benchmark tag.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      public function start($name = null){

         $startTime = $this->__generateMicroTime();

         if($name === null){
            trigger_error('[BenchmarkTimer::start()] Required parameter name is not set!');
          // end if
         }

         if($this->__getRunningProcessByName($name) !== null){
            trigger_error('[BenchmarkTimer::start()] Benchmark process with name "'.$name
                    .'" is already running! Use a different one!');
          // end if
         }
         else{

            $parent = &$this->__getCurrentParent();
            $process = $this->__createProcess($name,$startTime,$parent);
            $newProcess = &$process; // note process as reference to have the same process instance!
            $parent->appendProcess($newProcess);
            $this->__setCurrentParent($newProcess);
            $this->__addRunningProcess($newProcess);

          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Stops the benchmark timer, started with start().
       *
       * @param string $name The (unique!) name of the benchmark tag.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      public function stop($name){

         $stopTime = $this->__generateMicroTime();

         if(isset($this->__RunningProcesses[$name])){
            $currentProcess = &$this->__getRunningProcessByName($name);
            $currentProcess->setProcessStopTime($stopTime);
            $this->__setCurrentParent($currentProcess->getParentProcess());
            $this->__removeRunningProcess($name);
          // end if
         }
         else{
            trigger_error('[BenchmarkTimer::stop()] Process with name "'.$name
                    .'" is not running yet!');
          // end else
         }

       // end function
      }

      /**
       * @private
       *
       * Returns the id of the next process.
       *
       * @return int The next internal process id.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      private function __getID(){
         $this->__CurrentProcessID += 1;
         return $this->__CurrentProcessID;
       // end function
      }

      /**
       * @private
       *
       * Returns the current timestamp in milliseconds.
       *
       * @return string Current timestamp in milliseconds.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
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
       * @private
       *
       * Creates the process and returns it.
       *
       * @param string $name the name of the process.
       * @param string $startTime the start timestamp.
       * @param BenchmarkProcess $parent reference on the parent object.
       * @return BenchmarkProcess The process itself.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      private function __createProcess($name,$startTime,&$parent){

         $process = new BenchmarkProcess();
         $process->setProcessID($this->__getID());
         $process->setProcessName($name);
         $process->setProcessLevel($parent->getProcessLevel() + 1);
         $process->setProcessStartTime($startTime);
         $process->setParentProcess($parent);
         return $process;

       // end function
      }

      /**
       * @private
       *
       * Creates the root process and returns it.
       *
       * @return BenchmarkProcess The root process.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      private function &__createRootProcess(){

         $startTime = $this->__generateMicroTime();
         $rootProcess = new BenchmarkProcess();
         $rootProcess->setProcessID($this->__getID());
         $rootProcess->setProcessName(get_class($this));
         $rootProcess->setProcessLevel(0);
         $rootProcess->setProcessStartTime($startTime);
         $this->__RootProcess = &$rootProcess;
         return $rootProcess;

       // end function
      }

      /**
       * @private
       *
       * Stopps the root process and returns it.
       *
       * @return BenchmarkProcess The stopped root process.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      private function &__getRootProcess(){

         $rootProcess = &$this->__RootProcess;
         $rootProcess->setProcessStopTime($this->__generateMicroTime());
         return $rootProcess;

       // end function
      }

      /**
       * @private
       *
       * Adds a process to the list of running processes.
       *
       * @param BenchmarkProcess $process A reference on the running process.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      private function __addRunningProcess(&$process){
         $name = $process->getProcessName();
         $this->__RunningProcesses[$name] = &$process;
       // end function
      }

      /**
       * @private
       *
       * Deletes a running process from the hash table.
       *
       * @param string $name The name of the process.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      private function __removeRunningProcess($name){
         unset($this->__RunningProcesses[$name]);
       // end function
      }

      /**
       * @private
       *
       * Returns a running process by it's name.
       *
       * @param string $name Name of the desired process.
       * @return BenchmarkProcess Null or the desired object reference.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
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
       * @private
       *
       * References the currently created process.
       *
       * @param BenchmarkProcess $process The reference on the desired benchmark process.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      private function __setCurrentParent(&$process){
         $this->__CurrentParent = &$process;
       // end function
       }

      /**
       * @private
       *
       * Returns the currently created process.
       *
       * @return BenchmarkProcess The reference on the desired benchmark process.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      private function &__getCurrentParent(){
         return $this->__CurrentParent;
       // end function
      }

      /**
       * @public
       *
       * Generates the report of the recorded benchmark tags.
       *
       * @return string The HTML source code of the benchmark.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      public function createReport(){

         // get process tree
         $processTree = $this->__getRootProcess();

         // initialize buffer
         $buffer = (string)'';

         // generate header
         $buffer .= $this->__generateHeader();

         // generate report recursivly
         $buffer .= $this->__createReport4Process($processTree);

         // generate footer
         $buffer .= $this->__generateFooter();

         // return report
         return $buffer;

       // end function
      }

      /**
       * @private
       *
       * Marks classes to format the process run time with.
       *
       * @param float $time The process run time.
       * @return string The markup of the process time.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2009<br />
       */
      private function getMarkedUpProcessTimeClass($time){

         $class = (string)'';
         if($time > $this->__CriticalTime){
            $class .= 'warn';
         }
         else{
            $class .= 'ok';
         }
         return $class;

       // end function
      }

      /**
       * @private
       *
       * Generates the report for one single process.
       *
       * @param BenchmarkProcess $process the current process.
       * @return string The report for the current process.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 01.01.2007<br />
       * Version 0.2, 29.12.2009 (Refactored markup)<br />
       */
      private function __createReport4Process(&$process){

         $buffer = (string)'';
         $level = $process->getProcessLevel();

         // add closing dl only if level greater than 0 to have
         // correct definition list leveling!
         if($level > 0){
            $buffer = (string)'<dl>';
         }

         // assemble class for the current line
         $class = (string)'';
         if(($this->__LineCounter % 2) == 0 ){
            $class .= 'even';
          // end if
         }
         else{
            $class .= 'odd';
          // end else
         }

         // increment the line counter to be able to distinguish between even and odd lines
         $this->__LineCounter++;
         
         $buffer .= self::$NEWLINE;
         $buffer .= '    <dt class="'.$class.'">'.$process->getProcessName().'</dt>';
         $buffer .= self::$NEWLINE;

         // add specific run time class to mark run times greater that the critical time
         $time = $process->getProcessRuntime();
         $buffer .= '    <dd class="'.$class.' '
            .$this->getMarkedUpProcessTimeClass($time).'">'.$time.' s';

         // display children
         if($process->hasChildProcesses()){

            $processChildren = $process->getProcesses();
            foreach($processChildren as $offset => $child){
               $buffer .= self::$NEWLINE;
               $buffer .= $this->__createReport4Process($child);
             // end foreach
            }

            $buffer .= self::$NEWLINE;

          // end if
         }

         $buffer .= '</dd>';
         $buffer .= self::$NEWLINE;

         // add closing dl only if level greater than 0 to have
         // correct definition list leveling!
         if($level > 0){
            $buffer .= '</dl>';
         }
         
         return $buffer;

       // end function
      }

      /**
       * @private
       *
       * Generates the header.
       *
       * @return string The report header.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 01.01.2007<br />
       * Version 0.2, 24.06.2007 (Text align is set to left)<br />
       * Version 0.3, 29.12.2009 (Refactored markup)<br />
       */
      private function __generateHeader(){

         $buffer = (string)'';
         $buffer .= self::$NEWLINE;
         $buffer .= '<style type="text/css">
#APF-Benchmark-Report {
   font-size: 0.8em;
   padding: 0.4em;
   background-color: #fff;
}
#APF-Benchmark-Report .even {
   background-color: #fff;
}
#APF-Benchmark-Report .odd {
   background-color: #ccc;
}
#APF-Benchmark-Report .ok, #APF-Benchmark-Report .warn {
   color: #080;
   font-weight: bold;
}
#APF-Benchmark-Report .warn {
   color: #f00;
}
#APF-Benchmark-Report .header {
   border-bottom: 1px solid #ccc;
   border-top: 1px solid #ccc;
   font-weight: bold;
}
#APF-Benchmark-Report .header:before {
    content: \'\';
}
#APF-Benchmark-Report dl {
   margin: 0;
   border-left: 1px solid #ccc;
   color: #000;
   font-size: 1em;
   font-weight: normal;
   line-height: 1.5em;
   margin: 0 0 0 2em;
}
#APF-Benchmark-Report > dl {
   margin: 0;
   border-right: 1px solid #ccc;
}
#APF-Benchmark-Report .odd  > dl {
   border-left: 1px solid #fff;
}
#APF-Benchmark-Report dt {
   float: left;
   line-height: 1.5em;
}
#APF-Benchmark-Report dt:before {
   color: #666;
   content: \'» \';
   padding: 0 0.3em;
}
#APF-Benchmark-Report dd {
   text-align: right;
}
#APF-Benchmark-Report:after {
   clear: both;
   content: " ";
   display: block;
   visibility: hidden;
   }
</style>';
         $buffer .= self::$NEWLINE;
         $buffer .= '<div id="APF-Benchmark-Report">';
         $buffer .= self::$NEWLINE;
         $buffer .= '  <h2>Benchmark report</h2>';
         $buffer .= self::$NEWLINE;
         $buffer .= '  <dl>';
         $buffer .= self::$NEWLINE;
         $buffer .= '    <dt class="header">Processtree</dt>';
         $buffer .= self::$NEWLINE;
         $buffer .= '    <dd class="header">Time</dd>';
         return $buffer;

       // end function
      }

      /**
       * @private
       *
       * Generates the footer.
       *
       * @return string The footer.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 01.01.2007<br />
       * Version 0.2, 29.12.2009 (Refactored markup)<br />
       */
      private function __generateFooter(){

         $buffer = (string)'';
         $buffer .= '</dl>';
         $buffer .= self::$NEWLINE;
         $buffer .= '</div>';
         return $buffer;

       // end function
      }

    // end class
   }

   /**
    * @package core::benchmark
    * @class BenchmarkProcess
    *
    * Represents a benchmark process node within the benchmark tree.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   final class BenchmarkProcess {

      /**
       * @private
       * @var int ID of the process.
       */
      private $__ProcessID;

      /**
       * @private
       * @var string Name of the process.
       */
      private $__ProcessName;

      /**
       * @private
       * @var int Level of the process.
       */
      private $__ProcessLevel;

      /**
       * @private
       * @var int Start time of the process.
       */
      private $__ProcessStartTime = null;

      /**
       * @private
       * @var int Stop time of the process.
       */
      private $__ProcessStopTime = null;

      /**
       * @private
       * @var BenchmarkProcess Reference on the process' parent.
       */
      private $__ParentProcess = null;

      /**
       * @private
       * @var BenchmarkProcess[] List of child processes.
       */
      private $__Processes = array();

      public function BenchmarkProcess(){
      }

      public function setProcessID($id){
         $this->__ProcessID = $id;
       // end function
      }

      public function getProcessID(){
         return $this->__ProcessID;
       // end function
      }

      public function setProcessName($name){
         $this->__ProcessName = $name;
       // end function
      }

      public function getProcessName(){
         return $this->__ProcessName;
       // end function
      }

      public function setProcessLevel($level){
         $this->__ProcessLevel = $level;
       // end function
      }

      public function getProcessLevel(){
         return $this->__ProcessLevel;
       // end function
      }

      public function setProcessStartTime($startTime){
         $this->__ProcessStartTime = $startTime;
       // end function
      }

      public function getProcessStartTime(){
         return $this->__ProcessStartTime;
       // end function
      }

      public function setProcessStopTime($stopTime){
         $this->__ProcessStopTime = $stopTime;
       // end function
      }

      public function getProcessStopTime(){
         return $this->__ProcessStopTime;
       // end function
      }

      public function setParentProcess(&$process){
         $this->__ParentProcess = &$process;
       // end function
      }

      public function &getParentProcess(){
         return $this->__ParentProcess;
       // end function
      }

      public function appendProcess(&$process){
         $ProcessID = $process->getProcessID();
         $this->__Processes[$ProcessID] = &$process;
       // end function
      }

      public function getProcesses(){
         return $this->__Processes;

       // end function
      }

      public function hasChildProcesses(){

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
       * @public
       *
       * Returns the process' runtime.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 31.12.2006<br />
       */
      public function getProcessRuntime(){

         if($this->__ProcessStopTime == null){
            return '--------------------';
         }
         return number_format($this->__ProcessStopTime - $this->__ProcessStartTime,10);

       // end function
      }

    // end class
   }
?>