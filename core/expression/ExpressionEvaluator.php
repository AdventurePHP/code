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

use APF\core\pagecontroller\DomNode;
use APF\core\pagecontroller\ParserException;
use Exception;

/**
 * Evaluates APF dynamic expressions allowing a more comfortable way of templating.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.01.2014<br />
 */
abstract class ExpressionEvaluator {

   /**
    * Evaluates a dynamic expression. Acts both as a factory as well as an
    * abstraction component to evaluate expressions.
    *
    * @param DomNode $dataNode The APF DOM node that can be used to retrieve the model information.
    * @param string $expressionString The APF dynamic expression string.
    *
    * @return string The result of the expression evaluation.
    * @throws Exception In case evaluation of the given expression fails.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.01.2014<br />
    */
   public static function evaluate(DomNode $dataNode, string $expressionString) {

      $parts = explode('->', $expressionString);

      try {
         // always evaluate the first part separately (even if we have array access expressions later on)
         $expression = new ModelEvaluationExpression($parts[0], $dataNode);
         $result = $expression->getResult();

         foreach ($parts as $part) {

            $openingNormalBracket = strpos($part, MethodEvaluationExpression::BRACKET_OPEN);
            if ($openingNormalBracket !== false) {

               $expression = new MethodEvaluationExpression($part, $result);
               $result = $expression->getResult();

            } else {

               $openingSquareBracket = strpos($part, ArrayAccessEvaluationExpression::BRACKET_OPEN);
               if ($openingSquareBracket !== false) {

                  $expression = new ArrayAccessEvaluationExpression($part, $result);
                  $result = $expression->getResult();

               }
            }

         }
      } catch (Exception $e) {
         // re-throw with more content
         throw new ParserException('Execution of expression "' . $expressionString . '" failed with message "' . $e->getMessage() . '"', E_USER_ERROR, $e);
      }

      return $result;
   }

}
