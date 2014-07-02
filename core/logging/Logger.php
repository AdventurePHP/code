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
namespace APF\core\logging;

use APF\core\logging\entry\SimpleLogEntry;
use APF\core\logging\writer\FileLogWriter;
use APF\core\registry\Registry;

/**
 * Implements a generic logger used in the framework's core components and your applications. The
 * class must be initialized singleton! Flushing is done automatically ba shutdown function after
 * a request.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2007<br />
 * Version 0.2, 19.04.2012 (Introduced log thresholds)<br />
 */
class Logger {

   public static $LOGGER_THRESHOLD_ALL = array(
         LogEntry::SEVERITY_TRACE,
         LogEntry::SEVERITY_DEBUG,
         LogEntry::SEVERITY_WARNING,
         LogEntry::SEVERITY_INFO,
         LogEntry::SEVERITY_ERROR,
         LogEntry::SEVERITY_FATAL
   );

   public static $LOGGER_THRESHOLD_WARN = array(
         LogEntry::SEVERITY_WARNING,
         LogEntry::SEVERITY_INFO,
         LogEntry::SEVERITY_ERROR,
         LogEntry::SEVERITY_FATAL
   );

   public static $LOGGER_THRESHOLD_INFO = array(
         LogEntry::SEVERITY_INFO,
         LogEntry::SEVERITY_ERROR,
         LogEntry::SEVERITY_FATAL
   );

   public static $LOGGER_THRESHOLD_ERROR = array(
         LogEntry::SEVERITY_ERROR,
         LogEntry::SEVERITY_FATAL
   );

   /**
    * Defines the severity types that are written to the log file.
    *
    * @var array $logThreshold
    */
   protected $logThreshold;

   /**
    * Log entry store.
    *
    * @var LogEntry[][] $logEntries
    */
   protected $logEntries = array();

   /**
    * The maximum number of log entries before the log buffer is flushed automatically.
    *
    * @var int $maxBufferLength
    */
   protected $maxBufferLength = 300;

   /**
    * Counter for log file entries to handle the buffer length.
    *
    * @var int $logEntryCount
    */
   protected $logEntryCount = 0;

   /**
    * The list of registered log writers.
    *
    * @var LogWriter[] $writers
    */
   protected $writers = array();

   /**
    * Initializes the logger.
    * <p/>
    * Please be aware, that starting with release 1.15 DEBUG and TRACE statements
    * are not automatically written to the log file due to the default severity
    * threshold configuration.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2007<br />
    * Version 0.2, 02.04.2007<br />
    * Version 0.3, 21.06.2008<br />
    * Version 0.4, 14.08.2008 (LogDir initialization was moved do the flushLogBuffer() method)<br />
    * Version 0.5, 12.01.2013 (Moved log dir initialization to log writer)<br />
    */
   public function __construct() {
      $this->logThreshold = self::$LOGGER_THRESHOLD_WARN;

      // By default, a file-based log writer is initialized.
      // Please note, that the writer's target name can be configured
      // within the Registry for all framework-related log statements.
      $this->addLogWriter(
            Registry::retrieve('APF\core', 'InternalLogTarget'),
            new FileLogWriter(
                  str_replace('\\', '/', getcwd()) . '/logs'
            )
      );
   }

   /**
    * Calling this method you can define the maximum number of entries
    * before auto-flush takes place.
    *
    * @param int $maxBufferLength The threshold number.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.05.2011<br />
    */
   public function setMaxBufferLength($maxBufferLength) {
      $this->maxBufferLength = $maxBufferLength;
   }

   /**
    * Let's you set the logging threshold. This is a list of severity that are
    * written to the log. All other severity are ignored.
    *
    * @param array $threshold The threshold configuration to apply to the log statements.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.03.2012<br />
    */
   public function setLogThreshold(array $threshold) {
      $this->logThreshold = $threshold;
   }

   /**
    * Using this method you can add a LogWriter implementation to the current logger.
    * <p/>
    * Each log writer is identified by a dedicated target name that is injected to the
    * writer.
    * <p/>
    * This method can be used to reconfigure a writer as follows:
    * <code>
    * $writer = $logger->getLogWriter('foo');
    * $writer->setBar('123');
    * $logger->addLogWriter('foo', $writer);
    * </code>
    * In case you intend to add a new writer based on an existing one, consider this:
    * <code>
    * $writer = clone $logger->getLogWriter('foo');
    * $logger->addLogWriter('bar', $writer);
    * </code>
    *
    * @param string $target The log target name.
    * @param LogWriter $writer The respective writer.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2013<br />
    */
   public function addLogWriter($target, LogWriter $writer) {
      // let the writer know the identifier it is registered with (e.g. to open log files efficiently).
      $writer->setTarget($target);

      $this->writers[$target] = $writer;
   }

   /**
    * Removes a registered writer identified by the applied target name.
    *
    * @param string $target The log target name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2013<br />
    */
   public function removeLogWriter($target) {
      unset($this->writers[$target]);
   }

   /**
    * Let's you retrieve a log writer identified by the applied target name.
    *
    * @param string $target The log target name.
    *
    * @return LogWriter $writer The respective writer.
    * @throws LoggerException In case the desired log writer is not registered.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2013<br />
    */
   public function getLogWriter($target) {
      if (isset($this->writers[$target])) {
         return $this->writers[$target];
      }
      throw new LoggerException('Log writer with name "' . $target . '" is not registered!');
   }

   /**
    * Returns a list of registered log targets.
    * <p/>
    * May be used for re-configuration (e.g. register a debugging log writer) or information purposes.
    *
    * @return string[] The list of registered log targets.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.01.2013<br />
    */
   public function getRegisteredTargets() {
      return array_keys($this->writers);
   }

   /**
    * Creates a log entry with the SimpleLogEntry type.
    * <p/>
    * In case you want to add custom log entry elements, please use the <em>addEntry()</em> method
    * in combination with your custom <em>LogEntry</em> implementation.
    *
    * @param string $logFileName Name of the log file to log to.
    * @param string $message Log message.
    * @param string $severity The severity of the log message.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2007<br />
    * Version 0.2, 02.05.2011 (Flushes the log buffer implicitly after a configured number of entries)<br />
    */
   public function logEntry($logFileName, $message, $severity = LogEntry::SEVERITY_INFO) {
      $this->addEntry(new SimpleLogEntry($logFileName, $message, $severity));
   }

   /**
    * Method to create a log entry the OO-way.
    * <p/>
    * Use this method, in case you intend to add custom log entry implementations.
    *
    * @param LogEntry $entry The log entry to add.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.04.2012<br />
    * Version 0.2, 09.01.2013 (Now public to add log entries OO-style)<br />
    */
   public function addEntry(LogEntry $entry) {

      // check for severity to match the threshold definition
      if (in_array($entry->getSeverity(), $this->logThreshold)) {
         $this->logEntries[$entry->getLogTarget()][] = $entry;
         $this->logEntryCount++;
      }

      // flush the log buffer in case the maximum number of entries is reached.
      if ($this->logEntryCount > $this->maxBufferLength) {
         $this->flushLogBuffer();
      }
   }

   /**
    * Flushes the log buffer to the desired files.
    *
    * @throws LoggerException In case a writer cannot be retrieved.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2007<br />
    * Version 0.2, 14.08.2008 (LogDir now is created during flush instead of during initialization)<br />
    * Version 0.3, 18.03.2009 (After writing entries to file, the log container is now reset)<br />
    * Version 0.4, 19.04.2009 (Suppressed mkdir() warning to make the error message nice)<br />
    * Version 0.5, 12.01.2013 (Optimized count() calls to increase performance)<br />
    * Version 0.6, 12.01.2013 (Switched to log writer concept)<br />
    */
   public function flushLogBuffer() {

      // Flush entries to the respective writers.
      // To increase performance, the writers get a whole bunch of log entries instead of
      // single items. For even better performance, the log entries are grouped by writer
      // to avoid sorting when flushing the buffer.
      foreach ($this->logEntries as $target => $logEntries) {

         $writer = $this->getLogWriter($target);

         /* @var $logEntries LogEntry[] */
         $writer->writeLogEntries($logEntries);
      }

      // reset the buffer and the counter
      $this->logEntries = array();
      $this->logEntryCount = 0;
   }

}
