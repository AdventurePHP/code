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
namespace APF\core\expression;

use APF\core\pagecontroller\ParserException;

/**
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

      // validate whether we are facing a correct expression
      if (strpos($this->expression, self::BRACKET_OPEN) === false) {
         throw new ParserException('No opening bracket found for expression "' . $this->expression . '"!');
      }

      $result = $this->previousResult;

      $pointer = 0;

      while (true) {

         $open = strpos($this->expression, self::BRACKET_OPEN, $pointer);
         if ($open === false) {
            break;
         }

         $close = strpos($this->expression, self::BRACKET_CLOSE, $open);
         if ($close === false) {
            throw new ParserException('No closing bracket found for expression "' . $this->expression . '"!');
         }

         $pointer = $close;

         $offset = substr($this->expression, $open + 1, $close - $open - 1);

         // extract string in case we have a string offset
         // support both >"< and >'<!
         if (is_string($offset)) {
            $offset = str_replace('\'', '', str_replace('"', '', $offset));
         }

         if (isset($result[$offset])) {
            $result = $result[$offset];
         } else {
            throw new ParserException('Invalid offset "' . $offset . '" for expression "' . $this->expression . '"!');
         }
      }

      return $result;
   }

   protected function check($expression, $previousResult) {
      if (!is_array($previousResult)) {
         throw new ParserException('$previousResult is not of type array but "' . gettype($previousResult) . '"! Expression: "' . $expression . '".');
      }
   }

} 