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
namespace APF\tests\suites\core\expression;

use APF\core\expression\MethodEvaluationExpression;

class MethodEvaluationExpressionTest extends \PHPUnit_Framework_TestCase {

   public function testSimpleCall() {
      $model = new ContentModel();
      $expression = new MethodEvaluationExpression('getCssClass()', $model);
      assertEquals($model->getCssClass(), $expression->getResult());
   }

   public function testWithoutArguments() {
      $model = new MethodArgumentsModel();
      $expression = new MethodEvaluationExpression('singleParamCall()', $model);
      assertEquals($model->singleParamCall(), $expression->getResult());
   }

   public function testWithOneArgument() {
      $model = new MethodArgumentsModel();

      $expression = new MethodEvaluationExpression('singleParamCall(false)', $model);
      assertEquals($model->singleParamCall('false'), $expression->getResult());

      $expression = new MethodEvaluationExpression('singleParamCall(true)', $model);
      assertEquals($model->singleParamCall('true'), $expression->getResult());
   }

   public function testWithMultipleArguments() {

      $model = new MethodArgumentsModel();

      $expression = new MethodEvaluationExpression('sumUp(1, 2, 3, 4)', $model);
      assertEquals($model->sumUp(1, 2, 3, 4), $expression->getResult());


      $expression = new MethodEvaluationExpression('concatenate(1, 2, 3, 4)', $model);
      assertEquals($model->concatenate('1', '2', '3', '4'), $expression->getResult());

      $expression = new MethodEvaluationExpression('concatenate(\'1\', \'2\', \'3\', \'4\')', $model);
      assertEquals($model->concatenate('1', '2', '3', '4'), $expression->getResult());

   }

   public function testInvalidPreviousResult() {
      $this->setExpectedException('APF\core\pagecontroller\ParserException');
      $expression = new MethodEvaluationExpression('getFoo()', 'bar');
      $expression->getResult();
   }

   public function testInvalidMethod() {
      $this->setExpectedException('APF\core\pagecontroller\ParserException');
      $model = new ContentModel();
      $expression = new MethodEvaluationExpression('getFoo()', $model);
      $expression->getResult();
   }

   public function testInvalidExpression() {
      $this->setExpectedException('APF\core\pagecontroller\ParserException');
      $model = new ContentModel();
      $expression = new MethodEvaluationExpression('getFoo', $model);
      $expression->getResult();
   }

} 