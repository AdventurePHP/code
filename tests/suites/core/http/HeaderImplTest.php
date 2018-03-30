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
namespace APF\tests\suites\core\http;

use APF\core\http\HeaderImpl;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HeaderImplTest extends TestCase {

   const HOST = 'localhost';
   const HEADER_NAME = 'Host';

   public function testFromString1() {
      $header = HeaderImpl::fromString(self::HEADER_NAME . ': ' . self::HOST);
      $this->assertEquals(self::HEADER_NAME, $header->getName());
      $this->assertEquals(self::HOST, $header->getValue());
   }

   public function testFromString2() {
      $this->expectException(InvalidArgumentException::class);
      HeaderImpl::fromString('foo');
   }

   public function testConstruct() {
      $header = new HeaderImpl(self::HEADER_NAME, self::HOST);
      $this->assertEquals(self::HEADER_NAME, $header->getName());
      $this->assertEquals(self::HOST, $header->getValue());
   }

   public function testToString() {
      $header = new HeaderImpl(self::HEADER_NAME, self::HOST);
      $this->assertEquals(self::HEADER_NAME . ': ' . self::HOST, $header->__toString());
   }


}
