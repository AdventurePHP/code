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

use APF\core\expression\ExpressionEvaluator;
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\ParserException;
use PHPUnit\Framework\TestCase;

class ExpressionEvaluatorTest extends TestCase {

   public function testEmptyExpression() {
      $doc = new Document();
      $this->assertNull(ExpressionEvaluator::evaluate($doc, ''));
   }

   public function testIllegalExpression() {
      $doc = new Document();
      $this->assertNull(ExpressionEvaluator::evaluate($doc, '->'));
   }

   public function testDataAccess() {
      $node = new Document();
      $model = new ContentModel();
      $node->setData('foo', $model);
      $this->assertEquals($model, ExpressionEvaluator::evaluate($node, 'foo'));
      $this->assertEquals($model->getCssClass(), ExpressionEvaluator::evaluate($node, 'foo->getCssClass()'));
   }

   public function testArrayAccessWithMethodCall() {
      $node = new Document();
      $model = new ContentModel();
      $node->setData(
            'foo',
            [
                  0 => $model,
                  'foo' => $model
            ]
      );
      $this->assertEquals($model->getCssClass(), ExpressionEvaluator::evaluate($node, 'foo[0]->getCssClass()'));
      $this->assertEquals($model->getMoreLinkModel()->getLabel(), ExpressionEvaluator::evaluate($node, 'foo[\'foo\']->getMoreLinkModel()->getLabel()'));
   }

   public function testMethodChain() {
      $node = new Document();
      $model = new ContentModel();
      $node->setData('foo', $model);
      $this->assertEquals($model, ExpressionEvaluator::evaluate($node, 'foo'));
      $this->assertEquals($model->getMoreLinkModel()->getLabel(), ExpressionEvaluator::evaluate($node, 'foo->getMoreLinkModel()->getLabel()'));
   }

   public function testMultiArrayAccessMethodChain() {
      $node = new Document();
      $model = new ContentModel();
      $node->setData(
            'foo',
            [
                  1 => [
                        2 => $model
                  ]
            ]
      );
      $this->assertEquals($model->getCssClass(), ExpressionEvaluator::evaluate($node, 'foo[1]->bar[2]->getCssClass()'));
   }

   public function testIllegalCallChain() {
      $this->expectException(ParserException::class);
      $doc = new Document();
      ExpressionEvaluator::evaluate($doc, '->foo->getMoreLinkModel()->->getLabel()');
   }

   public function testIllegalCall() {
      $this->expectException(ParserException::class);
      $doc = new Document();
      ExpressionEvaluator::evaluate($doc, 'foo-> getCssClass()');
   }

}
