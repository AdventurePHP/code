<?php
namespace APF\tests\suites\core\expression;

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
use APF\core\expression\ArrayAccessEvaluationExpression;

class ArrayAccessEvaluationExpressionTest extends \PHPUnit_Framework_TestCase {

   const DATA_ATTRIBUTE_NAME = 'foo';

   private function getPreviousResult() {
      $model = new ContentModel();
      return array(
         0 => $model,
         4711 => $model,
         'foo' => $model,
         'bar' => array(
            42 => $model,
            'baz' => $model
         ),
         42 => array(
            0 => $model,
            1 => $model
         )
      );
   }

   public function testNumericAccess() {
      $previousResult = $this->getPreviousResult();

      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[0]', $previousResult);
      assertTrue($expression->getResult() instanceof ContentModel);

      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[4711]', $previousResult);
      assertTrue($expression->getResult() instanceof ContentModel);
   }

   public function testAssociativeAccess() {
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[\'foo\']', $this->getPreviousResult());
      assertTrue($expression->getResult() instanceof ContentModel);
   }

   public function testMultiArrayNumericAccess() {
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[42][1]', $this->getPreviousResult());
      assertTrue($expression->getResult() instanceof ContentModel);
   }

   public function testMultiArrayAssociativeAccess() {
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[42][1]', $this->getPreviousResult());
      assertTrue($expression->getResult() instanceof ContentModel);
   }

   public function testMultiArrayMixedAccess() {
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[\'bar\'][42]', $this->getPreviousResult());
      assertTrue($expression->getResult() instanceof ContentModel);
   }

   public function testInvalidOffset() {
      $this->setExpectedException('APF\core\pagecontroller\ParserException');
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[\'baz\']', $this->getPreviousResult());
      $expression->getResult();
   }

   public function testInvalidExpression() {
      $this->setExpectedException('APF\core\pagecontroller\ParserException');
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME, array());
      $expression->getResult();
   }

   public function testInvalidPreviousResult() {
      $this->setExpectedException('APF\core\pagecontroller\ParserException');
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME, null);
      $expression->getResult();
   }

} 