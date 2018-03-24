<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
 * Implements APF's default stop watch. Records events as simple list with information
 * on the hierarchy level of each process.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 14.03.2016<br />
 */
class DefaultStopWatch implements StopWatch {

   /**
    * @var int The hierarchy level of processes running.
    */
   private $hierarchyLevel = 0;

   /**
    * @var Process[] List of processes recorded.
    */
   private $processes = [];

   /**
    * Indicates, whether or not the stop watch is active (<em>true</em>) or not (<em>false</em>).
    * Default is active.
    *
    * @var boolean $enabled
    */
   private $enabled = true;

   /**
    * Initializes the stop watch and starts the root process.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 14.03.2016<br />
    */
   public function __construct() {
      $this->start('Root');
   }

   public function start(string $name) {

      // do nothing in case disabled
      if ($this->enabled === false) {
         return;
      }

      $this->processes[$name] = new DefaultProcess($name, $this->hierarchyLevel++);
      $this->processes[$name]->start();
   }

   public function enable() {
      $this->enabled = true;
   }

   public function disable() {
      $this->enabled = false;
   }

   public function stop(string $name) {

      // do nothing in case disabled
      if ($this->enabled === false) {
         return;
      }

      if (!isset($this->processes[$name])) {
         throw new InvalidArgumentException('Process with name "' . $name . '" is not running!');
      }

      $this->processes[$name]->stop();
      $this->hierarchyLevel--;
   }

   public function createReport(Report $report = null) {

      // do nothing in case disabled
      if ($this->enabled === false) {
         return 'Stop watch is currently disabled. To generate a detailed report, please '
               . 'enable it calling <em>$t = Singleton::getInstance(BenchmarkTimer::class); '
               . '$t->enable();</em>!';
      }

      // Stop root process to be able to measure overall time accurately.
      // This needs to be done here as we don't have the chance to stop
      // it before, since we don't know if a stop() is the "last" stop.
      $this->getRootProcess()->stop();

      if ($report === null) {
         $report = new HtmlReport();
      }

      return $report->compile(array_values($this->processes));
   }

   /**
    * Stops the root process and returns it.
    *
    * @return Process The stopped root process.
    */
   private function getRootProcess() {

      $rootProcess = $this->processes['Root'];
      $rootProcess->stop();

      return $rootProcess;
   }

   public function getTotalTime() {
      return $this->getRootProcess()->getDuration();
   }

}
