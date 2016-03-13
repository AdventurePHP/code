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
 * $t = Singleton::getInstance(StopWatch::class);
 * $t->start('my_tag');
 * ...
 * $t->stop('my_tag');
 * </pre>
 * In order to create a benchmark report (typically at the end of your bootstrap file,
 * please note the following:
 * <pre>
 * $t = Singleton::getInstance(StopWatch::class);
 * echo $t->createReport();
 * </pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 31.12.2006<br />
 * Version 0.2, 01.01.2007<br />
 * Version 0.3, 29.12.2009 (Refactoring due to new HTML markup for the process report.)<br />
 */
final class StopWatch {

   private $runningDepth = 0;

   /**
    * @var Process[]
    */
   private $processes = [];

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
      $this->start('Root');
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

      $this->processes[$name] = new Process($name, $this->runningDepth++);
      $this->processes[$name]->start();
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

      if (!isset($this->processes[$name])) {
         throw new InvalidArgumentException('Process with name "' . $name . '" is not running!');
      }

      $this->processes[$name]->stop();
      $this->runningDepth--;
   }

   /**
    * Generates the report of the recorded benchmark tags.
    *
    * @param Report $report Custom report format if desired (default: HtmlReport).
    *
    * @return string The HTML source code of the benchmark.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function createReport(Report $report = null) {

      // return, if benchmarker is disabled
      if ($this->enabled === false) {
         return 'Benchmarker is currently disabled. To generate a detailed report, please '
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
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   private function &getRootProcess() {

      $rootProcess = &$this->processes['Root'];
      $rootProcess->stop();

      return $rootProcess;
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
      return $this->getRootProcess()->getDuration();
   }

}
