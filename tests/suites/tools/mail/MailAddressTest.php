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
namespace APF\tests\suites\tools\mail;

use APF\tools\mail\MailAddress;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Tests the capabilities of an e-mail recipient (sender, recipient, CC recipient, or BCC recipient).
 */
class MailAddressTest extends TestCase {

   public function testConstructor1() {
      $this->expectException(InvalidArgumentException::class);
      new MailAddress(null, null);
   }

   public function testConstructor2() {
      $this->expectException(InvalidArgumentException::class);
      new MailAddress(null, 'foo');
   }

   public function testConstructor3() {
      $recipient = new MailAddress('', 'foo@bar.com');
      $this->assertNull($recipient->getName());
   }

   public function testStringRepresentation1() {
      $recipient = new MailAddress(null, 'foo@bar.com');
      $this->assertEquals('foo@bar.com', $recipient->__toString());

      $recipient = new MailAddress('', 'foo@bar.com');
      $this->assertEquals('foo@bar.com', $recipient->__toString());
   }

   public function testStringRepresentation2() {
      $recipient = new MailAddress('Test name', 'foo@bar.com');
      $this->assertEquals('"Test name" <foo@bar.com>', $recipient->__toString());
   }

   public function testGetter() {
      $name = 'Test name';
      $email = 'foo@bar.com';
      $recipient = new MailAddress($name, $email);
      $this->assertEquals($name, $recipient->getName());
      $this->assertEquals($email, $recipient->getEmail());
   }

}
