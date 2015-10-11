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

/**
 * This is the APF error handler automatically registered to handle errors.
 * <p/>
 * In case you want to register your custom error handler, use the <em>registerErrorHandler()</em>
 * method.
 * <p/>
 * To disable the APF error handling mechanism use PHP's <em>restore_error_handler()</em> after
 * including the <em>bootstrap.php</em> or call.
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
 * Version 0.2, 16.05.2014 (ID#190: enable logging/catching of fatal errors with a separate shutdown function)<br />
 */
abstract class GlobalErrorHandler {

   /**
    * The instance of the error handler to use.
    *
    * @var ErrorHandler|null $HANDLER
    */
   private static $HANDLER;

   /**
    * Indicates whether or not fatal errors are handled by the GlobalErrorHandler.
    *
    * @var bool $catchFatalErrors
    */
   private static $catchFatalErrors = true;

   /**
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

      // disable fatal error catching as well
      self::$catchFatalErrors = false;
   }

   /**
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
      set_error_handler([GlobalErrorHandler::class, 'handleError']);

      // enable fatal error catching
      self::$catchFatalErrors = true;

      // Register callback for catching fatal errors. As this registration cannot be undone, a
      // separate flag is maintained to switch off this handling using <em>disable()</em>.
      register_shutdown_function([GlobalErrorHandler::class, 'handleFatalError']);
   }

   /**
    * Shutdown function registered during enabling the GlobalErrorHandler to catch fatal errors
    * and handle identically to other errors caught by <em>handleError()</em>.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.05.2014 (ID#190: introduced fatal error catching)<br />
    */
   public static function handleFatalError() {

      // Workaround for shutdown functions cannot be unregistered: check whether or not
      // APF global error handling has been turned off. If yes, nothing to do.
      if (self::$catchFatalErrors === false) {
         return;
      }

      // only handle *real* issues but not warnings (e.g. deprecated PHP API) or notices
      $errorTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING];

      // Chain to the "normal" error handling for consistency reasons (e.g. using a logging-only
      // production error handler for security reasons).
      $error = error_get_last();
      if ($error && in_array($error['type'], $errorTypes)) {
         self::handleError((int) $error['type'], $error['message'], $error['file'], (int) $error['line']);
      }

   }

   /**
    * This method is used as the central entry point to the APF's error management. It delegates the
    * error handling to the registered handler. In case no handler is registered or the mechanism is
    * disables, nothing will happen.
    *
    * @param int $errorNumber The error number.
    * @param string $errorMessage The error message.
    * @param string $errorFile The file the error occurred in.
    * @param int $errorLine The line the error occurred at.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.03.2012<br />
    */
   public static function handleError($errorNumber, $errorMessage, $errorFile, $errorLine) {

      // Don't raise error, if @ was applied
      if (error_reporting() == 0) {
         return;
      }

      if (self::$HANDLER === null) {
         // restore the PHP default error handler to avoid loops or other issues
         restore_error_handler();
         trigger_error($errorMessage, (int) $errorNumber);
      } else {
         self::$HANDLER->handleError($errorNumber, $errorMessage, $errorFile, $errorLine);
      }
   }

}
