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

/**
 * @package APF\core\errorhandler
 * @class CLIErrorHandler
 *
 * Implements a cli error handler. Logs errors to a logfile and displays
 * output to cli.
 *
 * @author Tobias Lückel
 * @version
 * Version 0.1, 25.11.2013<br />
 */
class CLIErrorHandler extends DefaultErrorHandler {

   const TAB = "\t";

   public function handleError($errorNumber, $errorMessage, $errorFile, $errorLine, array $errorContext) {
      // fill attributes
      $this->errorNumber = $errorNumber;
      $this->errorMessage = $errorMessage;
      $this->errorFile = $errorFile;
      $this->errorLine = $errorLine;

      // log error
      $this->logError();

      // build nice error page
      echo $this->buildErrorOutput();
   }

   /**
    * @protected
    *
    * Creates the error output.
    *
    * @return string The APF error output.
    *
    * @author Tobias Lückel[Megger]
    * @version
    * Version 0.1, 25.11.2013<br />
    */
   protected function buildErrorOutput() {
      $output = PHP_EOL;
      $output .= '[' . $this->generateErrorID() . ']';
      $output .= '[' . $this->errorNumber . ']';
      $output .= ' ' . $this->errorMessage . PHP_EOL;
      $output .= self::TAB . $this->errorFile . ':' . $this->errorLine . PHP_EOL;
      $output .= 'Stacktrace:' . PHP_EOL;

      $stacktrace = array_reverse(debug_backtrace());
      foreach ($stacktrace as $item) {
         // don't display any further messages, because these belong to the error manager
         if (isset($item['function']) && preg_match('/handleError/i', $item['function'])) {
            break;
         }
         $output .= self::TAB;
         if (isset($item['class'])) {
            $output .= $item['class'];
         }
         if (isset($item['type'])) {
            $output .= $item['type'];
         }
         if (isset($item['function'])) {
            $output .= $item['function'] . '()';
         }
         $output .= PHP_EOL;
         $output .= self::TAB . self::TAB;
         if (isset($item['file'])) {
            $output .= $item['file'];
         }
         if (isset($item['line'])) {
            $output .= ':' . $item['line'];
         }
         $output .= PHP_EOL;
      }

      $output .= PHP_EOL;

      return $output;
   }

}