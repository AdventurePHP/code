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
    // end function
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
    * Implements a log entry.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2007<br />
    */
   class LogEntry {

      /**
       * @private
       * @var string Date of the message.
       */
      private $date;

      /**
       * @private
       * @var string Time of the message.
       */
      private $time;

      /**
       * @private
       * @var string Message text.
       */
      private $message;

      /**
       * @private
       * @var string Message type.
       */
      private $severity;

      /**
       * @public
       *
       * Creates a new log entry.
       *
       * @param string $message Desired error message.
       * @param string $type Error message type.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.03.2007<br />
       */
      public function LogEntry($message,$severity) {

         $this->date = date('Y-m-d');
         $this->time = date('H:i:s');
         $this->message = $message;
         $this->severity = $severity;

       // end function
      }

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
      public function toString() {
         return '['.$this->date.' '.$this->time.'] ['.$this->severity.'] '.$this->message;
       // end function
      }

    // end class
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

      /**
       * @protected
       * @var LogEntry[] Log entry store.
       */
      protected $logEntries = array();

      /**
       * @private
       * @var string Directory, where log files are stored.
       */
      protected $logDir;

      /**
       * @private
       * @var string Permission that is applied to a newly created log folder.
       */
      protected $logFolderPermissions = 0777;

      /**
       * @private
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
      public function Logger() {
         $reg = &Singleton::getInstance('Registry');
         $this->logDir = $reg->retrieve('apf::core','LogDir');
       // end function
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
       */
      public function logEntry($logFileName,$message,$type = 'INFO') {
         $this->logEntries[$logFileName][] = new LogEntry($message,$type);
       // end function
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
         if(count($this->logEntries) > 0) {

            // check if lock dir exists
            if(!is_dir($this->logDir)) {

               // try to create non existing log dir
               if(!@mkdir($this->logDir,$this->logFolderPermissions)) {
                  throw new LoggerException('[Logger->Logger()] The log directory "'
                          .$this->logDir.'" cannot be created du to permission restrictions! '
                          .'Please check config and specify the "LogDir" (namespace: "apf::core") '
                          .'parameter in the registry!');
               }

             // end if
            }

            // flush entries to the filesystem
            foreach($this->logEntries as $logFileName => $logEntries) {

               // generate complete log file name
               $logFileName = $this->__getLogFileName($logFileName);

               // generate complete log file pathe
               $logFile = $this->logDir.'/'.$logFileName;

               if(count($logEntries) > 0) {

                  $lFH = fopen($logFile,'a+');

                  for($i = 0; $i < count($logEntries); $i++) {
                     fwrite($lFH,$logEntries[$i]->toString().self::$CRLF);
                     // end for
                  }

                  // close file!
                  fclose($lFH);

                // end if
               }

             // end foreach
            }

            // reset the buffer
            $this->logEntries = array();

          // end if
         }

       // end function
      }

      /**
       *  @protected
       *
       *  Returns the name of the log file by the body of the name. Each log file will be named
       * like jjjj_mm_dd__{filename}.log.
       *
       *  @param string $fileName Name of the log file
       *  @return string Complete file name, that contains a date prefix and an file extension
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 29.03.2007<br />
       */
      protected function __getLogFileName($fileName) {
         return date('Y_m_d').'__'.str_replace('-','_',strtolower($fileName)).'.log';
       // end function
      }

    // end class
   }
?>