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

/**
 * @package APF\core\logging\writer
 * @class StdOutLogWriter
 *
 * Implements a log writer to persist the applied entries to stdout.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.01.2013<br />
 */
class StdOutLogWriter implements LogWriter {

   /**
    * @var string The log target identifier.
    */
   protected $target;

   public function writeLogEntries(array $entries) {
      foreach ($entries as $entry) {
         echo '[' . $this->target . '] ' . $entry . PHP_EOL;
      }
   }

   public function setTarget($target) {
      $this->target = $target;
   }

}
