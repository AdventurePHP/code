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

// set the exception handling function
set_exception_handler('exceptionHandler');

// include necessary classes
import('core::exceptionhandler', 'ExceptionHandlerDefinition');

// setup the registry with the default APF exception handler
Registry::register('apf::core', 'ExceptionHandler', new ExceptionHandlerDefinition('core::exceptionhandler', 'DefaultExceptionHandler'));

/**
 * @package core::exceptionhandler
 * @function exceptionHandler
 *
 * This function is the global APF exception handler function. Calls the exception manager
 * configured in the registry.
 *
 * @param Exception $exception the thrown exception
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.02.2009<br />
 */
function exceptionHandler($exception) {

   // raise error and display error message
   $exceptionHandlerDef = Registry::retrieve('apf::core', 'ExceptionHandler');
   /* @var $exceptionHandlerDef ExceptionHandlerDefinition */

   if ($exceptionHandlerDef !== null && $exceptionHandlerDef instanceof ExceptionHandlerDefinition) {

      // get handler params
      $namespace = $exceptionHandlerDef->getNamespace();
      $class = $exceptionHandlerDef->getClass();

      // include exception handler
      import($namespace, $class);

      // execute exception handler
      $excHandler = new $class();
      /* @var $excHandler AbstractExceptionHandler */

      if ($excHandler instanceof AbstractExceptionHandler) {
         try {
            $excHandler->handleException($exception);
         } catch (Exception $exception) {
            // catch exceptions thrown within the exception handler to avoid
            // Fatal error: Exception thrown without a stack frame in Unknown on line 0
            // errors.
            echo 'APF catchable exception: ' . $exception->getMessage() . ' (code: '
                 . $exception->getCode() . ') in ' . $exception->getFile() . ' on line '
                 . $exception->getLine() . '!';
         }
      } else {
         echo 'APF catchable exception: ' . $exception->getMessage() . ' (code: '
              . $exception->getCode() . ') in ' . $exception->getFile() . ' on line '
              . $exception->getLine() . '!';
      }

   } else {
      echo 'APF catchable exception: ' . $exception->getMessage() . ' (code: '
           . $exception->getCode() . ') in ' . $exception->getFile() . ' on line '
           . $exception->getLine() . '!';
   }

}

?>