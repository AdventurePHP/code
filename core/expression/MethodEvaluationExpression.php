<?php
namespace APF\core\expression;

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
 * @package APF\core\expression
 * @class MethodEvaluationExpression
 *
 * Executes a method access.
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
         throw new Exception('Expression "' . $this->expression . '" invalid for ' . __CLASS__ . '!');
      }

      $close = strpos($this->expression, self::BRACKET_CLOSE, $open);
      if ($close === false) {
         throw new Exception('Expression "' . $this->expression . '" invalid for ' . __CLASS__ . '!');
      }

      $method = substr($this->expression, 0, $open);

      // support on arguments

      if (!method_exists($this->previousResult, $method)) {
         throw new Exception('Instance of type "' . get_class($this->previousResult)
            . '" has no method defined with name "' . $method . '()". Expression: "' . $this->expression . '".');
      }

      return $this->previousResult->{$method}();
   }

   protected function check($expression, $previousResult) {
      if (!is_object($previousResult)) {
         throw new Exception('$previousResult is not of type object! Expression: "' . $expression . '".');
      }
   }

} 