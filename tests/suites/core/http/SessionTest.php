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

use APF\core\http\Session;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase {

   public function testConstruction1() {
      $_SESSION = [];
      $this->getMockBuilder(Session::class)
            ->setConstructorArgs([__NAMESPACE__])
            ->setMethods(['startSession'])
            ->getMock();
      $this->assertEquals([], $_SESSION[__NAMESPACE__]);
   }

   public function testConstruction2() {
      $_SESSION = [];
      $this->expectException(InvalidArgumentException::class);
      new Session('');
   }

   public function testInterface() {
      $_SESSION = [];

      $session = new Session(__NAMESPACE__);
      $this->assertEquals([], $_SESSION[__NAMESPACE__]);

      $key = 'foo';
      $value = 'bar';
      $notExistingKey = 'not existing key';
      $default = 'special default';

      // test load
      $this->assertNull($session->load($key));
      $this->assertEquals($default, $session->load($key, $default));

      $this->assertNull($session->load($notExistingKey));
      $this->assertEquals($default, $session->load($notExistingKey, $default));

      $session->save($key, $value);
      $this->assertEquals($value, $session->load($key));

      // test save
      $session->destroy();
      $session->save($key, $value);

      $this->assertEquals($value, $_SESSION[__NAMESPACE__][$key]);
      $this->assertEquals([$key => $value], $session->loadAll());
      $this->assertEquals([$key], $session->getEntryKeys());

      // test delete
      $session->delete($key);
      $this->assertEquals([], $session->loadAll());
      $this->assertEquals([], $session->getEntryKeys());

      // test session destroy
      $session->save($key, $value);
      $session->destroy();
      $this->assertEquals([], $_SESSION[__NAMESPACE__]);
   }

   public function setUp() {
      $_SESSION = [];
   }

   public function tearDown() {
      $_SESSION = [];
   }

}
