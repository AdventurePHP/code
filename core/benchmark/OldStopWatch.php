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
 * Old implementation of the stop watch until release 3.1. New implementation DefaultStopWatch
 * has been introduced w/ release 3.2.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 31.12.2006<br />
 * Version 0.2, 01.01.2007<br />
 * Version 0.3, 29.12.2009 (Refactoring due to new HTML markup for the process report.)<br />
 */
class OldStopWatch implements StopWatch {

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
   private $runningProcesses = [];

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

   /**
    * Indicator, that defines, if the stop watch is enabled or not (for performance reasons!)
    * <em>true</em> in case, the stop watch is enabled, <em>false</em> otherwise.
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
      $rootProcess = $this->createRootProcess();
      $this->addRunningProcess($rootProcess);
      $this->setCurrentParent($rootProcess);
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
      $this->rootProcess = &$rootProcess;

      return $rootProcess;
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
    * References the currently created process.
    *
    * @param BenchmarkProcess $process The reference on the desired benchmark process.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function setCurrentParent(&$process) {
      $this->currentParent = &$process;
   }

   public function enable() {
      $this->enabled = true;
   }

   public function disable() {
      $this->enabled = false;
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

   public function start($name = null) {

      // return, if stop watch is disabled
      if ($this->enabled === false) {
         return;
      }

      $startTime = $this->generateMicroTime();

      if ($name === null) {
         throw new InvalidArgumentException('[BenchmarkTimer::start()] Required parameter name is not set!');
      }

      if ($this->getRunningProcessByName($name) === null) {

         $parent = &$this->getCurrentParent();
         $process = $this->createProcess($name, $startTime, $parent);
         $newProcess = &$process; // note process as reference to have the same process instance!
         $parent->appendProcess($newProcess);
         $this->setCurrentParent($newProcess);
         $this->addRunningProcess($newProcess);
      } else {
         throw new InvalidArgumentException('[BenchmarkTimer::start()] Benchmark process with name "' . $name
               . '" is already running! Use a different one!');
      }
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

   public function stop($name) {

      // return, if stop watch is disabled
      if ($this->enabled === false) {
         return;
      }

      $stopTime = $this->generateMicroTime();

      if (isset($this->runningProcesses[$name])) {
         $currentProcess = &$this->getRunningProcessByName($name);
         $currentProcess->setProcessStopTime($stopTime);
         $this->setCurrentParent($currentProcess->getParentProcess());
         $this->removeRunningProcess($name);
      } else {
         throw new InvalidArgumentException('[BenchmarkTimer::stop()] Process with name "' . $name
               . '" is not running yet!');
      }
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

   public function createReport(Report $report = null) {

      // return, if stop watch is disabled
      if ($this->enabled === false) {
         return 'Stop watch is currently disabled. To generate a detailed report, please '
         . 'enable it calling <em>$t = Singleton::getInstance(BenchmarkTimer::class); '
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
    * Stops the root process and returns it.
    *
    * @return BenchmarkProcess The stopped root process.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function &getRootProcess() {
      $rootProcess = &$this->rootProcess;
      $rootProcess->setProcessStopTime($this->generateMicroTime());

      return $rootProcess;
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
      $buffer .= PHP_EOL;
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
      $buffer .= PHP_EOL;
      $buffer .= '<div id="APF-Benchmark-Report">';
      $buffer .= PHP_EOL;
      $buffer .= '  <h2>Benchmark report</h2>';
      $buffer .= PHP_EOL;
      $buffer .= '  <dl>';
      $buffer .= PHP_EOL;
      $buffer .= '    <dt class="header">Processtree</dt>';
      $buffer .= PHP_EOL;
      $buffer .= '    <dd class="header">Time</dd>';

      return $buffer;
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

      $buffer .= PHP_EOL;
      $buffer .= '    <dt class="' . $class . '">' . $process->getProcessName() . '</dt>';
      $buffer .= PHP_EOL;

      // add specific run time class to mark run times greater that the critical time
      $time = $process->getProcessRuntime();
      $buffer .= '    <dd class="' . $class . ' '
            . $this->getMarkedUpProcessTimeClass($time) . '">' . $time . ' s';

      // display children
      if ($process->hasChildProcesses()) {

         $processChildren = $process->getProcesses();
         foreach ($processChildren as $child) {
            $buffer .= PHP_EOL;
            $buffer .= $this->createReport4Process($child);
         }

         $buffer .= PHP_EOL;

      }

      $buffer .= '</dd>';
      $buffer .= PHP_EOL;

      // add closing dl only if level greater than 0 to have
      // correct definition list leveling!
      if ($level > 0) {
         $buffer .= '</dl>';
      }

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
      $buffer .= PHP_EOL;
      $buffer .= '</div>';

      return $buffer;
   }

   public function getTotalTime() {
      return $this->getRootProcess()->getProcessRuntime();
   }

}
