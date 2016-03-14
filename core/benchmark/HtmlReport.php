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
 * Standard HTML report that can be used to be displayed on web pages (e.g. after page rendering).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 14.03.2016 (ID#214: extracted report generation from old BenchmarkTimer implementation)<br />
 */
class HtmlReport implements Report {

   /**
    * Line counter for the report.
    *
    * @var int $lineCounter
    */
   private $lineCounter = 0;

   /**
    * Defines the critical time for the benchmark report.
    *
    * @var float $criticalTime
    */
   private $criticalTime = 0.5;

   public function compile(array $processes) {
      $buffer = $this->generateHeader();

      $buffer .= $this->createReport4Process($processes[0]);

      foreach (array_slice($processes, 1) as $process) {
         /* @var $process Process */
         $buffer .= $this->createReport4Process($process);
      }

      $buffer .= $this->generateFooter();

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

      $buffer = PHP_EOL;
      $buffer .= '<style type="text/css">
#APF-Benchmark-Report {
   font-size: 10px !important;
   padding: 0.3em;
   background-color: #fff;
   font-family: Arial, Helvetica, sans-serif;
}
#APF-Benchmark-Report .even {
   background: #fff !important;
}
#APF-Benchmark-Report .odd {
   background: #ccc !important;
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
   color: #000;
   font-size: 1em;
   font-weight: normal;
   line-height: 1em;
   margin: 0 0 0 2em;
   border: 1px solid #ccc;
   height: 1.8em !important;
}
#APF-Benchmark-Report > dl {
   margin: 0;
}
#APF-Benchmark-Report .odd  > dl {
}
#APF-Benchmark-Report dt {
   float: left;
   border: none !important;
   line-height: 1em;
   margin-top: 0 !important;
   margin-bottom: 0 !important;
   height: 1.8em !important;
   min-height: 1.8em !important;
   padding-top: 0 !important;
   padding-bottom: 0 !important;
}
#APF-Benchmark-Report dt:before {
   color: #666;
   content: \'» \';
   padding: 0 0.3em;
}
#APF-Benchmark-Report dd {
   text-align: right;
   border: none !important;
   margin-top: 0 !important;
   margin-bottom: 0 !important;
   height: 1.8em !important;
   min-height: 1.8em !important;
   padding-top: 0 !important;
   padding-bottom: 0 !important;
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
      $buffer .= '    <dt class="header">Process tree</dt>';
      $buffer .= PHP_EOL;
      $buffer .= '    <dd class="header">Time</dd>';
      $buffer .= '  </dl>';
      $buffer .= PHP_EOL;

      return $buffer;
   }

   /**
    * Generates the report for one single process.
    *
    * @param Process $process the current process.
    *
    * @return string The report for the current process.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 01.01.2007<br />
    * Version 0.2, 29.12.2009 (Refactored markup)<br />
    */
   private function createReport4Process(Process $process) {

      $level = $process->getLevel();

      // add closing dl only if level greater than 0 to have
      // correct definition list leveling!
      $buffer = '<dl style="margin-left: ' . ($level * 20) . 'px;">';

      // assemble class for the current line
      if (($this->lineCounter % 2) == 0) {
         $class = 'even';
      } else {
         $class = 'odd';
      }

      // increment the line counter to be able to distinguish between even and odd lines
      $this->lineCounter++;

      $buffer .= PHP_EOL;
      $buffer .= '    <dt class="' . $class . '">' . $process->getName() . '</dt>';
      $buffer .= PHP_EOL;

      // add specific run time class to mark run times greater that the critical time
      $time = $process->getDuration();
      $buffer .= '    <dd class="' . $class . ' '
            . $this->getMarkedUpProcessTimeClass($time) . '">' . $time . ' s';

      $buffer .= '</dd>';
      $buffer .= PHP_EOL;

      // add closing dl only if level greater than 0 to have
      // correct definition list leveling!
      $buffer .= '</dl>';

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

      $buffer = PHP_EOL;
      $buffer .= '</div>';

      return $buffer;
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

}
