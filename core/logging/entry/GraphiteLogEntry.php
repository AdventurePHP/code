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
namespace APF\core\logging\entry;

use APF\core\logging\LogEntry;

/**
 * @package APF\core\logging\entry
 * @class GraphiteLogEntry
 *
 * Implements a log entry that is compatible with the GraphiteLogWriter.
 * <p/>
 * Creates a dot-separated representation of the current log entry that is
 * familiar to the GraphiteLogWriter.
 * <p/>
 * Use the following code sample to add a graphite log entry:
 * <code>
 * $logger = & Singleton::getInstance('APF\core\logging\Logger');
 * $logger->addEntry(new GraphiteLogEntry(
 *    'graphite',
 *    'services.web.rendering-times',
 *    's',
 *    '1.234',
 *    LogEntry::SEVERITY_INFO
 * ));
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 17.01.2013<br />
 */
class GraphiteLogEntry implements LogEntry {

   /**
    * @var string The desired log target to write this log entry to.
    */
   protected $target;

   /**
    * @var string The metric descriptor (e.g. <em>services.web.rendering-time</em>).
    */
   protected $metric;

   /**
    * @var string The unit of the metric.
    */
   protected $unit;

   /**
    * @var string The value of the current log entry (a.k.a. metric).
    */
   protected $value;

   /**
    * @var string The severity of this entry.
    */
   protected $severity;

   /**
    * @public
    *
    * Creates a Graphite log entry.
    *
    * @param string $target The desired log target to write this log entry to.
    * @param string $metric The metric descriptor (e.g. <em>services.web.rendering-time</em>).
    * @param string $unit The unit of the metric.
    * @param string $value The value of the current log entry (a.k.a. metric).
    * @param string $severity The severity of this entry.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.01.2013<br />
    */
   public function __construct($target, $metric, $unit, $value, $severity) {
      $this->target = $target;
      $this->metric = $metric;
      $this->unit = $unit;
      $this->value = $value;
      $this->severity = $severity;
   }

   public function getLogTarget() {
      return $this->target;
   }

   public function getSeverity() {
      return $this->severity;
   }

   public function __toString() {
      // e.g. services.web.rendering-time:0.1234|s
      return $this->metric . ':' . $this->value . '|' . $this->unit;
   }

}
