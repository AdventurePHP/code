<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\core\expression;

/**
 * Helps parsing arguments specified in APF's template expressions.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 24.02.2018b<br />
 */
class ArgumentParser {

   /**
    * @param string $expression Template expression w/ multiple (string) arguments.
    * @return array The sanitized list if arguments.
    */
   public static function getArguments(string $expression) {

      $expression = trim($expression);
      if (empty($expression) && strval($expression) !== '0') {
         return [];
      }

      $arguments = explode(',', $expression);

      foreach ($arguments as &$argument) {

         $argument = trim($argument);

         // trim leading ot trailing single and double quotes
         $argument = trim($argument, '\'');
         $argument = trim($argument, '"');

         // trim content of argument to allow correct comparision
         $argument = trim($argument);
      }

      return $arguments;
   }

}
