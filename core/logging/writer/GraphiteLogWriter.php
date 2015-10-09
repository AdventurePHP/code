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
namespace APF\core\logging\writer;

use APF\core\logging\LoggerException;
use APF\core\logging\LogWriter;

/**
 * Implements a UDP-based non-blocking log writer to be used with the statsd daemon
 * that is used with Graphite an advanced system monitoring tool.
 * <p/>
 * Use the following code to register this log writer:
 * <code>
 * use APF\core\logging\Logger;
 * $logger = & Singleton::getInstance(Logger::class);
 *
 * use APF\core\logging\writer\GraphiteLogWriter;
 * $logger->addLogWriter('graphite', new GraphiteLogWriter('localhost', '8125'));
 * </code>
 *
 * @author Christian Achatz, Daniel Basedow
 * @version
 * Version 0.1, 16.01.2013<br />
 * Version 0.2, 14.02.2013 (Introduced batch write mode configuration due to issues with the pystatsd implementation)<br />
 */
class GraphiteLogWriter implements LogWriter {

   /**
    * The Graphite server address (IP or DNS name).
    *
    * @var string $host
    */
   protected $host;

   /**
    * The Graphite server port.
    *
    * @var string $port
    */
   protected $port;

   /**
    * Defines the separator between multiple log entries sent to the server within one call (bulk).
    *
    * @var string $entrySeparator
    */
   protected $entrySeparator = "\n";

   /**
    * The log target identifier.
    *
    * @var string $target
    */
   protected $target;

   /**
    * Specifies if multiple LogEntries will be written in one datagram (true) or not (false) (pystatsd doesn't support multiple metrics in one datagram).
    *
    * @var bool $batchWrites
    */
   protected $batchWrites;

   /**
    * Configures the Graphite log writer.
    *
    * @param string $host The Graphite server address (IP or DNS name).
    * @param string $port The Graphite server port.
    * @param bool $batchWrites Specifies if multiple LogEntries will be written in one datagram (true) or not (false).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.01.2013<br />
    */
   public function __construct($host, $port, $batchWrites = true) {
      $this->host = $host;
      $this->port = $port;
      $this->batchWrites = $batchWrites;
   }

   /**
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

   /**
    * Let's you define if batch writes should be considered.
    *
    * @param bool $batchWrites True enables batch writes, false disables multiple datagrams per connection.
    *
    * @author Daniel Basedow
    * @version
    * Version 0.1, 14.02.2013<br />
    */
   public function setBatchWrites($batchWrites) {
      $this->batchWrites = $batchWrites;
   }

   public function writeLogEntries(array $entries) {
      if ($this->batchWrites) {
         $this->sendDatagram(implode($this->entrySeparator, $entries));
      } else {
         foreach ($entries as $entry) {
            $this->sendDatagram($entry);
         }
      }
   }

   /**
    * Send the actual UDP datagram to statsd.
    *
    * @param string $data The data that should be send to statsd.
    *
    * @throws LoggerException In case the UDP connection cannot be established.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.01.2013<br />
    */
   private function sendDatagram($data) {
      // suppress errors for fsockopen() to have one nice exception message instead of several error messages
      $socket = @fsockopen('udp://' . $this->host, $this->port, $errorNumber, $errorMessage);
      if ($socket === false || !empty($errorNumber) || !empty($errorMessage)) { // really a good check for not-empty?
         throw new LoggerException('Socket connection to host "' . $this->host . '" and port "' . $this->port
               . '" cannot be established (code: ' . $errorNumber . '; message: ' . $errorMessage);
      }

      // configure non-blocking I/O only to not influence/block request processing
      stream_set_blocking($socket, 0);

      // bulk-send data to avoid too much network I/O overhead
      // we can use implode() here, because LogEntry forces __toString() to be implemented
      fwrite($socket, $data);

      fclose($socket);
   }

   public function setTarget($target) {
      $this->target = $target;
   }

}
