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
namespace APF\core\benchmark;

use InvalidArgumentException;

/**
 * This class implements the benchmark timer used for measurement of the core components
 * and your software. Must be used as a singleton to guarantee, that all benchmark tags
 * are included within the report. Usage (for each time!):
 * <pre>
 * $t = &Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
 * $t->start('my_tag');
 * ...
 * $t->stop('my_tag');
 * </pre>
 * In order to create a benchmark report (typically at the end of your bootstrap file,
 * please note the following:
 * <pre>
 * $t = &Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
 * echo $t->createReport();
 * </pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 31.12.2006<br />
 * Version 0.2, 01.01.2007<br />
 * Version 0.3, 29.12.2009 (Refactoring due to new HTML markup for the process report.)<br />
 */
final class BenchmarkTimer {

   /**
    * The benchmark root process.
    *
    * @var BenchmarkProcess $rootProcess
    */
   private $rootProcess = null;

   /**
    * The process table, that contains all running processes (hash table).
    *
    * @var BenchmarkProcess[] $runningProcesses
    */
   private $runningProcesses = array();

   /**
    * References the current parent process (=last process created).
    *
    * @var BenchmarkProcess $currentParent
    */
   private $currentParent = null;

   /**
    * Stores the process count.
    *
    * @var int $currentProcessId
    */
   private $currentProcessId = 0;

   /**
    * Defines the critical time for the benchmark report.
    *
    * @var float $criticalTime
    */
   private $criticalTime = 0.5;

   /**
    * Line counter for the report.
    *
    * @var int $lineCounter
    */
   private $lineCounter = 0;

   private static $NEWLINE = PHP_EOL;

   /**
    * Indicator, that defines, if the benchmarker is enabled or not (for performance reasons!)
    * <em>true</em> in case, the benchmarker is enabled, <em>false</em> otherwise.
    *
    * @var boolean $enabled
    */
   private $enabled = true;

   /**
    * Constructor of the BenchmarkTimer. Initializes the root process.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function __construct() {
      $rootProcess = & $this->createRootProcess();
      $this->addRunningProcess($rootProcess);
      $this->setCurrentParent($rootProcess);
   }

   /**
    * Enables the benchmarker for measurement of the predefined points.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.01.2010<br />
    */
   public function enable() {
      $this->enabled = true;
   }

   /**
    * Disables the benchmarker for measurement of the predefined points. This is often
    * important for performance reasons, because release 1.11 introduced onParseTime()
    * measurement, that could probably decrease the APF's performance!
    * <p />
    * Experiential tests proofed, that disabling the benchmarker can increase performance
    * from ~0.185s to ~0.138s, what is ~25%!
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.01.2010<br />
    */
   public function disable() {
      $this->enabled = false;
   }

   /**
    * Sets the critical time. If the critical time is reached, the time is printed in red digits.
    *
    * @param float $time the critical time in seconds.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function setCriticalTime($time) {
      $this->criticalTime = $time;
   }

   /**
    * Returns the critical time.
    *
    * @return float The critical time.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function getCriticalTime() {
      return $this->criticalTime;
   }

   /**
    * This method is used to starts a new benchmark timer.
    *
    * @param string $name The (unique!) name of the benchmark tag.
    *
    * @throws InvalidArgumentException In case the given name is null.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function start($name = null) {

      // return, if benchmarker is disabled
      if ($this->enabled === false) {
         return;
      }

      $startTime = $this->generateMicroTime();

      if ($name === null) {
         throw new InvalidArgumentException('[BenchmarkTimer::start()] Required parameter name is not set!');
      }

      if ($this->getRunningProcessByName($name) === null) {

         $parent = & $this->getCurrentParent();
         $process = $this->createProcess($name, $startTime, $parent);
         $newProcess = & $process; // note process as reference to have the same process instance!
         $parent->appendProcess($newProcess);
         $this->setCurrentParent($newProcess);
         $this->addRunningProcess($newProcess);
      } else {
         throw new InvalidArgumentException('[BenchmarkTimer::start()] Benchmark process with name "' . $name
               . '" is already running! Use a different one!');
      }

   }

   /**
    * Stops the benchmark timer, started with start().
    *
    * @param string $name The (unique!) name of the benchmark tag.
    *
    * @throws InvalidArgumentException In case the named process is not running.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function stop($name) {

      // return, if benchmarker is disabled
      if ($this->enabled === false) {
         return;
      }

      $stopTime = $this->generateMicroTime();

      if (isset($this->runningProcesses[$name])) {
         $currentProcess = & $this->getRunningProcessByName($name);
         $currentProcess->setProcessStopTime($stopTime);
         $this->setCurrentParent($currentProcess->getParentProcess());
         $this->removeRunningProcess($name);
      } else {
         throw new InvalidArgumentException('[BenchmarkTimer::stop()] Process with name "' . $name
               . '" is not running yet!');
      }

   }

   /**
    * Returns the id of the next process.
    *
    * @return int The next internal process id.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function getID() {
      $this->currentProcessId += 1;

      return $this->currentProcessId;
   }

   /**
    * Returns the current timestamp in milliseconds.
    *
    * @return string Current timestamp in milliseconds.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function generateMicroTime() {
      return microtime(true);
   }

   /**
    * Creates the process and returns it.
    *
    * @param string $name the name of the process.
    * @param string $startTime the start timestamp.
    * @param BenchmarkProcess $parent reference on the parent object.
    *
    * @return BenchmarkProcess The process itself.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function createProcess($name, $startTime, &$parent) {

      $process = new BenchmarkProcess();
      $process->setProcessId($this->getID());
      $process->setProcessName($name);
      $process->setProcessLevel($parent->getProcessLevel() + 1);
      $process->setProcessStartTime($startTime);
      $process->setParentProcess($parent);

      return $process;

   }

   /**
    * Creates the root process and returns it.
    *
    * @return BenchmarkProcess The root process.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function &createRootProcess() {

      $startTime = $this->generateMicroTime();
      $rootProcess = new BenchmarkProcess();
      $rootProcess->setProcessId($this->getID());
      $rootProcess->setProcessName('Root');
      $rootProcess->setProcessLevel(0);
      $rootProcess->setProcessStartTime($startTime);
      $this->rootProcess = & $rootProcess;

      return $rootProcess;

   }

   /**
    * Stops the root process and returns it.
    *
    * @return BenchmarkProcess The stopped root process.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function &getRootProcess() {

      $rootProcess = & $this->rootProcess;
      $rootProcess->setProcessStopTime($this->generateMicroTime());

      return $rootProcess;

   }

   /**
    * Adds a process to the list of running processes.
    *
    * @param BenchmarkProcess $process A reference on the running process.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function addRunningProcess(&$process) {
      $name = $process->getProcessName();
      $this->runningProcesses[$name] = $process;
   }

   /**
    * Deletes a running process from the hash table.
    *
    * @param string $name The name of the process.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function removeRunningProcess($name) {
      unset($this->runningProcesses[$name]);
   }

   /**
    * Returns a running process by it's name.
    *
    * @param string $name Name of the desired process.
    *
    * @return BenchmarkProcess Null or the desired object reference.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function &getRunningProcessByName($name) {

      if (isset($this->runningProcesses[$name])) {
         return $this->runningProcesses[$name];
      } else {
         $return = null;

         return $return;
      }

   }

   /**
    * References the currently created process.
    *
    * @param BenchmarkProcess $process The reference on the desired benchmark process.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function setCurrentParent(&$process) {
      $this->currentParent = & $process;
   }

   /**
    * Returns the currently created process.
    *
    * @return BenchmarkProcess The reference on the desired benchmark process.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function &getCurrentParent() {
      return $this->currentParent;
   }

   /**
    * Generates the report of the recorded benchmark tags.
    *
    * @return string The HTML source code of the benchmark.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function createReport() {

      // return, if benchmarker is disabled
      if ($this->enabled === false) {
         return 'Benchmarker is currently disabled. To generate a detailed report, please '
         . 'enable it calling <em>$t = &Singleton::getInstance(\'BenchmarkTimer\'); '
         . '$t->enable();</em>!';
      }

      // get process tree
      $processTree = $this->getRootProcess();

      // initialize buffer
      $buffer = (string) '';

      // generate header
      $buffer .= $this->generateHeader();

      // generate report recursively
      $buffer .= $this->createReport4Process($processTree);

      // generate footer
      $buffer .= $this->generateFooter();

      return $buffer;
   }

   /**
    * Marks classes to format the process run time with.
    *
    * @param float $time The process run time.
    *
    * @return string The markup of the process time.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2009<br />
    */
   private function getMarkedUpProcessTimeClass($time) {

      $class = (string) '';
      if ($time > $this->criticalTime) {
         $class .= 'warn';
      } else {
         $class .= 'ok';
      }

      return $class;

   }

   /**
    * Generates the report for one single process.
    *
    * @param BenchmarkProcess $process the current process.
    *
    * @return string The report for the current process.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 01.01.2007<br />
    * Version 0.2, 29.12.2009 (Refactored markup)<br />
    */
   private function createReport4Process(&$process) {

      $buffer = (string) '';
      $level = $process->getProcessLevel();

      // add closing dl only if level greater than 0 to have
      // correct definition list leveling!
      if ($level > 0) {
         $buffer = (string) '<dl>';
      }

      // assemble class for the current line
      $class = (string) '';
      if (($this->lineCounter % 2) == 0) {
         $class .= 'even';
      } else {
         $class .= 'odd';
      }

      // increment the line counter to be able to distinguish between even and odd lines
      $this->lineCounter++;

      $buffer .= self::$NEWLINE;
      $buffer .= '    <dt class="' . $class . '">' . $process->getProcessName() . '</dt>';
      $buffer .= self::$NEWLINE;

      // add specific run time class to mark run times greater that the critical time
      $time = $process->getProcessRuntime();
      $buffer .= '    <dd class="' . $class . ' '
            . $this->getMarkedUpProcessTimeClass($time) . '">' . $time . ' s';

      // display children
      if ($process->hasChildProcesses()) {

         $processChildren = $process->getProcesses();
         foreach ($processChildren as $child) {
            $buffer .= self::$NEWLINE;
            $buffer .= $this->createReport4Process($child);
         }

         $buffer .= self::$NEWLINE;

      }

      $buffer .= '</dd>';
      $buffer .= self::$NEWLINE;

      // add closing dl only if level greater than 0 to have
      // correct definition list leveling!
      if ($level > 0) {
         $buffer .= '</dl>';
      }

      return $buffer;

   }

   /**
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
   private function generateHeader() {

      $buffer = (string) '';
      $buffer .= self::$NEWLINE;
      $buffer .= '<style type="text/css">
#APF-Benchmark-Report {
   font-size: 10px !important;
   padding: 0.3em;
   background-color: #fff;
   font-family: Arial, Helvetica, sans-serif;
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

   }

   /**
    * Generates the footer.
    *
    * @return string The footer.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 01.01.2007<br />
    * Version 0.2, 29.12.2009 (Refactored markup)<br />
    */
   private function generateFooter() {

      $buffer = (string) '';
      $buffer .= '</dl>';
      $buffer .= self::$NEWLINE;
      $buffer .= '</div>';

      return $buffer;
   }

   /**
    * Returns the total process time recorded until the call to this method.
    * <p/>
    * You may use this method to add the total rendering time of an APF-based
    * application to your source code or any proprietary HTTP header.
    *
    * @return string The total processing time.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.04.2012<br />
    */
   public function getTotalTime() {
      return $this->getRootProcess()->getProcessRuntime();
   }
}
