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

use APF\core\pagecontroller\ParserException;

/**
 * Executes a method access including dynamic string or integer arguments.
 * <p/>
 * Boolean values are not supported since no explicit type cast is executed but
 * values are treated as string - either in simple quotes or literally.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.01.2014<br />
 */
class MethodEvaluationExpression extends EvaluationExpressionBase implements EvaluationExpression {

   const BRACKET_OPEN = '(';
   const BRACKET_CLOSE = ')';

   public function getResult() {

      $open = strpos($this->expression, self::BRACKET_OPEN);
      if ($open === false) {
         throw new ParserException('No opening bracket found for expression "' . $this->expression . '"!');
      }

      $close = strpos($this->expression, self::BRACKET_CLOSE, $open);
      if ($close === false) {
         throw new ParserException('No closing bracket found for expression "' . $this->expression . '"!');
      }

      $method = substr($this->expression, 0, $open);

      // extract arguments passed to the method (only trivial data types such as int and string supported)
      $argumentsExpression = substr($this->expression, $open + 1, $close - $open - 1);
      if (empty($argumentsExpression)) {
         $arguments = [];
      } else {
         // sanitize arguments string to pass arguments as they are meant to be
         $arguments = explode(',', str_replace('\'', '', str_replace(' ', '', $argumentsExpression)));
      }

      if (!method_exists($this->previousResult, $method)) {
         throw new ParserException('Instance of type "' . get_class($this->previousResult)
               . '" has no method defined with name "' . $method . '()". Expression: "' . $this->expression . '".');
      }

      return call_user_func_array([$this->previousResult, $method], $arguments);
   }

   protected function check($expression, $previousResult) {
      if (!is_object($previousResult)) {
         throw new ParserException('$previousResult is not of type object but "' . gettype($previousResult) . '"! Expression: "' . $expression . '".');
      }
   }

}
