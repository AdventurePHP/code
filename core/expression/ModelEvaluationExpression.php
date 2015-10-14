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

use APF\core\pagecontroller\DomNode;
use APF\core\pagecontroller\ParserException;

/**
 * Evaluates a model access - merely involved as a first step accessing data attributes of a DOM node.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.01.2014<br />
 * Version 0.2, 07.03.2015 (ID#118: added support for "this" keyword to return the current document instance)<br />
 */
class ModelEvaluationExpression extends EvaluationExpressionBase implements EvaluationExpression {

   const THIS_MODEL_PARAM_NAME = 'this';

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

      // In case the model name is "this" the current document is returned. Current in this case
      // means the Document instance where the expression is defined. Example:
      //
      // <html:iterator name="outer">
      //    <iterator:item>
      //       ${this->getIterator('inner')->getData('foo')}
      //    </iterator:item>
      // </html:iterator>
      //
      // Here, "this" will evaluate to the instance of the <iterator:item /> (HtmlIteratorItemTag).
      if ($name === self::THIS_MODEL_PARAM_NAME) {
         return $this->previousResult;
      } else {
         /* @var $document DomNode */
         $document = $this->previousResult;

         return $document->getData($name);
      }
   }

   protected function check($expression, $previousResult) {
      if (!($previousResult instanceof DomNode)) {
         throw new ParserException('$previousResult is not of type Document but "' . gettype($previousResult) . '"! Expression: "' . $expression . '".');
      }
   }

}
