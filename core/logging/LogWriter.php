<?php
namespace APF\core\logging;

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

/**
 * Defines the interface for a log writer that can be registered
 * with the Logger using the addLogWriter() method.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.01.2013<br />
 */
interface LogWriter {

   /**
    * Writes log entries applied by the Logger.
    *
    * @param LogEntry[] The list of log entries to write.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2013<br />
    */
   public function writeLogEntries(array $entries);

   /**
    * Method to inject the log target identifier by the Logger.
    *
    * @param string $target The log target identifier for LogWriter-internal usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2013<br />
    */
   public function setTarget($target);
}
