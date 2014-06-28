<?php
namespace APF\core\benchmark;

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
 * Represents a benchmark process node within the benchmark tree.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 31.12.2006<br />
 */
final class BenchmarkProcess {

   /**
    * ID of the process.
    *
    * @var int $processId
    */
   private $processId;

   /**
    * Name of the process.
    *
    * @var string $processName
    */
   private $processName;

   /**
    * Level of the process.
    *
    * @var int $processLevel
    */
   private $processLevel;

   /**
    * Start time of the process.
    *
    * @var int $processStartTime
    */
   private $processStartTime = null;

   /**
    * Stop time of the process.
    *
    * @var int $processStopTime
    */
   private $processStopTime = null;

   /**
    * Reference on the process' parent.
    *
    * @var BenchmarkProcess $parentProcess
    */
   private $parentProcess = null;

   /**
    * List of child processes.
    *
    * @var BenchmarkProcess[] $processes
    */
   private $processes = array();

   public function setProcessId($id) {
      $this->processId = $id;
   }

   public function getProcessId() {
      return $this->processId;
   }

   public function setProcessName($name) {
      $this->processName = $name;
   }

   public function getProcessName() {
      return $this->processName;
   }

   public function setProcessLevel($level) {
      $this->processLevel = $level;
   }

   public function getProcessLevel() {
      return $this->processLevel;
   }

   public function setProcessStartTime($startTime) {
      $this->processStartTime = $startTime;
   }

   public function getProcessStartTime() {
      return $this->processStartTime;
   }

   public function setProcessStopTime($stopTime) {
      $this->processStopTime = $stopTime;
   }

   public function getProcessStopTime() {
      return $this->processStopTime;
   }

   public function setParentProcess(BenchmarkProcess &$process) {
      $this->parentProcess = & $process;
   }

   public function &getParentProcess() {
      return $this->parentProcess;
   }

   public function appendProcess(BenchmarkProcess &$process) {
      $processId = $process->getProcessID();
      $this->processes[$processId] = $process;
   }

   public function getProcesses() {
      return $this->processes;
   }

   public function hasChildProcesses() {
      return count($this->processes) > 0;
   }

   /**
    * Returns the process' runtime.
    *
    * @return string The runtime of the process in seconds.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function getProcessRuntime() {
      if ($this->processStopTime == null) {
         return '--------------------';
      }

      return number_format($this->processStopTime - $this->processStartTime, 10);
   }

}
