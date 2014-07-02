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
namespace APF\core\exceptionhandler;

use Exception;

/**
 * @package APF\core\exceptionhandler
 * @class CLIExceptionHandler
 *
 * Implements a cli exception handler for uncaught exceptions.
 *
 * @author Tobias Lückel
 * @version
 * Version 0.1, 25.11.2013<br />
 */
class CLIExceptionHandler extends DefaultExceptionHandler {

   const TAB = "\t";

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
      echo $this->buildExceptionOutput();
   }

   /**
    * @protected
    *
    * Creates the exception output.
    *
    * @return string the exception output.
    *
    * @author Tobias Lückel[Megger]
    * @version
    * Version 0.1, 25.11.2013<br />
    */
   protected function buildExceptionOutput() {
      $output = PHP_EOL;
      $output .= '[' . $this->generateExceptionID() . ']';
      $output .= '[' . $this->exceptionNumber . ']';
      $output .= ' ' . $this->exceptionMessage . PHP_EOL;
      $output .= self::TAB . $this->exceptionFile . ':' . $this->exceptionLine . PHP_EOL;
      $output .= 'Stacktrace:' . PHP_EOL;

      $stacktrace = array_reverse($this->exceptionTrace);
      foreach ($stacktrace as $item) {
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
         if (isset($item['file'])) {
            $output .= ':' . $item['line'];
         }
         $output .= PHP_EOL;
      }

      $output .= PHP_EOL;

      return $output;
   }

}