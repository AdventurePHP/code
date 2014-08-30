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
namespace APF\tests\suites\tools\validation;

use APF\tools\validation\TextLengthValidator;

class TextLengthValidatorTest extends \PHPUnit_Framework_TestCase {

   public function testMinLength() {
      $validator = new TextLengthValidator(3, 0);
      $this->assertTrue($validator->validate('Lorem ipsum'));
      $this->assertTrue($validator->validate('DJ '));

      $this->assertFalse($validator->validate('DJ'));
      $this->assertFalse($validator->validate(''));
      $this->assertFalse($validator->validate(null));
   }

   public function testTestMaxLength() {
      $validator = new TextLengthValidator(0, 3);
      $this->assertFalse($validator->validate('Lorem ipsum'));
      $this->assertFalse($validator->validate('DJ  '));
      $this->assertFalse($validator->validate(''));
      $this->assertFalse($validator->validate(null));

      $this->assertTrue($validator->validate('DJ'));
   }

   public function testRange() {
      $validator = new TextLengthValidator(3, 11);
      $this->assertTrue($validator->validate('Lorem ipsum'));
      $this->assertTrue($validator->validate('DJ  '));

      $this->assertFalse($validator->validate(''));
      $this->assertFalse($validator->validate(null));

      $this->assertFalse($validator->validate('DJ'));
      $this->assertFalse($validator->validate('Lorem ipsum dolor'));
   }

   public function testRangeStrict() {
      $validator = new TextLengthValidator(3, 11, TextLengthValidator::MODE_STRICT);
      $this->assertTrue($validator->validate('Lorem ipsum'));
      $this->assertTrue($validator->validate('Lorem ipsum  '));
      $this->assertTrue($validator->validate('  Lorem ipsum'));

      $this->assertFalse($validator->validate('    '));
      $this->assertFalse($validator->validate(null));

      $this->assertFalse($validator->validate('DJ  '));

      $this->assertFalse($validator->validate('  DJ'));
      $this->assertFalse($validator->validate('Lorem ipsum dolor'));
   }

}
 