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
import('core::logging::entry', 'GraphiteLogEntry');

/**
 * @package core::logging::writer
 * @class GraphiteLogWriter
 *
 * Implements a UDP-based non-blocking log writer to be used with the statsd daemon
 * that is used with Graphite an advanced system monitoring tool.
 * <p/>
 * Use the following code to register this log writer:
 * <code>
 * import('core::logging', 'Logger');
 * $logger = & Singleton::getInstance('Logger');
 *
 * import('core::logging::writer', 'GraphiteLogWriter');
 * $logger->addLogWriter('graphite', new GraphiteLogWriter('localhost', '8125'));
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 16.01.2013<br />
 */
class GraphiteLogWriter implements LogWriter {

   /**
    * @var string The Graphite server address (IP or DNS name).
    */
   protected $host;

   /**
    * @var string The Graphite server port.
    */
   protected $port;

   /**
    * @var string Defines the separator between multiple log entries sent to the server within one call (bulk).
    */
   protected $entrySeparator = "\n";

   /**
    * @var string The log target identifier.
    */
   protected $target;

   /**
    * @public
    *
    * Configures the Graphite log writer.
    *
    * @param string $host The Graphite server address (IP or DNS name).
    * @param string $port The Graphite server port.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.01.2013<br />
    */
   public function __construct($host, $port) {
      $this->host = $host;
      $this->port = $port;
   }

   /**
    * @public
    *
    * Let's you define the separator between multiple entries that are bulk-sent to the Grapite server.
    *
    * @param string $entrySeparator The separator between multiple log entries.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.01.2013<br />
    */
   public function setEntrySeparator($entrySeparator) {
      $this->entrySeparator = $entrySeparator;
   }

   public function writeLogEntries(array $entries) {

      $socket = fsockopen('udp://' . $this->host, $this->port, $errorNumber, $errorMessage);
      if ($socket === false || !empty($errorNumber) || !empty($errorMessage)) { // really a good check for not-empty?
         throw new LoggerException('Socket connection to host "' . $this->host . '" and port "' . $this->port
               . '" cannot be established (code: ' . $errorNumber . '; message: ' . $errorMessage);
      }

      // configure non-blocking I/O only to not influence/block request processing
      stream_set_blocking($socket, 0);

      // bulk-send data to avoid too much network I/O overhead
      // we can use implode() here, because LogEntry forces __toString() to be implemented
      fwrite($socket, implode($this->entrySeparator, $entries));

      fclose($socket);
   }

   public function setTarget($target) {
      $this->target = $target;
   }

}
