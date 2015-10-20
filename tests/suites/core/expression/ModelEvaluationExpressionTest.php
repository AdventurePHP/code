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

use APF\core\expression\ModelEvaluationExpression;
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\DomNode;
use APF\core\pagecontroller\ParserException;
use DateTime;

class ModelEvaluationExpressionTest extends \PHPUnit_Framework_TestCase {

   const DATA_ATTRIBUTE_NAME = 'foo';

   public function testHappyCase() {
      $expression = new ModelEvaluationExpression(self::DATA_ATTRIBUTE_NAME, $this->getDocument(self::DATA_ATTRIBUTE_NAME));
      $this->assertTrue($expression->getResult() instanceof ContentModel);
   }

   private function getDocument($dataAttributeName) {
      $document = new Document();
      $document->setData($dataAttributeName, new ContentModel());

      return $document;
   }

   public function testWithArrayAccess() {

      $expression = new ModelEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[123]', $this->getDocument(self::DATA_ATTRIBUTE_NAME));
      $this->assertTrue($expression->getResult() instanceof ContentModel);

      $expression = new ModelEvaluationExpression(self::DATA_ATTRIBUTE_NAME . '[\'123\']', $this->getDocument(self::DATA_ATTRIBUTE_NAME));
      $this->assertTrue($expression->getResult() instanceof ContentModel);

   }

   public function testWrongPreviousResult() {
      $this->setExpectedException(ParserException::class);
      new ModelEvaluationExpression('', new DateTime());
   }

   public function testMissingPreviousResult() {
      $this->setExpectedException(ParserException::class);
      new ModelEvaluationExpression('', null);
   }

   public function testWrongDataAttributeReference() {
      $expression = new ModelEvaluationExpression('bar', $this->getDocument(self::DATA_ATTRIBUTE_NAME));
      $this->assertTrue($expression->getResult() === null);
   }

   public function testThisModelExpression() {
      $expected = new Document();
      $expected->setAttribute('foo', 'bar');

      $expression = new ModelEvaluationExpression('this', $expected);

      /* @var $result DomNode */
      $result = $expression->getResult();

      $this->assertEquals($expected, $result);
      $this->assertEquals($expected->getAttribute('foo'), $result->getAttribute('foo'));
   }

}
