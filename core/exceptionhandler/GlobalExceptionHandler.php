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

/**
 * @package core::exceptionhandler
 * @class ExceptionHandler
 *
 * Describes the signature of any APF exception handler.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.03.2012<br />
 */
interface ExceptionHandler {

   /**
    * @public
    *
    * This method is intended to take the exception's information and processes it.
    *
    * @param Exception $exception The current exception.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.02.2009<br />
    */
   public function handleException(Exception $exception);
}

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
class DefaultExceptionHandler implements ExceptionHandler {

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
      /* @var $log Logger */
      $log->logEntry('php', $message, 'EXCEPTION');
   }

   /**
    * @private
    *
    * Creates the exception page.
    *
    * @return string the exception page content.
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
    * @return string The unique exception id.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.02.2009<br />
    */
   protected function generateExceptionID() {
      return md5($this->exceptionMessage . $this->exceptionNumber . $this->exceptionFile . $this->exceptionLine);
   }

}

/**
 * @package core::exceptionhandler
 * @class GlobalExceptionHandler
 *
 * This is the APF exception handler automatically registered to handle exceptions.
 * <p/>
 * In case you want to register your custom exception handler, use the <em>registerExceptionHandler()</em>
 * method.
 * <p/>
 * To disable the APF exception handling mechanism use PHP's <em>restore_exception_handler()</em> after
 * including the <em>pagecontroller.php</em> or call
 * <code>
 * GlobalExceptionHandler::disable()
 * </code>
 * In order to programmatically (re-)enable the exception handler, type
 * <code>
 * GlobalExceptionHandler::enable()
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.03.2012<br />
 */
abstract class GlobalExceptionHandler {

   /**
    * @var ExceptionHandler|null The instance of the exception handler to use.
    */
   private static $HANDLER;

   /**
    * @var bool Stores the status (enabled=true, disabled=false).
    */
   private static $ENABLED = true;

   /**
    * @public
    * @static
    *
    * Let's you register an exception handler.
    *
    * @param ExceptionHandler $handler The exception handler that is delegated the exception processing.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public static function registerExceptionHandler(ExceptionHandler $handler) {
      self::$HANDLER = $handler;
   }

   /**
    * @public
    * @static
    *
    * This method is used as the central entry point to the APF's exception management. It delegates the
    * exception handling to the registered handler. In case no handler is registered or the mechanism is
    * disables, nothing will happen.
    *
    * @param Exception $exception The current exception.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public static function handleException(Exception $exception) {
      if (self::$HANDLER === null) {
         // restore the PHP default exception handler to avoid loops or other issues
         restore_exception_handler();
         throw $exception;
      } else {
         try {
            self::$HANDLER->handleException($exception);
         } catch (Exception $exception) {
            // catch exceptions thrown within the exception handler to avoid
            // Fatal error: Exception thrown without a stack frame in Unknown on line 0
            // errors.
            echo 'APF catchable exception: ' . $exception->getMessage() . ' (code: '
                  . $exception->getCode() . ') in ' . $exception->getFile() . ' on line '
                  . $exception->getLine() . '!';
         }
      }
   }

   /**
    * @public
    * @static
    *
    * Disables the APF exception handling mechanism. From this point in time, the PHP default
    * error handler will be used to handle the error.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public static function disable() {
      self::$ENABLED = false;

      // restore PHP's default handler
      restore_exception_handler();
   }

   /**
    * @public
    * @static
    *
    * (Re-)enables the APF exception handling mechanism. In case no exception handler has been
    * registered, the APF default exception handler is used.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public static function enable() {
      self::$ENABLED = true;

      // ensure that an error handler is set
      if (self::$HANDLER === null) {
         self::$HANDLER = new DefaultErrorHandler();
      }

      // (re-)register the APF exception handler
      set_exception_handler(array('GlobalExceptionHandler', 'handleException'));
   }
}

// register the APF error handler to be able to easily configure the error handling mechanism
GlobalExceptionHandler::registerExceptionHandler(new DefaultExceptionHandler());
GlobalExceptionHandler::enable();
