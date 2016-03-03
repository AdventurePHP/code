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
namespace APF\tests\suites\core\singleton;

use APF\core\http\RequestImpl;
use APF\core\singleton\ApplicationSingleton;
use APF\core\singleton\SessionSingleton;
use APF\core\singleton\Singleton;
use ReflectionProperty;

class SessionSingletonTest extends \PHPUnit_Framework_TestCase {

   /**
    * Test whether singleton, session singleton, and application singleton caches
    * are not interfering. See issue ID#286 for details.
    */
   public function testCacheBoundaries() {

      $singletonCache = new ReflectionProperty(Singleton::class, 'CACHE');
      $singletonCache->setAccessible(true);
      $singletonCache->setValue(null, []);

      $sessionSingletonCache = new ReflectionProperty(SessionSingleton::class, 'CACHE');
      $sessionSingletonCache->setAccessible(true);
      $sessionSingletonCache->setValue(null, []);

      $applicationSingletonCache = new ReflectionProperty(ApplicationSingleton::class, 'CACHE');
      $applicationSingletonCache->setAccessible(true);
      $applicationSingletonCache->setValue(null, []);

      // test singleton instance is created in singleton cache only
      Singleton::getInstance(TagModel::class);

      $this->assertCount(1, $singletonCache->getValue(null));
      $this->assertCount(0, $sessionSingletonCache->getValue(null), 'Session singleton cache should be empty!');
      $this->assertCount(0, $applicationSingletonCache->getValue(null), 'Application singleton cache should be empty!');

      // test session singleton instance is created in singleton cache only
      SessionSingleton::getInstance(TagModel::class);

      $value = $singletonCache->getValue(null);
      $this->assertCount(2, $value); // 1=TagModel, 2=HttpRequestImpl (session singleton uses request -> session!)
      $this->assertEquals(TagModel::class, get_class($value[array_keys($value)[0]]));
      $this->assertEquals(RequestImpl::class, get_class($value[array_keys($value)[1]]));

      $this->assertCount(1, $sessionSingletonCache->getValue(null));
      $this->assertCount(0, $applicationSingletonCache->getValue(null), 'Application singleton cache should be empty!');

      // test application instance is created in singleton cache only
      ApplicationSingleton::getInstance(TagModel::class);

      $this->assertCount(2, $singletonCache->getValue(null));
      $this->assertCount(1, $sessionSingletonCache->getValue(null));
      $this->assertCount(1, $applicationSingletonCache->getValue(null));

   }

   protected function setUp() {
      // suppress session_start()
      $_SESSION = [];
   }

}
