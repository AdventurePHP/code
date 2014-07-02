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
namespace APF\core\errorhandler;

use APF\core\logging\entry\SimpleLogEntry;
use APF\core\logging\LogEntry;
use APF\core\logging\Logger;
use APF\core\pagecontroller\Page;
use APF\core\registry\Registry;
use APF\core\singleton\Singleton;

/**
 * Implements the default error handler of the APF. Logs errors to a logfile and displays the
 * standard error page.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.01.2009<br />
 */
class DefaultErrorHandler implements ErrorHandler {

   /**
    * Error number.
    *
    * @var int $errorNumber
    */
   protected $errorNumber;

   /**
    * Error message,
    *
    * @var string $errorMessage
    */
   protected $errorMessage;

   /**
    * Error file.
    *
    * @var string $errorFile
    */
   protected $errorFile;

   /**
    * Error line.
    *
    * @var int $errorLine
    */
   protected $errorLine;

   public function handleError($errorNumber, $errorMessage, $errorFile, $errorLine) {

      // fill attributes
      $this->errorNumber = $errorNumber;
      $this->errorMessage = $errorMessage;
      $this->errorFile = $errorFile;
      $this->errorLine = $errorLine;

      // log error
      $this->logError();

      // build nice error page
      echo $this->buildErrorPage();
   }

   /**
    * Creates a log entry containing the error occurred.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 21.01.2007<br />
    * Version 0.2, 29.03.2007 (Changed to new logger)<br />
    */
   protected function logError() {
      $message = '[' . ($this->generateErrorID()) . '] ' . $this->errorMessage . ' (Number: ' . $this->errorNumber . ', File: ' . $this->errorFile . ', Line: ' . $this->errorLine . ')';

      $log = & Singleton::getInstance('APF\core\logging\Logger');
      /* @var $log Logger */
      $log->addEntry(
            new SimpleLogEntry(
            // use the configured log target to allow custom configuration of APF-internal log statements
            // to be written to a custom file/location
                  Registry::retrieve('APF\core', 'InternalLogTarget'),
                  $message,
                  LogEntry::SEVERITY_ERROR
            )
      );
   }

   /**
    * Creates the error page.
    *
    * @return string The APF error page.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.01.2007<br />
    * Version 0.2, 03.03.2007<br />
    * Version 0.3, 04.03.2007 (Context now is set)<br />
    * Version 0.4, 29.03.2007<br />
    * Version 0.5, 13.08.2008 (Removed text only error page messages)<br />
    */
   protected function buildErrorPage() {

      // at this point we have to re-include the benchmark timer, because PHP
      // sometimes forgets about this import and throws a
      // Fatal error: Exception thrown without a stack frame in Unknown on line 0
      // exception.


      // create page
      $stackTrace = new Page();
      $stackTrace->setContext('APF\core\errorhandler');
      $stackTrace->loadDesign('APF\core\errorhandler\templates', 'errorpage');

      // inject error information into the document attributes array
      $doc = & $stackTrace->getRootDocument();
      $doc->setAttribute('id', $this->generateErrorID());
      $doc->setAttribute('message', $this->errorMessage);
      $doc->setAttribute('number', $this->errorNumber);
      $doc->setAttribute('file', $this->errorFile);
      $doc->setAttribute('line', $this->errorLine);

      // create error page
      return $stackTrace->transform();
   }

   /**
    * Generates the error id.
    *
    * @return string The unique error id.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 21.01.2007<br />
    */
   protected function generateErrorID() {
      return md5($this->errorMessage . $this->errorNumber . $this->errorFile . $this->errorLine);
   }

}
