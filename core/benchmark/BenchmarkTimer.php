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

/**
 * This class implements a benchmark tool to measure execution speed of core components
 * of the framework as well as parts of your software. Must be used as a singleton to
 * guarantee, that all events are captured within the report. Usage (for each time!):
 * <pre>
 * $t = Singleton::getInstance(BenchmarkTimer::class);
 * $t->start('my_tag');
 * ...
 * $t->stop('my_tag');
 * </pre>
 * In order to create a benchmark report (typically at the end of your bootstrap file,
 * please note the following:
 * <pre>
 * $t = Singleton::getInstance(BenchmarkTimer::class);
 * echo $t->createReport();
 * </pre>
 * Based on interface Report you can generate custom reports (e.g. aggregate certain events).
 * To run a custom report, please specify the desired report implementation when calling
 * createReport():
 * <pre>
 * echo $t->createReport(\My\Custom\Report::class);
 * </pre>
 * For special cases, the stop watch implementation shipped with the APF can be adapted and/or
 * exchanged on a configuration basis. For this reason, implement interface StopWatch and
 * instruct the BenchmarkTimer to use it by:
 * <pre>
 * BenchmarkTimer::$watchClass = \My\Custom\StopWatch::class;
 * </pre>
 * Please ensure that configuration takes place in your bootstrap file before any other
 * application component using the BenchmarkTimer is executed.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 31.12.2006<br />
 * Version 0.2, 01.01.2007<br />
 * Version 0.3, 29.12.2009 (Refactoring due to new HTML markup for the process report.)<br />
 * Version 0.4, 14.03.2016 (ID#214: extracted stop watch functionality, report generation, and improved performance by ~50%)<br />
 */
final class BenchmarkTimer {

   /**
    * In order to exchange APF's default stop watch implementation please set this property within
    * your bootstrap file before starting the front controller.
    *
    * @var string Fully qualified name of the watch implementation.
    */
   public static $watchClass = DefaultStopWatch::class;

   /**
    * @var StopWatch The stop watch instance.
    */
   private $stopWatch;

   /**
    * Initializes the underlying stop watch.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    * Version 0.2, 14.03.2016 (ID#214: introduced stop watch implementation to allow exchange)<br />
    */
   public function __construct() {
      $this->stopWatch = new self::$watchClass();
   }

   /**
    * Enables the stop watch for measurement of the predefined events.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.01.2010<br />
    */
   public function enable() {
      $this->stopWatch->enable();
   }

   /**
    * Disables the stop watch for measurement of the predefined events.
    * <p />
    * Experiential tests proofed, that disabling the stop watch can increase performance!
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.01.2010<br />
    */
   public function disable() {
      $this->stopWatch->disable();
   }

   /**
    * This method is used to start a new timer.
    *
    * @param string $name The (unique!) name of the benchmark tag.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function start(string $name) {
      $this->stopWatch->start($name);
   }

   /**
    * Stops the stop watch for a certain event started with start().
    *
    * @param string $name The (unique!) name of the benchmark tag.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function stop(string $name) {
      $this->stopWatch->stop($name);
   }

   /**
    * Generates the report of the recorded events.
    *
    * @param Report $report Custom report format if desired (default: HtmlReport).
    *
    * @return string The HTML source code of the benchmark report.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function createReport(Report $report = null) {
      return $this->stopWatch->createReport($report);
   }

   /**
    * Returns the total process time recorded until the call to this method.
    * <p/>
    * You may use this method to add the total rendering time of an APF-based
    * application to your source code or any proprietary HTTP header.
    *
    * @return float The total processing time.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.04.2012<br />
    */
   public function getTotalTime() {
      return $this->stopWatch->getTotalTime();
   }

}
