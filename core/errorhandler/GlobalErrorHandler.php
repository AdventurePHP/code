<?php
namespace APF\core\errorhandler;

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
use APF\core\benchmark\BenchmarkTimer;
use APF\core\logging\LogEntry;
use APF\core\logging\Logger;
use APF\core\logging\SimpleLogEntry;
use APF\core\pagecontroller\Page;
use APF\core\registry\Registry;
use APF\core\singleton\Singleton;

/**
 * @package core::errorhandler
 * @class ErrorHandler
 *
 * Describes the signature of any APF error handler.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.03.2012<br />
 */
interface ErrorHandler {

   /**
    * @public
    *
    * This method is intended to take the error's information and processes it.
    *
    * @param int $errorNumber The error number.
    * @param string $errorMessage The error message.
    * @param string $errorFile The file the error occurred in.
    * @param int $errorLine The line the error occurred at.
    * @param array $errorContext The error context (symbol table at the error).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public function handleError($errorNumber, $errorMessage, $errorFile, $errorLine, array $errorContext);

}

/**
 * @package core::errorhandler
 * @class DefaultErrorHandler
 *
 * Implements the default error handler of the APF. Logs errors to a logfile and displays the
 * standard error page.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.01.2009<br />
 */
class DefaultErrorHandler implements ErrorHandler {

   /**
    * @var int Error number.
    */
   protected $errorNumber;

   /**
    * @var string Error message,
    */
   protected $errorMessage;

   /**
    * @var string Error file.
    */
   protected $errorFile;

   /**
    * @var int Error line.
    */
   protected $errorLine;

   /**
    * @var array Error line.
    */
   protected $errorContext;

   public function handleError($errorNumber, $errorMessage, $errorFile, $errorLine, array $errorContext) {

      // fill attributes
      $this->errorNumber = $errorNumber;
      $this->errorMessage = $errorMessage;
      $this->errorFile = $errorFile;
      $this->errorLine = $errorLine;
      $this->errorContext = $errorContext;

      // log error
      $this->logError();

      // build nice error page
      echo $this->buildErrorPage();
   }

   /**
    * @protected
    *
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
            Registry::retrieve('apf::core', 'InternalLogTarget'),
            $message,
            LogEntry::SEVERITY_ERROR
         )
      );
   }

   /**
    * @protected
    *
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
      $stacktrace = new Page();
      $stacktrace->setContext('core::errorhandler');
      $stacktrace->loadDesign('core::errorhandler::templates', 'errorpage');

      // inject error information into the document attributes array
      $doc = & $stacktrace->getRootDocument();
      $doc->setAttribute('id', $this->generateErrorID());
      $doc->setAttribute('message', $this->errorMessage);
      $doc->setAttribute('number', $this->errorNumber);
      $doc->setAttribute('file', $this->errorFile);
      $doc->setAttribute('line', $this->errorLine);

      // create error page
      return $stacktrace->transform();
   }

   /**
    * @private
    *
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

/**
 * @package core::errorhandler
 * @class GlobalErrorHandler
 *
 * This is the APF error handler automatically registered to handle errors.
 * <p/>
 * In case you want to register your custom error handler, use the <em>registerErrorHandler()</em>
 * method.
 * <p/>
 * To disable the APF error handling mechanism use PHP's <em>restore_error_handler()</em> after
 * including the <em>pagecontroller.php</em> or call
 * <code>
 * GlobalErrorHandler::disable()
 * </code>
 * In order to (re-)enable the error handler by code, type
 * <code>
 * GlobalErrorHandler::enable()
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.03.2012<br />
 */
abstract class GlobalErrorHandler {

   /**
    * @var ErrorHandler|null The instance of the error handler to use.
    */
   private static $HANDLER;

   /**
    * @public
    * @static
    *
    * Let's you register an error handler.
    *
    * @param ErrorHandler $handler The error handler that is delegated the error processing.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public static function registerErrorHandler(ErrorHandler $handler) {
      self::$HANDLER = $handler;
   }

   /**
    * @public
    * @static
    *
    * This method is used as the central entry point to the APF's error management. It delegates the
    * error handling to the registered handler. In case no handler is registered or the mechanism is
    * disables, nothing will happen.
    *
    * @param int $errorNumber The error number.
    * @param string $errorMessage The error message.
    * @param string $errorFile The file the error occurred in.
    * @param int $errorLine The line the error occurred at.
    * @param array $errorContext The error context (symbol table at the error).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public static function handleError($errorNumber, $errorMessage, $errorFile, $errorLine, array $errorContext) {

      // Don't raise error, if @ was applied
      if (error_reporting() == 0) {
         return;
      }

      if (self::$HANDLER === null) {
         // restore the PHP default error handler to avoid loops or other issues
         restore_error_handler();
         trigger_error($errorMessage, (int)$errorNumber);
      } else {
         self::$HANDLER->handleError($errorNumber, $errorMessage, $errorFile, $errorLine, $errorContext);
      }
   }

   /**
    * @public
    * @static
    *
    * Disables the APF error handling mechanism. From this point in time, the PHP default
    * error handler will be used to handle the error.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public static function disable() {
      // restore PHP's default handler
      restore_error_handler();
   }

   /**
    * @public
    * @static
    *
    * (Re-)enables the APF error handling mechanism. In case no error handler has been
    * registered, the APF default error handler is used.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public static function enable() {

      // ensure that an error handler is set
      if (self::$HANDLER === null) {
         self::$HANDLER = new DefaultErrorHandler();
      }

      // (re-)register the APF error handler
      set_error_handler(array('APF\core\errorhandler\GlobalErrorHandler', 'handleError'));
   }

}
