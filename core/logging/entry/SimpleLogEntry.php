<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
 * Implements a log entry.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2007<br />
 */
class SimpleLogEntry implements LogEntry {

   /**
    * Date of the message.
    *
    * @var string $date
    */
   protected $date;

   /**
    * Time of the message.
    *
    * @var string $time
    */
   protected $time;

   /**
    * Message text.
    *
    * @var string $message
    */
   protected $message;

   /**
    * Message type.
    *
    * @var string $severity
    */
   protected $severity;

   /**
    * The log target (here: the body of the log file name)
    *
    * @var string $target
    */
   protected $target;

   public function __construct(string $target, string $message, string $severity) {
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

   /**
    * @return string The log entry's recording date.
    */
   public function getDate() {
      return $this->date;
   }

   /**
    * @return string The log entry's recording time.
    */
   public function getTime() {
      return $this->time;
   }

   /**
    * @return string The log entry's message.
    */
   public function getMessage() {
      return $this->message;
   }

}
