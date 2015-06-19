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

use APF\tools\validation\NumberScopeValidator;

class NumberScopeValidatorTest extends \PHPUnit_Framework_TestCase {

   public function testHappyCase() {
      $validator = new NumberScopeValidator();
      $this->assertTrue($validator->isValid(5));
      $this->assertTrue($validator->isValid('5'));
      $this->assertFalse($validator->isValid('ABC'));
   }

   public function testMinLength() {
      $validator = new NumberScopeValidator(5);

      $this->assertTrue($validator->isValid(6));
      $this->assertTrue($validator->isValid('6'));
      $this->assertTrue($validator->isValid(5.5));
      $this->assertTrue($validator->isValid(99999999999999));

      $this->assertFalse($validator->isValid(5));
      $this->assertFalse($validator->isValid(5.0));
      $this->assertFalse($validator->isValid('ABC'));
   }

   public function testMaxLength() {
      $validator = new NumberScopeValidator(null, 5);

      $this->assertTrue($validator->isValid(4));
      $this->assertTrue($validator->isValid(4.5));
      $this->assertTrue($validator->isValid(0));
      $this->assertTrue($validator->isValid('0'));

      $this->assertFalse($validator->isValid(5));
      $this->assertFalse($validator->isValid(5.0));
      $this->assertFalse($validator->isValid(6));
      $this->assertFalse($validator->isValid('ABC'));
   }

   public function testRange() {
      $validator = new NumberScopeValidator(3, 5);

      $this->assertTrue($validator->isValid(4));
      $this->assertTrue($validator->isValid('4'));
      $this->assertTrue($validator->isValid(4.5));

      $this->assertFalse($validator->isValid(0));
      $this->assertFalse($validator->isValid(3));
      $this->assertFalse($validator->isValid('3'));
      $this->assertFalse($validator->isValid(3.0));
      $this->assertFalse($validator->isValid(5));
      $this->assertFalse($validator->isValid(5.0));
      $this->assertFalse($validator->isValid(6));
      $this->assertFalse($validator->isValid('ABC'));
   }

   public function testRangeWithLowerIncluded() {
      $validator = new NumberScopeValidator(3, 5, false, true);

      $this->assertTrue($validator->isValid(4));
      $this->assertTrue($validator->isValid(4.5));
      $this->assertTrue($validator->isValid(3));
      $this->assertTrue($validator->isValid('3'));
      $this->assertTrue($validator->isValid(3.0));

      $this->assertFalse($validator->isValid(0));
      $this->assertFalse($validator->isValid(5));
      $this->assertFalse($validator->isValid(5.0));
      $this->assertFalse($validator->isValid(6));
      $this->assertFalse($validator->isValid('ABC'));
   }

   public function testRangeWithUpperIncluded() {
      $validator = new NumberScopeValidator(3, 5, false, false, true);

      $this->assertTrue($validator->isValid(4));
      $this->assertTrue($validator->isValid(4.5));
      $this->assertTrue($validator->isValid(5));
      $this->assertTrue($validator->isValid('5'));
      $this->assertTrue($validator->isValid(5.0));

      $this->assertFalse($validator->isValid(0));
      $this->assertFalse($validator->isValid(3));
      $this->assertFalse($validator->isValid(3.0));
      $this->assertFalse($validator->isValid(6));
      $this->assertFalse($validator->isValid('ABC'));
   }

   public function testRangeWithIncludedLimits() {
      $validator = new NumberScopeValidator(3, 5, false, true, true);

      $this->assertTrue($validator->isValid(3));
      $this->assertTrue($validator->isValid('3'));
      $this->assertTrue($validator->isValid(3.0));
      $this->assertTrue($validator->isValid(4));
      $this->assertTrue($validator->isValid(5));
      $this->assertTrue($validator->isValid(5.0));
      $this->assertTrue($validator->isValid('5.0'));

      $this->assertFalse($validator->isValid(0));
      $this->assertFalse($validator->isValid(6));
      $this->assertFalse($validator->isValid(6.1));
      $this->assertFalse($validator->isValid('ABC'));
   }

   public function testAcceptIntegersOnly() {
      $validator = new NumberScopeValidator(3, 5, true, true, true);

      $this->assertTrue($validator->isValid(3));
      $this->assertTrue($validator->isValid(4));
      $this->assertTrue($validator->isValid(5));

      $this->assertFalse($validator->isValid('4'));

      $this->assertFalse($validator->isValid(3.0));
      $this->assertFalse($validator->isValid(5.0));
      $this->assertFalse($validator->isValid(0));
      $this->assertFalse($validator->isValid(6));
      $this->assertFalse($validator->isValid('6'));
      $this->assertFalse($validator->isValid(6.1));
      $this->assertFalse($validator->isValid('ABC'));
   }

}
