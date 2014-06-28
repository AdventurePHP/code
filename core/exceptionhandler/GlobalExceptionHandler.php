<?php
namespace APF\core\exceptionhandler;

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
use Exception;

/**
 * This is the APF exception handler automatically registered to handle exceptions.
 * <p/>
 * In case you want to register your custom exception handler, use the <em>registerExceptionHandler()</em>
 * method.
 * <p/>
 * To disable the APF exception handling mechanism use PHP's <em>restore_exception_handler()</em> after
 * including the <em>bootstrap.php</em> or call.
 * <code>
 * GlobalExceptionHandler::disable()
 * </code>
 * In order to (re-)enable the exception handler by code, type
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
    * The instance of the exception handler to use.
    *
    * @var ExceptionHandler|null $HANDLER
    */
   private static $HANDLER;

   /**
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
    * This method is used as the central entry point to the APF's exception management. It delegates the
    * exception handling to the registered handler. In case no handler is registered or the mechanism is
    * disables, nothing will happen.
    *
    * @param Exception $exception The current exception.
    *
    * @throws Exception In case the APF exception handler is disabled, the original exception is thrown.
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
    * Disables the APF exception handling mechanism. From this point in time, the PHP default
    * error handler will be used to handle the error.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public static function disable() {
      // restore PHP's default handler
      restore_exception_handler();
   }

   /**
    * (Re-)enables the APF exception handling mechanism. In case no exception handler has been
    * registered, the APF default exception handler is used.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public static function enable() {

      // ensure that an error handler is set
      if (self::$HANDLER === null) {
         self::$HANDLER = new DefaultExceptionHandler();
      }

      // (re-)register the APF exception handler
      set_exception_handler(array('APF\core\exceptionhandler\GlobalExceptionHandler', 'handleException'));
   }
}
