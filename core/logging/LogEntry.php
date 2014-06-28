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
 * Defines the scheme of a log entry.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 17.04.2012<br />
 * Version 0.2, 17.01.2013 (Removed the constructor definition to easily allow custom log entry implementations)<br />
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
