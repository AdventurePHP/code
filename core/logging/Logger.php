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
register_shutdown_function('flushLogger');

/**
 * @package core::logging
 *
 * Wrapper for flushing the log buffer.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2007<br />
 */
function flushLogger() {
   Singleton::getInstance('Logger')->flushLogBuffer();
}

/**
 * @package core::logging
 * @class LoggerException
 *
 * Defines a custom exception for the logger component.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.04.2010<br />
 */
class LoggerException extends Exception {
}

/**
 * @package core::logging
 * @class LogEntry
 *
 * Defines the scheme of a log entry.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 17.04.2012<br />
 */
interface LogEntry {

   /**
    * The following constants define common severity types to be used within log statements.
    */
   const SEVERITY_TRACE = 'TRACE';
   const SEVERITY_DEBUG = 'DEBUG';
   const SEVERITY_INFO = 'INFO';
   const SEVERITY_WARNING = 'WARN';
   const SEVERITY_ERROR = 'ERROR';
   const SEVERITY_FATAL = 'FATAL';

   /**
    * @public
    *
    * Creates a new log entry.
    *
    * @param string $target The log target of this entry.
    * @param string $message Desired error message.
    * @param string $severity Error message severity.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2007<br />
    */
   public function __construct($target, $message, $severity);

   /**
    * @public
    *
    * Returns the message string used to write into a log file.
    *
    * @return string Complete error message including date and time.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2007<br />
    */
   public function __toString();

   /**
    * @public
    *
    * Returns the target identifier of this log entry. Merely, - but depending
    * on the LogWriter implementation - this is the name of the log file or at
    * least the main part of it.
    *
    * @return string The log target key.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.04.2012<br />
    */
   public function getLogTarget();

   /**
    * @public
    *
    * Returns the severity of this log entry.
    *
    * @return string The log entry's severity.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.04.2012<br />
    */
   public function getSeverity();
}

/**
 * @package core::logging
 * @class LogEntry
 *
 * Implements a log entry.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2007<br />
 */
class SimpleLogEntry implements LogEntry {

   /**
    * @var string Date of the message.
    */
   protected $date;

   /**
    * @var string Time of the message.
    */
   protected $time;

   /**
    * @var string Message text.
    */
   protected $message;

   /**
    * @var string Message type.
    */
   protected $severity;

   /**
    * @var string The log target (here: the body of the log file name)
    */
   protected $target;

   public function __construct($target, $message, $severity) {
      $this->date = date('Y-m-d');
      $this->time = date('H:i:s');
      $this->target = $target;
      $this->message = $message;
      $this->severity = $severity;
   }

   public function __toString() {
      return '[' . $this->date . ' ' . $this->time . '] [' . $this->severity . '] ' . $this->message;
   }

   public function getLogTarget() {
      return $this->target;
   }

   public function getSeverity() {
      return $this->severity;
   }

}

/**
 * @package core::logging
 * @class Logger
 *
 * Implements a generic logger used in the framework's core components and your applications. The
 * class must be initialized singleton! Flushing is done automatically ba shutdown function after
 * a request.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2007<br />
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
    * @var array Defines the severity types that are witten to the log file.
    */
   protected $logThreshold;

   /**
    * @var LogEntry[] Log entry store.
    */
   protected $logEntries = array();

   /**
    * @var string Directory, where log files are stored.
    */
   protected $logDir;

   /**
    * @var string Permission that is applied to a newly created log folder.
    */
   protected $logFolderPermissions = 0777;

   /**
    * @var int The maximum number of log entries before the log buffer is flushed automatically.
    */
   protected $maxBufferLength = 300;

   /**
    * @var int Counter for log file entries to handle the buffer length.
    */
   protected $logEntryCount = 0;

   /**
    * @var string Newline sign. Uses the PHP's standard newline sign if not configured in different way.
    */
   protected static $CRLF = PHP_EOL;

   /**
    * @public
    *
    * Initializes the logger.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2007<br />
    * Version 0.2, 02.04.2007<br />
    * Version 0.3, 21.06.2008<br />
    * Version 0.4, 14.08.2008 (LogDir initialization was moved do the flushLogBuffer() method)<br />
    */
   public function __construct() {
      $this->logDir = Registry::retrieve('apf::core', 'LogDir');
      $this->logThreshold = self::$LOGGER_THRESHOLD_WARN;
   }

   /**
    * @public
    *
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
    * @public
    *
    * Let's you set the logging threshold. This is a list of severities that are
    * written to the log. All other severities are ignored.
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
    * @public
    *
    * Create a log entry.
    *
    * @param string $logFileName Name of the log file to log to
    * @param string $message Log message
    * @param string $type Desired type of the message
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2007<br />
    * Version 0.2, 02.05.2011 (Flushes the log buffer implicitly after a configured number of entries)<br />
    */
   public function logEntry($logFileName, $message, $type = LogEntry::SEVERITY_INFO) {
      $this->addEntry(new SimpleLogEntry($logFileName, $message, $type));
   }

   /**
    * @protected
    *
    * Internal method to create a log entry.
    * <p/>
    * Will be opened to the outside world in future versions in conjunction
    * with the refactoring of the Logger and the combination with the
    * AdvancedLogger.
    *
    * @param LogEntry $entry The log entry to add
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.04.2012<br />
    */
   protected function addEntry(LogEntry $entry) {

      // check for severity to match the threshold definition
      if (in_array($entry->getSeverity(), $this->logThreshold)) {
         $this->logEntries[$entry->getLogTarget()][] = $entry;

         // flush the log buffer in case the maximum number of entries is reached.
         $this->logEntryCount++;
      }

      if ($this->logEntryCount > $this->maxBufferLength) {
         $this->flushLogBuffer();
      }
   }

   /**
    * @public
    *
    * Flushes the log buffer to the desired files.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2007<br />
    * Version 0.2, 14.08.2008 (LogDir now is created during flush instead of during initialization)<br />
    * Version 0.3, 18.03.2009 (After writing entries to file, the log container is now resetted)<br />
    * Version 0.4, 19.04.2009 (Suppressed mkdir() warning to make the error message nice)<br />
    */
   public function flushLogBuffer() {

      // check, if buffer contains log entries
      if (count($this->logEntries) > 0) {

         // check if lock dir exists
         if (!is_dir($this->logDir)) {

            // try to create non existing log dir
            if (!@mkdir($this->logDir, $this->logFolderPermissions)) {
               throw new LoggerException('[Logger->flushLogBuffer()] The log directory "'
                     . $this->logDir . '" cannot be created du to permission restrictions! '
                     . 'Please check config and specify the "LogDir" (namespace: "apf::core") '
                     . 'parameter in the registry!');
            }

         }

         // flush entries to the files
         foreach ($this->logEntries as $logFileName => $logEntries) {

            // generate complete log file name
            $logFileName = $this->getLogFileName($logFileName);

            // generate complete log file path
            $logFile = $this->logDir . '/' . $logFileName;

            if (count($logEntries) > 0) {

               $lFH = fopen($logFile, 'a+');

               for ($i = 0; $i < count($logEntries); $i++) {
                  fwrite($lFH, $logEntries[$i]->__toString() . self::$CRLF);
               }

               // close file to avoid deadlocks!
               fclose($lFH);
            }
         }

         // reset the buffer and the counter
         $this->logEntries = array();
         $this->logEntryCount = 0;
      }
   }

   /**
    * @protected
    *
    *  Returns the name of the log file by the body of the name. Each log file will be named
    * like jjjj_mm_dd__{filename}.log.
    *
    * @param string $fileName Name of the log file
    * @return string Complete file name, that contains a date prefix and an file extension
    *
    * @author Christian Achatz
    * @version
    *  Version 0.1, 29.03.2007<br />
    */
   protected function getLogFileName($fileName) {
      return date('Y_m_d') . '__' . str_replace('-', '_', strtolower($fileName)) . '.log';
   }

}
