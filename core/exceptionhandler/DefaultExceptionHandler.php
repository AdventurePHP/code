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
import('core::exceptionhandler', 'AbstractExceptionHandler');

/**
 * @package core::exceptionhandler
 * @class DefaultExceptionHandler
 *
 * Implements the default APF exception handler for uncaught exceptions.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.02.2009<br />
 */
class DefaultExceptionHandler extends AbstractExceptionHandler {

   /**
    * @var int The number of the exception.
    */
   protected $exceptionNumber = null;

   /**
    * @var string The message of the exception.
    */
   protected $exceptionMessage = null;

   /**
    * @var string The file, the exception occures in.
    */
   protected $exceptionFile = null;

   /**
    * @var int The line, the exception occures in
    */
   protected $exceptionLine = null;

   /**
    * @var string The exception type (name of the class).
    */
   protected $exceptionType = null;

   /**
    * @var string[] The exception trace.
    */
   protected $exceptionTrace = array();

   /**
    * @public
    *
    * Implements the exception handling function, that is called by the APF exception handling
    * function.
    *
    * @param Exception $exception the thrown exception.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.02.2009<br />
    */
   public function handleException(Exception $exception) {

      // fill attributes
      $this->exceptionNumber = $exception->getCode();
      $this->exceptionMessage = $exception->getMessage();
      $this->exceptionFile = $exception->getFile();
      $this->exceptionLine = $exception->getLine();
      $this->exceptionTrace = $exception->getTrace();
      $this->exceptionType = get_class($exception);

      // log exception
      $this->logException();

      // build nice exception page
      echo $this->buildExceptionPage();
   }

   /**
    * @private
    *
    * Creates a log entry containing the exception occured.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.02.2009<br />
    */
   protected function logException() {
      $message = '[' . ($this->generateExceptionID()) . '] ' . $this->exceptionMessage . ' (Number: ' . $this->exceptionNumber . ', File: ' . $this->exceptionFile . ', Line: ' . $this->exceptionLine . ')';
      import('core::logging', 'Logger');
      $log = Singleton::getInstance('Logger');
      $log->logEntry('php', $message, 'EXCEPTION');
   }

   /**
    * @private
    *
    * Creates the exception page.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.02.2009<br />
    */
   protected function buildExceptionPage() {

      // at this point we have to re-include the benchmark timer, because PHP
      // sometimes forgets about this import and throws a
      // Fatal error: Exception thrown without a stack frame in Unknown on line 0
      // exception.
      import('core::benchmark', 'BenchmarkTimer');

      // create page
      $stacktrace = new Page();
      $stacktrace->setContext('core::exceptionhandler');
      $stacktrace->loadDesign('core::exceptionhandler::templates', 'exceptionpage');

      // inject exception information into the document attributes array
      $doc = $stacktrace->getRootDocument();
      $doc->setAttribute('id', $this->generateExceptionID());
      $doc->setAttribute('message', $this->exceptionMessage);
      $doc->setAttribute('number', $this->exceptionNumber);
      $doc->setAttribute('file', $this->exceptionFile);
      $doc->setAttribute('line', $this->exceptionLine);
      $doc->setAttribute('trace', array_reverse($this->exceptionTrace));
      $doc->setAttribute('type', $this->exceptionType);

      // create exception page
      return $stacktrace->transform();
   }

   /**
    * @private
    *
    * Generates the exception id.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.02.2009<br />
    */
   protected function generateExceptionID() {
      return md5($this->exceptionMessage . $this->exceptionNumber . $this->exceptionFile . $this->exceptionLine);
   }

}
?>