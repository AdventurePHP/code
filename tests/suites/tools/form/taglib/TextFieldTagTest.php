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
namespace APF\tests\suites\tools\form\taglib;

use APF\tools\form\taglib\TextFieldTag;

class TextFieldTagTest extends \PHPUnit_Framework_TestCase {

   const FIELD_NAME = 'foo';
   const FIELD_VALUE = 'bar';

   public function testControllerPresetting() {

      $_REQUEST = [];

      $field = $this->getTextField();

      $field->onParseTime();
      $field->onAfterAppend();

      $field->setValue(self::FIELD_VALUE);

      $this->assertEquals($field->getValue(), self::FIELD_VALUE);
      $this->assertTrue($field->isFilled());

   }

   /**
    * @return TextFieldTag
    */
   protected function getTextField() {
      $field = new TextFieldTag();
      $field->setAttribute('name', self::FIELD_NAME);

      return $field;
   }

   public function testSubmitPresetting() {

      $_REQUEST = [];

      $userInput = 'This is a user input...';
      $_REQUEST[self::FIELD_NAME] = $userInput;

      $field = $this->getTextField();
      $field->setValue(self::FIELD_VALUE);

      $field->onParseTime();
      $field->onAfterAppend();

      $this->assertEquals($field->getValue(), $userInput);
      $this->assertTrue($field->isFilled());

      // re-test with "0" as input in URL
      $userInput = '0';
      $_REQUEST[self::FIELD_NAME] = $userInput;

      $field->onParseTime();
      $field->onAfterAppend();

      $this->assertEquals($field->getValue(), $userInput);
      $this->assertTrue($field->isFilled());

   }

   /**
    * Test whether field can be hidden by template definition and via controller
    */
   public function testHiding() {

      // hide via template definition
      $field = new TextFieldTag();
      $field->setAttributes([
            'name'   => self::FIELD_NAME,
            'value'  => self::FIELD_VALUE,
            'hidden' => 'true'
      ]);
      $field->onParseTime();
      $field->onAfterAppend();

      $this->assertEmpty($field->transform());

      // hide via controller
      $field = new TextFieldTag();
      $field->setAttributes([
            'name'  => self::FIELD_NAME,
            'value' => self::FIELD_VALUE
      ]);
      $field->onParseTime();
      $field->onAfterAppend();

      $field->hide();

      $this->assertEmpty($field->transform());
   }

}
