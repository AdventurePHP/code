<?php
namespace APF\core\logging\writer;

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
use APF\core\logging\LogWriter;
use APF\core\logging\LogEntry;
use APF\core\logging\LoggerException;

/**
 * Implements a log writer to persist the applied entries to log files.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.01.2013<br />
 */
class FileLogWriter implements LogWriter {

   /**
    * @var string The host prefix that is added to the log file name to distinguish between different hosts.
    */
   protected $hostPrefix = null;

   /**
    * @var string Directory, where log files are stored.
    */
   protected $logDir;

   /**
    * @var string Permission that is applied to a newly created log folder.
    */
   protected $logFolderPermissions = 0777;

   /**
    * @var string The log target identifier.
    */
   protected $target;

   /**
    * Initializes a FileLogWriter.
    *
    * @param string $logDir The directory to write the logs to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2013<br />
    */
   public function __construct($logDir) {
      $this->logDir = $logDir;
   }

   /**
    * Let's you change the log directory.
    *
    * @param string $logDir The directory to write the logs to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.03.2012<br />
    */
   public function setLogDir($logDir) {
      $this->logDir = $logDir;
   }

   /**
    * Let's you configure a host name prefix for all log files.
    * <p/>
    * This may be used in clustered hosting environments to reduce file lock overhead.
    *
    * @param string $hostPrefix The host name prefix to add to the log file name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.03.2012<br />
    */
   public function setHostPrefix($hostPrefix) {
      $this->hostPrefix = $hostPrefix;
   }

   /**
    * Let's you re-configure the folder permission for newly created log folders.
    *
    * @param string $logFolderPermissions Permission that is applied to a newly created log folder.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2013<br />
    */
   public function setLogFolderPermissions($logFolderPermissions) {
      $this->logFolderPermissions = $logFolderPermissions;
   }

   /**
    * Returns the name of the log file by the body of the name. Each log file will be named
    * like jjjj_mm_dd__[host-prefix_]{filename}.log.
    * <p/>
    * In case the host prefix is defined, it is prepended to the file name. This enables you
    * to write log files for different hosts on clustered hosting environments.
    *
    * @param string $fileName Name of the log file
    *
    * @return string Complete file name, that contains a date prefix and an file extension
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2007<br />
    * Version 0.2, 12.05.2012 (Added support for clustered hosting environments to write host-dependent log files)<br />
    */
   protected function getLogFileName($fileName) {

      // prepend host prefix to support multiple log files for clustered hosting environments
      if ($this->hostPrefix !== null) {
         $fileName = $this->hostPrefix . '__' . $fileName;
      }

      return $this->logDir . '/' . date('Y_m_d') . '__' . strtolower(preg_replace('/[^A-Za-z0-9\-_]/', '', $fileName)) . '.log';
   }

   public function setTarget($target) {
      $this->target = $target;
   }

   public function writeLogEntries(array $entries) {
      /* @var $entries LogEntry[] */

      // check if lock dir exists
      if (!is_dir($this->logDir)) {

         // try to create non existing log dir
         if (!@mkdir($this->logDir, $this->logFolderPermissions)) {
            throw new LoggerException('[FileLogWriter->writeLogEntries()] The log directory "'
                  . $this->logDir . '" cannot be created du to permission restrictions! '
                  . 'Please check system setup or change the log directory using FileLogWriter::setLogDir()!');
         }

      }

      $fileHandle = fopen($this->getLogFileName($this->target), 'a+');
      foreach ($entries as $entry) {
         fwrite($fileHandle, $entry . PHP_EOL);
      }
      fclose($fileHandle);
   }

}
