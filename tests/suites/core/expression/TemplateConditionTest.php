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

use APF\core\expression\TemplateCondition;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Tests template condition expressions.
 */
class TemplateConditionTest extends TestCase {

   public function testGetArgument() {

      $method = new ReflectionMethod(TemplateCondition::class, 'getArgument');
      $method->setAccessible(true);

      $this->assertEquals([], $method->invokeArgs(null, ['foo()']));
      $this->assertEquals([], $method->invokeArgs(null, ['foo( )']));
      $this->assertEquals(['0'], $method->invokeArgs(null, ['foo(0)']));
      $this->assertEquals(['1'], $method->invokeArgs(null, ['foo(1)']));
      $this->assertEquals(['1'], $method->invokeArgs(null, ['foo( 1)']));
      $this->assertEquals(['1'], $method->invokeArgs(null, ['foo( 1 )']));
      $this->assertEquals(['1', '2'], $method->invokeArgs(null, ['foo(1,2)']));
      $this->assertEquals(['1', '2', '3'], $method->invokeArgs(null, ['foo(1,2 , 3 )']));
      $this->assertEquals(['bar', 'baz'], $method->invokeArgs(null, ['foo(\'bar\', \'baz\')']));

   }

   public function testEvaluate1() {

      $this->assertTrue(TemplateCondition::applies('true()', true));
      $this->assertFalse(TemplateCondition::applies('true()', false));

      $this->assertTrue(TemplateCondition::applies('false()', false));
      $this->assertFalse(TemplateCondition::applies('false()', true));

   }

   public function testEvaluate2() {

      $this->assertTrue(TemplateCondition::applies('empty()', null));
      $this->assertTrue(TemplateCondition::applies('empty()', ''));
      $this->assertTrue(TemplateCondition::applies('empty()', ' '));
      $this->assertFalse(TemplateCondition::applies('empty()', 'foo'));

      $this->assertTrue(TemplateCondition::applies('notEmpty()', 'foo'));
      $this->assertFalse(TemplateCondition::applies('notEmpty()', null));
      $this->assertFalse(TemplateCondition::applies('notEmpty()', ''));

   }

   public function testEvaluate3() {
      $this->expectException(InvalidArgumentException::class);
      TemplateCondition::applies('matches()', 'foo');
   }


   public function testEvaluate4() {
      $this->assertTrue(TemplateCondition::applies('matches(\'foo\')', 'foo'));
      $this->assertFalse(TemplateCondition::applies('matches(\'foo\')', 'bar'));
      $this->assertFalse(TemplateCondition::applies('matches(\'foo\')', ''));
   }

   public function testEvaluate5() {
      $this->expectException(InvalidArgumentException::class);
      TemplateCondition::applies('longerThan()', 'foo');
   }

   public function testEvaluate6() {
      $this->assertTrue(TemplateCondition::applies('longerThan(1)', 'foo'));
      $this->assertTrue(TemplateCondition::applies('longerThan(10)', 'foo-foo-foo'));
      $this->assertTrue(TemplateCondition::applies('longerThan(0)', 'f'));
      $this->assertFalse(TemplateCondition::applies('longerThan(0)', ''));
      $this->assertFalse(TemplateCondition::applies('longerThan(0)', null));
   }

   public function testEvaluate7() {
      $this->expectException(InvalidArgumentException::class);
      TemplateCondition::applies('shorterThan()', '');
   }

   public function testEvaluate8() {
      $this->assertTrue(TemplateCondition::applies('shorterThan(1)', ''));
      $this->assertFalse(TemplateCondition::applies('shorterThan(0)', ''));
      $this->assertFalse(TemplateCondition::applies('shorterThan(10)', '1234567890'));
      $this->assertTrue(TemplateCondition::applies('shorterThan(10)', '123456789'));
   }

   public function testEvaluate9() {
      $this->expectException(InvalidArgumentException::class);
      TemplateCondition::applies('length()', '');
   }

   public function testEvaluate10() {
      $this->assertTrue(TemplateCondition::applies('length(10)', '1234567890'));
      $this->assertFalse(TemplateCondition::applies('length(10)', '123456789'));
      $this->assertTrue(TemplateCondition::applies('length(0)', ''));
      $this->assertTrue(TemplateCondition::applies('length(0)', null));
   }

   public function testEvaluate11() {
      $this->expectException(InvalidArgumentException::class);
      TemplateCondition::applies('between()', '');
   }

   public function testEvaluate12() {
      $this->expectException(InvalidArgumentException::class);
      TemplateCondition::applies('between(1)', '');
   }

   public function testEvaluate13() {
      $this->assertTrue(TemplateCondition::applies('between(5,10)', '123456'));
      $this->assertFalse(TemplateCondition::applies('between(5,10)', 'foo'));
      $this->assertFalse(TemplateCondition::applies('between(5,10)', '1234567890123'));
      $this->assertTrue(TemplateCondition::applies('between(5,10)', '12345'));
      $this->assertTrue(TemplateCondition::applies('between(5,10)', '1234567890'));
   }

   public function testEvaluate14() {
      $this->expectException(InvalidArgumentException::class);
      TemplateCondition::applies('contains()', '');
   }

   public function testEvaluate15() {
      $this->assertTrue(TemplateCondition::applies('contains(foo)', '--foo-bar--'));
      $this->assertTrue(TemplateCondition::applies('contains(b)', 'b'));
      $this->assertFalse(TemplateCondition::applies('contains(test)', 'foo-foo-foo'));
      $this->assertFalse(TemplateCondition::applies('contains(10)', '1234567890'));
      $this->assertTrue(TemplateCondition::applies('contains(10)', '2345671089'));
   }

   /**
    * ID#301: test capability to work with regular expressions
    */
   public function testEvaluate16() {
      $this->assertTrue(TemplateCondition::applies('regExp(\'#^foo#\')', 'foobar'));
      $this->assertFalse(TemplateCondition::applies('regExp(\'#^foo#\')', 'barfoo'));
      $this->assertTrue(TemplateCondition::applies('regExp(\'#^foo$#\')', 'foo'));
      $this->assertFalse(TemplateCondition::applies('regExp(\'#^foo$#\')', 'foo '));
      $this->assertFalse(TemplateCondition::applies('regExp(\'#^foo$#\')', 'foobar'));
      $this->assertTrue(TemplateCondition::applies('regExp(\'#^f([o]{4})#\')', 'foooo'));
   }

   /**
    * ID#301: test wrapping issues with regular expression compilation or execution into an exception
    */
   public function testEvaluate17() {
      $this->expectException(InvalidArgumentException::class);
      TemplateCondition::applies('regExp(\'^foo\')', 'foobar');
   }

   public function testEvaluate18() {
      $this->assertFalse(TemplateCondition::applies('greaterThan(5)', '4'));
      $this->assertTrue(TemplateCondition::applies('greaterThan(5)', '6'));
      $this->assertFalse(TemplateCondition::applies('greaterThan(5)', null));
      $this->assertFalse(TemplateCondition::applies('greaterThan(5)', '-1'));
      $this->assertFalse(TemplateCondition::applies('greaterThan(5)', '4.5'));
      $this->assertFalse(TemplateCondition::applies('greaterThan(5)', '5.0'));
      $this->assertTrue(TemplateCondition::applies('greaterThan(5)', '5.5'));
   }

   public function testEvaluate19() {
      $this->assertTrue(TemplateCondition::applies('lowerThan(5)', '4'));
      $this->assertFalse(TemplateCondition::applies('lowerThan(5)', '6'));
      $this->assertTrue(TemplateCondition::applies('lowerThan(5)', null));
      $this->assertTrue(TemplateCondition::applies('lowerThan(5)', '-1'));
      $this->assertTrue(TemplateCondition::applies('lowerThan(5)', '4.5'));
      $this->assertFalse(TemplateCondition::applies('lowerThan(5)', '5.0'));
      $this->assertFalse(TemplateCondition::applies('lowerThan(5)', '5.5'));
   }

   public function testEvaluate20() {
      $this->assertFalse(TemplateCondition::applies('equalTo(5)', '4'));
      $this->assertFalse(TemplateCondition::applies('equalTo(5)', null));
      $this->assertFalse(TemplateCondition::applies('equalTo(5)', '-1'));
      $this->assertFalse(TemplateCondition::applies('equalTo(5)', '4.5'));
      $this->assertTrue(TemplateCondition::applies('equalTo(5)', '5.0'));
   }

   public function testUnknownCondition() {
      $this->assertFalse(TemplateCondition::applies('foo()', null));
   }

}
