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
namespace APF\tests\suites\tools\validation;

use APF\tools\validation\TextLengthValidator;
use PHPUnit\Framework\TestCase;

class TextLengthValidatorTest extends TestCase {

   public function testMinLength() {
      $validator = new TextLengthValidator(3, 0);
      $this->assertTrue($validator->isValid('Lorem ipsum'));
      $this->assertTrue($validator->isValid('DJ '));

      $this->assertFalse($validator->isValid('DJ'));
      $this->assertFalse($validator->isValid(''));
      $this->assertFalse($validator->isValid(null));

      $validator = new TextLengthValidator(1, 0);
      $this->assertTrue($validator->isValid('D'));
      $this->assertTrue($validator->isValid('0'));

      $this->assertFalse($validator->isValid(''));
      $this->assertFalse($validator->isValid(NULL));
   }

   public function testTestMaxLength() {
      $validator = new TextLengthValidator(0, 3);
      $this->assertFalse($validator->isValid('Lorem ipsum'));
      $this->assertFalse($validator->isValid('DJ  '));
      $this->assertFalse($validator->isValid(''));
      $this->assertFalse($validator->isValid(null));

      $this->assertTrue($validator->isValid('DJ'));
   }

   public function testRange() {
      $validator = new TextLengthValidator(3, 11);
      $this->assertTrue($validator->isValid('Lorem ipsum'));
      $this->assertTrue($validator->isValid('DJ  '));

      $this->assertFalse($validator->isValid(''));
      $this->assertFalse($validator->isValid(null));

      $this->assertFalse($validator->isValid('DJ'));
      $this->assertFalse($validator->isValid('Lorem ipsum dolor'));
   }

   public function testRangeStrict() {
      $validator = new TextLengthValidator(3, 11, TextLengthValidator::MODE_STRICT);
      $this->assertTrue($validator->isValid('Lorem ipsum'));
      $this->assertTrue($validator->isValid('Lorem ipsum  '));
      $this->assertTrue($validator->isValid('  Lorem ipsum'));

      $this->assertFalse($validator->isValid('    '));
      $this->assertFalse($validator->isValid(null));

      $this->assertFalse($validator->isValid('DJ  '));

      $this->assertFalse($validator->isValid('  DJ'));
      $this->assertFalse($validator->isValid('Lorem ipsum dolor'));
   }

}
