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
namespace APF\tests\suites\core\expression;

use APF\core\expression\ArrayAccessEvaluationExpression;
use APF\core\pagecontroller\ParserException;

class ArrayAccessEvaluationExpressionTest extends \PHPUnit_Framework_TestCase {

   const DATA_ATTRIBUTE_NAME = 'foo';

   public function testNumericAccess() {
      $previousResult = $this->getPreviousResult();

      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[0]', $previousResult);
      $this->assertTrue($expression->getResult() instanceof ContentModel);

      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[4711]', $previousResult);
      $this->assertTrue($expression->getResult() instanceof ContentModel);
   }

   private function getPreviousResult() {
      $model = new ContentModel();

      return [
            0     => $model,
            4711  => $model,
            'foo' => $model,
            'bar' => [
                  42    => $model,
                  'baz' => $model
            ],
            42    => [
                  0 => $model,
                  1 => $model
            ]
      ];
   }

   public function testAssociativeAccess() {
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[\'foo\']', $this->getPreviousResult());
      $this->assertTrue($expression->getResult() instanceof ContentModel);
   }

   public function testMultiArrayNumericAccess() {
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[42][1]', $this->getPreviousResult());
      $this->assertTrue($expression->getResult() instanceof ContentModel);
   }

   public function testMultiArrayAssociativeAccess() {
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[42][1]', $this->getPreviousResult());
      $this->assertTrue($expression->getResult() instanceof ContentModel);
   }

   public function testMultiArrayMixedAccess() {
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[\'bar\'][42]', $this->getPreviousResult());
      $this->assertTrue($expression->getResult() instanceof ContentModel);
   }

   public function testInvalidOffset() {
      $this->expectException(ParserException::class);
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[\'baz\']', $this->getPreviousResult());
      $expression->getResult();
   }

   public function testInvalidExpression() {
      $this->expectException(ParserException::class);
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME, []);
      $expression->getResult();
   }

   public function testInvalidPreviousResult() {
      $this->expectException(ParserException::class);
      $expression = new ArrayAccessEvaluationExpression(self::DATA_ATTRIBUTE_NAME, null);
      $expression->getResult();
   }

}
