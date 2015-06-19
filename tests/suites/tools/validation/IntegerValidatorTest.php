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

use APF\tools\validation\IntegerValidator;

class IntegerValidatorTest extends \PHPUnit_Framework_TestCase {

   /**
    * @var IntegerValidator
    */
   private $validator;

   protected function setUp() {
      $this->validator = new IntegerValidator();
   }

   public function testValidInteger() {
      $this->assertTrue($this->validator->isValid(123));
   }

   public function testString() {
      $this->assertTrue($this->validator->isValid('123'));
      $this->assertTrue($this->validator->isValid(' 123'));
      $this->assertTrue($this->validator->isValid('123 '));
      $this->assertTrue($this->validator->isValid(' 123 '));

      $this->assertFalse($this->validator->isValid('ABC'));
      $this->assertFalse($this->validator->isValid('1%'));
   }

   public function testFloat() {
      $this->assertTrue($this->validator->isValid(123.0));
      $this->assertFalse($this->validator->isValid(123.5));
   }

}
