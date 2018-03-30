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

use APF\core\http\Cookie;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CookieTest extends TestCase {

   const NAME = 'foo';
   const DOMAIN = 'localhost';

   const PATH = '/foo';

   public function testConstruct() {

      $time = time() + Cookie::DEFAULT_EXPIRATION_TIME + 1000;

      $cookie = new Cookie(
            self::NAME,
            $time,
            self::DOMAIN,
            self::PATH,
            true,
            true
      );

      $this->assertEquals(self::NAME, $cookie->getName());
      $this->assertEquals($time, $cookie->getExpireTime());
      $this->assertEquals(self::DOMAIN, $cookie->getDomain());
      $this->assertEquals(self::PATH, $cookie->getPath());
      $this->assertTrue($cookie->isSecure());
      $this->assertTrue($cookie->isHttpOnly());

      $this->assertNull($cookie->getValue());
   }

   public function testSetters() {

      // test standard values
      $cookie = new Cookie(self::NAME);

      $this->assertEquals(self::NAME, $cookie->getName());
      $this->assertNotNull($cookie->getExpireTime());
      $this->assertEquals(self::DOMAIN, $cookie->getDomain());
      $this->assertEquals('/', $cookie->getPath());
      $this->assertFalse($cookie->isSecure());
      $this->assertFalse($cookie->isHttpOnly());

      $this->assertNull($cookie->getValue());

      // test setters
      $time = intval(time() * 2);
      $domain = 'example.com';
      $value = 'foo';

      $cookie->setExpireTime($time);
      $cookie->setValue($value);
      $cookie->setDomain($domain);
      $cookie->setPath(self::PATH);
      $cookie->setHttpOnly(true);
      $cookie->setSecure(true);

      $this->assertEquals(self::NAME, $cookie->getName());
      $this->assertEquals($time, $cookie->getExpireTime());
      $this->assertEquals($domain, $cookie->getDomain());
      $this->assertEquals(self::PATH, $cookie->getPath());
      $this->assertTrue($cookie->isSecure());
      $this->assertTrue($cookie->isHttpOnly());

      $this->assertEquals($value, $cookie->getValue());

   }

   public function testWrongConstruction() {
      $this->expectException(InvalidArgumentException::class);
      new Cookie('');
   }

   public function testDelete() {
      $cookie = new Cookie(self::NAME);
      $this->assertFalse($cookie->isDeleted());

      $cookie->delete();

      $this->assertTrue($cookie->isDeleted());
   }

}
