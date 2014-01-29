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
use APF\core\pagecontroller\Document;
use Exception;

/**
 * @package APF\core\expression
 * @class ModelEvaluationExpression
 *
 * Evaluates a model access - merely involved as a first step accessing data attributes of a DOM node.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.01.2014<br />
 */
class ModelEvaluationExpression extends EvaluationExpressionBase implements EvaluationExpression {

   public function getResult() {

      $name = $this->expression;

      $open = strpos($this->expression, ArrayAccessEvaluationExpression::BRACKET_OPEN);
      if ($open !== false) {
         $name = substr($name, 0, $open);
      }

      $open = strpos($this->expression, MethodEvaluationExpression::BRACKET_OPEN);
      if ($open !== false) {
         $name = substr($name, 0, $open);
      }

      /* @var $document Document */
      $document = $this->previousResult;
      return $document->getData($name);
   }

   protected function check($expression, $previousResult) {
      if (!($previousResult instanceof Document)) {
         throw new Exception('$previousResult is not of type Document. Expression: "' . $expression . '".');
      }
   }

} 