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

use APF\core\expression\ExpressionEvaluator;
use APF\core\pagecontroller\Document;

class ExpressionEvaluatorTest extends \PHPUnit_Framework_TestCase {

   public function testEmptyExpression() {
      assertNull(ExpressionEvaluator::evaluate(new Document(), ''));
   }

   public function testIllegalExpression() {
      assertNull(ExpressionEvaluator::evaluate(new Document(), '->'));
   }

   public function testDataAccess() {
      $node = new Document();
      $model = new ContentModel();
      $node->setData('foo', $model);
      assertEquals($model, ExpressionEvaluator::evaluate($node, 'foo'));
      assertEquals($model->getCssClass(), ExpressionEvaluator::evaluate($node, 'foo->getCssClass()'));
   }

   public function testArrayAccessWithMethodCall() {
      $node = new Document();
      $model = new ContentModel();
      $node->setData(
         'foo',
         array(
            0 => $model,
            'foo' => $model
         )
      );
      assertEquals($model->getCssClass(), ExpressionEvaluator::evaluate($node, 'foo[0]->getCssClass()'));
      assertEquals($model->getMoreLinkModel()->getMoreLabel(), ExpressionEvaluator::evaluate($node, 'foo[\'foo\']->getMoreLinkModel()->getMoreLabel()'));
   }

   public function testMethodChain() {
      $node = new Document();
      $model = new ContentModel();
      $node->setData('foo', $model);
      assertEquals($model, ExpressionEvaluator::evaluate($node, 'foo'));
      assertEquals($model->getMoreLinkModel()->getMoreLabel(), ExpressionEvaluator::evaluate($node, 'foo->getMoreLinkModel()->getMoreLabel()'));
   }

   public function testMultiArrayAccessMethodChain() {
      $node = new Document();
      $model = new ContentModel();
      $node->setData(
         'foo',
         array(
            1 => array(
               2 => $model
            )
         )
      );
      assertEquals($model->getCssClass(), ExpressionEvaluator::evaluate($node, 'foo[1]->bar[2]->getCssClass()'));
   }

   public function testIllegalCallChain() {
      $this->setExpectedException('APF\core\pagecontroller\ParserException');
      ExpressionEvaluator::evaluate(new Document(), '->foo->getMoreLinkModel()->->getMoreLabel()');
   }

   public function testIllegalCall() {
      $this->setExpectedException('APF\core\pagecontroller\ParserException');
      ExpressionEvaluator::evaluate(new Document(), 'foo-> getCssClass()');
   }

} 