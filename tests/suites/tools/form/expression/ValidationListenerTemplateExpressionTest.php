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
namespace APF\tests\suites\tools\form\expression;

use APF\tools\form\expression\ValidationMarkerTemplateExpression;
use APF\tools\form\taglib\ValidationListenerTag;
use APF\tools\form\validator\AbstractFormValidator;
use InvalidArgumentException;

/**
 * Tests the capabilities of the validation listener expression.
 */
class ValidationListenerTemplateExpressionTest extends \PHPUnit_Framework_TestCase {

   /**
    * Tests whether the validation expression correctly detects responsibility.
    */
   public function testApplies() {
      $this->assertFalse(ValidationMarkerTemplateExpression::applies('foo'));
      $this->assertFalse(ValidationMarkerTemplateExpression::applies('foo(control)'));
      $this->assertTrue(ValidationMarkerTemplateExpression::applies('validationMarker()'));
      $this->assertTrue(ValidationMarkerTemplateExpression::applies('validationMarker(control)'));
      $this->assertTrue(ValidationMarkerTemplateExpression::applies('validationMarker(control, my-css-class)'));
      $this->assertTrue(ValidationMarkerTemplateExpression::applies('validationMarker(control, \'my validation marker content\')'));
   }

   /**
    * Tests whether first argument is mandatory.
    */
   public function testRequiredArgument() {
      $this->expectException(InvalidArgumentException::class);
      $this->expectExceptionMessage(
            'First argument of template expression "validationMarker(control, [\'marker content\'])" '
            . 'is mandatory! Expression given: "validationMarker()".'
      );

      ValidationMarkerTemplateExpression::getDocument('validationMarker()');
   }

   /**
    * Tests parsing of expression arguments.
    */
   public function testArgumentParsing() {
      $doc = ValidationMarkerTemplateExpression::getDocument('validationMarker(\'foo \')');
      $this->assertInstanceOf(ValidationListenerTag::class, $doc);
      $this->assertEquals('foo', $doc->getAttribute('control'));

      $doc = ValidationMarkerTemplateExpression::getDocument('validationMarker(\' foo\')');
      $this->assertInstanceOf(ValidationListenerTag::class, $doc);
      $this->assertEquals('foo', $doc->getAttribute('control'));

      $doc = ValidationMarkerTemplateExpression::getDocument('validationMarker(\' foo \')');
      $this->assertInstanceOf(ValidationListenerTag::class, $doc);
      $this->assertEquals('foo', $doc->getAttribute('control'));
   }

   /**
    * Test assigning validation marker output - arbitrary content or CSS class.
    */
   public function testCssClassDefinition() {
      $doc = ValidationMarkerTemplateExpression::getDocument('validationMarker(foo)');
      $this->assertInstanceOf(ValidationListenerTag::class, $doc);
      $this->assertEquals('foo', $doc->getAttribute('control'));
      $this->assertEquals(AbstractFormValidator::$DEFAULT_MARKER_CLASS, $doc->getContent());

      $doc = ValidationMarkerTemplateExpression::getDocument('validationMarker(foo, \' bar baz \')');
      $this->assertInstanceOf(ValidationListenerTag::class, $doc);
      $this->assertEquals('foo', $doc->getAttribute('control'));
      $this->assertEquals('bar baz', $doc->getContent());
   }

   /**
    * Test whether transformation of validation marker tag only shows content on notification.
    */
   public function testTransformation() {
      $errorCssClass = 'error-class';

      $doc = ValidationMarkerTemplateExpression::getDocument('validationMarker(foo, \' ' . $errorCssClass . ' \')');
      $this->assertInstanceOf(ValidationListenerTag::class, $doc);
      $this->assertEquals('foo', $doc->getAttribute('control'));
      $this->assertEquals($errorCssClass, $doc->getContent());

      $this->assertEquals('', $doc->transform());

      $doc->notify();
      $this->assertEquals($errorCssClass, $doc->transform());

   }

}
