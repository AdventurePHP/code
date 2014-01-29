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
 * @class ArrayAccessEvaluationExpression
 *
 * Implements a dynamic expression to access an array offset.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.01.2014<br />
 */
class ArrayAccessEvaluationExpression extends EvaluationExpressionBase implements EvaluationExpression {

   const BRACKET_OPEN = '[';
   const BRACKET_CLOSE = ']';

   public function getResult() {

      $open = strpos($this->expression, self::BRACKET_OPEN);
      if ($open === false) {
         throw new Exception('Expression "' . $this->expression . '" invalid for ' . __CLASS__ . '!');
      }

      $close = strpos($this->expression, self::BRACKET_CLOSE, $open);
      if ($close === false) {
         throw new Exception('Expression "' . $this->expression . '" invalid for ' . __CLASS__ . '!');
      }

      $offset = substr($this->expression, $open + 1, $close - $open - 1);

      // extract string in case we have a string offset
      // support both >"< and >'<!
      if (is_string($offset)) {
         $offset = str_replace('\'', '', str_replace('"', '', $offset));
      }

      return $this->previousResult[$offset];
   }

   protected function check($expression, $previousResult) {
      if (!is_array($previousResult)) {
         throw new Exception('$previousResult is not of type array! Expression: "' . $expression . '".');
      }
   }

} 