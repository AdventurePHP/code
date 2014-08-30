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

use APF\tools\validation\UriValidator;

class UriValidatorTest extends \PHPUnit_Framework_TestCase {

   public function testValidUrl() {
      $validator = new UriValidator();
      $this->assertTrue($validator->isValid('ftp://example.com/foo/bar'));
      $this->assertTrue($validator->isValid('HTTps://example.com/foo?bar=123'));
      $this->assertTrue($validator->isValid('HtTp://images.example.com/foo/bar.png'));
      $this->assertTrue($validator->isValid('://www.example.com/?page=test'));
      $this->assertTrue($validator->isValid('www.example.com'));
      $this->assertTrue($validator->isValid('http://ex.ample.com&foo=bar'));
   }

   public function testInValidUrl() {
      $validator = new UriValidator();
      $this->assertFalse($validator->isValid('ftp:example.com'));
      $this->assertFalse($validator->isValid('ex-ample'));
      $this->assertFalse($validator->isValid('XyZ://ex.ample.com'));
      $this->assertFalse($validator->isValid('ht-tp://ex.ample.com'));

   }

}
 