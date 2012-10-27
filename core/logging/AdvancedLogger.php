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
register_shutdown_function('flushAdvancedLogger');

/**
 * Wrapper for flushing the advanced logger instances.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 09.11.2008<br />
 */
function flushAdvancedLogger() {

   /* @var $aLF AdvancedLoggerFactory */
   $aLF = &Singleton::getInstance('AdvancedLoggerFactory');

   // get registered logger
   $logger = Registry::retrieve('apf::core', 'AdvancedLogger');
   $count = count($logger);
   if ($count > 0) {

      for ($i = 0; $i < $count; $i++) {

         $log = &$aLF->getAdvancedLogger($logger[$i]['section']);
         $log->flushLogBuffer();
         unset($log);
      }
   }
}

/**
 * @package core::logging
 * @class AdvancedLogEntry
 *
 * Implements a  logEntry object.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2007<br />
 */
class AdvancedLogEntry extends APFObject {

   /**
    * @private
    *  Date of the message.
    */
   private $date;

   /**
    * @private
    *  Time of the message.
    */
   private $time;

   /**
    * @private
    *  Message text.
    */
   private $message;

   /**
    * @private
    *  Message type (aka severity).
    */
   private $severity;

   /**
    * @public
    *
    * Constructor of the class. Creates a new logEntry object.
    *
    * @param string $message Desired error message.
    * @param string $type Error message type.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2007<br />
    */
   public function __construct($message, $type) {
      $this->date = date('Y-m-d');
      $this->time = date('H:i:s');
      $this->message = $message;
      $this->severity = $type;
   }

   /**
    * @public
    *
    * Returns the message string used to write into a log file.
    *
    * @param bool $timestamp True in case the timestamp should be included, false otherwise.
    * @param bool $type True in case the type should be included, false otherwise.
    * @return string The string representation of the error message.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.11.2008<br />
    */
   public function toString($timestamp = true, $type = true) {

      $logString = (string)'';
      if ($timestamp === true) {
         $logString .= '[' . $this->date . ' ' . $this->time . '] ';
      }
      if ($type === true) {
         $logString .= '[' . $this->severity . '] ';
      }
      return $logString . $this->message;
   }

   public function getDate() {
      return $this->date;
   }

   public function getTime() {
      return $this->time;
   }

   public function getMessage() {
      return $this->message;
   }

   public function getSeverity() {
      return $this->severity;
   }

}

/**
 * @package core::logging
 * @class AdvancedLoggerFactory
 *
 * Implements the factory for the AdvancedLogger. Must be created singleton using the
 * service manager!
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 09.11.2008<br />
 */
class AdvancedLoggerFactory extends APFObject {

   /**
    * @private
    * The logger cache.
    */
   private $logger = array();

   /**
    * @public
    *
    *  Returns the logger for the desired config section. Caches all previously created logger.
    *
    * @param string $section the section, the logger should be initialized with
    * @return AdvancedLogger $logger the desired logger
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.11.2008<br />
    */
   public function &getAdvancedLogger($section) {

      // calculate logger cache key
      $loggerKey = md5($section);

      // create logger, if it does not exist
      if (!isset($this->logger[$loggerKey])) {

         // create logger
         $this->logger[$loggerKey] = $this->getAndInitServiceObject('core::logging', 'AdvancedLogger', $section, APFService::SERVICE_TYPE_NORMAL);

         // register current instance in the registry so that the flush function can get the
         // instances from the service manager in correct service type configuration
         $logger = Registry::retrieve('apf::core', 'AdvancedLogger');
         if (count($logger) == 0) {
            $logger = array();
         }
         $logger[] = array(
            'context' => $this->__Context,
            'language' => $this->__Language,
            'section' => $section
         );
         Registry::register('apf::core', 'AdvancedLogger', $logger);
      }

      return $this->logger[$loggerKey];
   }

}

/**
 * @package core::logging
 * @class AdvancedLogger
 *
 * Implements an advanced logger for the adventure php framework. In contrast to the default
 * logger, this component must be configured for each usage. The advantage of the component is
 * that multiple targets, log formats and output targets can be chosen.
 * Usage:
 * <code>
 * $logFactory = &$this->getServiceObject('core::logging','AdvancedLoggerFactory');
 * $log = &$logFactory->getAdvancedLogger('<section_name>');
 * </code>
 * Please note, that flushing the log buffer to stdout and file is much more faster. Here's an
 * benchmark example of the three possibilities:
 * <pre>flushLogBuffer_file      0.0026059151 s
 * flushLogBuffer_stdout    0.0001997948 s
 * flushLogBuffer_database  0.0228970051 s</pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 09.11.2008<br />
 */
class AdvancedLogger extends APFObject {

   /**
    * @var AdvancedLogEntry[] The log buffer.
    */
   private $logBuffer = array();

   /**
    * @var Configuration Contains the desired log configuration section.
    */
   private $logConfig = null;

   public function init($initParam) {

      // initialize the current log configuration section
      if ($this->logConfig === null) {

         // initialize config
         $config = $this->getConfiguration('core::logging', 'logconfig');
         $this->logConfig = $config->getSection($initParam);

         if ($this->logConfig === null) {
            $env = Registry::retrieve('apf::core', 'Environment');
            throw new LoggerException('[AdvancedLogger::init()] The configuration section ("'
                  . $initParam . '") cannot be loaded from the logging configuration file "'
                  . $env . '_logconfig.ini" for namespace "core::logging" and context "'
                  . $this->getContext() . '"!', E_USER_ERROR);
         }

         // check for the target directive
         if ($this->logConfig->getValue('LogTarget') == null) {
            throw new LoggerException('[AdvancedLogger::init()] The configuration section ("'
                  . $initParam . '") does not contain a "LogTarget" directive! Please check '
                  . 'your configuration.', E_USER_ERROR);
         }
      }
   }

   /**
    * @public
    *
    *  The logEntry() function let's you append a log message to the current AdvancedLogger instance.
    *  Configuration is done by the init() method.
    *
    * @param string $message the log entry's message
    * @param string $type the log entry's type (aka severity)
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.11.2008<br />
    */
   public function logEntry($message, $type = 'INFO') {
      $this->logBuffer[] = new AdvancedLogEntry($message, $type);
   }

   /**
    * @public
    *
    *  Flushes the log buffer. Must be called after each request.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.11.2008<br />
    */
   public function flushLogBuffer() {

      switch ($this->logConfig->getValue('LogTarget')) {

         case 'file':
            $this->flush2File();
            break;
         case 'database':
            $this->flush2Database();
            break;
         case 'stdout':
            $this->flush2Stdout();
            break;
         default:
            throw new LoggerException('[AdvancedLogger::flushLogBuffer()] The chosen log target ("'
                  . $this->logConfig->getValue('LogTarget') . '") is not implemented. Please take '
                  . 'one out of "file", "database" and "stdout"!', E_USER_ERROR);
            break;
      }
   }

   /**
    * @private
    *
    *  Implements the log flushing for the database target.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.11.2008<br />
    */
   private function flush2Database() {

      // read params from the configuration
      if ($this->logConfig->getValue('LogDatabase') == null) {
         throw new LoggerException('[AdvancedLogger::flush2Database()] The configuration '
               . 'section does not contain a "LogDatabase" definition! Please check your configuration.');
      }
      if ($this->logConfig->getValue('LogTable') == null) {
         throw new LoggerException('[AdvancedLogger::flush2Database()] The configuration '
               . 'section does not contain a "LogTable" definition! Please check your configuration.');
      }
      $logDatabase = $this->logConfig->getValue('LogDatabase');
      $logTable = $this->logConfig->getValue('LogTable');

      /* @var $cM ConnectionManager */
      $cM = &$this->getServiceObject('core::database', 'ConnectionManager');
      $db = &$cM->getConnection($logDatabase);

      // flush log entries to the table
      foreach ($this->logBuffer as $entry) {
         /* @var $entry AdvancedLogEntry */
         $timestamp = $entry->getDate() . ' ' . $entry->getTime();
         $insert = 'INSERT INTO `' . $logTable . '`
                          (`Timestamp`,`Type`,`Message`)
                          VALUES
                          (\'' . $timestamp . '\',\'' . $entry->getSeverity() . '\',\'' . $entry->getMessage() . '\');';
         $db->executeTextStatement($insert);
      }
   }

   /**
    * @private
    *
    *  Implements the log flushing for the file target.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.11.2008<br />
    */
   private function flush2File() {

      // read params from the configuration
      if ($this->logConfig->getValue('LogDir') == null) {
         throw new LoggerException('[AdvancedLogger::flush2Database()] The configuration '
               . 'section does not contain a "LogDir" definition! Please check your configuration.');
      }
      if ($this->logConfig->getValue('LogFileName') == null) {
         throw new LoggerException('[AdvancedLogger::flush2Database()] The configuration '
               . 'section does not contain a "LogFileName" definition! Please check your configuration.');
      }
      $logDir = $this->logConfig->getValue('LogDir');
      $logFileName = date('Y_m_d') . '_' . $this->logConfig->getValue('LogFileName') . '.log';

      // create folder, if it does not exist
      if (!is_dir($logDir)) {
         throw new LoggerException('[AdvancedLogger::flush2File()] Given log directory "'
               . $logDir . '" does not exist! Please create it.');
      }

      // flush buffer to file
      $fH = fopen($logDir . '/' . $logFileName, 'a+');

      foreach ($this->logBuffer as $entry) {
         fwrite($fH, $this->getLogEntryString($entry) . PHP_EOL);
      }

      fclose($fH);
   }

   /**
    * @private
    *
    *  Implements the log flushing for the stdout target.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.11.2008<br />
    */
   private function flush2Stdout() {
      foreach ($this->logBuffer as $entry) {
         echo $this->getLogEntryString($entry) . PHP_EOL;
      }
   }

   /**
    * @private
    *
    *  Generates the log entry string by a given AdvancedLogEntry object.
    *
    * @param AdvancedLogEntry $entry the current log entry
    * @return string $logString the corresponding log string
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.11.2008<br />
    */
   private function getLogEntryString(AdvancedLogEntry $entry) {

      // configure timestamp
      $timestamp = true;
      if ($this->logConfig->getValue('LogTimestamp') == null || $this->logConfig->getValue('LogTimestamp') == 'false') {
         $timestamp = false;
      }

      // configure type
      $type = true;
      if ($this->logConfig->getValue('LogType') == null || $this->logConfig->getValue('LogType') == 'false') {
         $type = false;
      }

      return $entry->toString($timestamp, $type);
   }

}
