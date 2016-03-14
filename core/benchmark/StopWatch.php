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

/**
 * Defines the structure of APF's stop watch implementations.
 * <p/>
 * The stop watch is used with the benchmark timer to measure dedicated events during
 * request processing.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 14.03.2016 (ID#214: introduced interface to be able to exchange/adapt APF's default stop watch)<br />
 */
interface StopWatch {

   /**
    * This method is used to start a new timer.
    *
    * @param string $name The (unique!) name of the benchmark tag.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function start($name = null);

   /**
    * Enables the stop watch for measurement of the predefined events.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.01.2010<br />
    */
   public function enable();

   /**
    * Disables the stop watch for measurement of the predefined events.
    * <p />
    * Experiential tests proofed, that disabling the stop watch can increase
    * page processing performance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.01.2010<br />
    */
   public function disable();

   /**
    * Stops the stop watch for a certain event started with start().
    *
    * @param string $name The (unique!) name of the benchmark tag.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 31.12.2006<br />
    */
   public function stop($name);

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
   public function createReport(Report $report = null);

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
   public function getTotalTime();

}
