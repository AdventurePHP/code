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
namespace APF\tests\suites\core\service;

use APF\core\service\APFService;
use APF\core\service\ServiceManager;
use APF\core\singleton\Singleton;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Tests service object creation capabilities.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 10.04.2015<br />
 */
class ServiceManagerTest extends TestCase {

   const SERVICE_CLASS = DummyService::class;
   const CONTEXT = 'foo';
   const LANGUAGE = 'de';
   const INSTANCE_ID = 'test-id';

   public function testServiceCreationFail() {
      $this->expectException(InvalidArgumentException::class);
      ServiceManager::getServiceObject('\stdClass', self::CONTEXT, self::LANGUAGE);
   }

   public function testSimpleCreation() {

      Singleton::deleteInstance(self::SERVICE_CLASS);

      /* @var $service APFService */
      $service = ServiceManager::getServiceObject(self::SERVICE_CLASS, self::CONTEXT, self::LANGUAGE);
      $this->assertEquals(self::CONTEXT, $service->getContext());
      $this->assertEquals(self::LANGUAGE, $service->getLanguage());
      $this->assertEquals(APFService::SERVICE_TYPE_SINGLETON, $service->getServiceType());

   }

   public function testConstructorCreation() {

      Singleton::deleteInstance(self::SERVICE_CLASS);

      $paramOne = 'one';
      $paramTwo = 'two';

      /* @var $service DummyService */
      $service = ServiceManager::getServiceObject(self::SERVICE_CLASS, self::CONTEXT, self::LANGUAGE,
            [$paramOne, $paramTwo]);
      $this->assertEquals(self::CONTEXT, $service->getContext());
      $this->assertEquals(self::LANGUAGE, $service->getLanguage());
      $this->assertEquals(APFService::SERVICE_TYPE_SINGLETON, $service->getServiceType());

      $this->assertEquals($paramOne, $service->getParamOne());
      $this->assertEquals($paramTwo, $service->getParamTwo());

   }

   public function testMixedInstanceIdCreation() {

      Singleton::deleteInstance(self::SERVICE_CLASS, self::INSTANCE_ID);

      $paramOne = 'one';
      $paramTwo = 'two';

      /* @var $service DummyService */
      $service = ServiceManager::getServiceObject(self::SERVICE_CLASS, self::CONTEXT, self::LANGUAGE,
            [$paramOne, $paramTwo], APFService::SERVICE_TYPE_SINGLETON, self::INSTANCE_ID);
      $this->assertEquals(self::CONTEXT, $service->getContext());
      $this->assertEquals(self::LANGUAGE, $service->getLanguage());
      $this->assertEquals(APFService::SERVICE_TYPE_SINGLETON, $service->getServiceType());

      $this->assertEquals($paramOne, $service->getParamOne());
      $this->assertEquals($paramTwo, $service->getParamTwo());

      $service = ServiceManager::getServiceObject(self::SERVICE_CLASS, self::CONTEXT, self::LANGUAGE);
      $this->assertNotEquals($paramOne, $service->getParamOne());
      $this->assertNotEquals($paramTwo, $service->getParamTwo());

      /* @var $service DummyService */
      $service = ServiceManager::getServiceObject(self::SERVICE_CLASS, self::CONTEXT, self::LANGUAGE,
            [], APFService::SERVICE_TYPE_SINGLETON, self::INSTANCE_ID);
      $this->assertEquals($paramOne, $service->getParamOne());
      $this->assertEquals($paramTwo, $service->getParamTwo());

   }

   public function testConstructorNormalInstanceCreation() {

      $paramOne = 'one';
      $paramTwo = 'two';

      /* @var $service DummyService */
      $service = ServiceManager::getServiceObject(self::SERVICE_CLASS, self::CONTEXT, self::LANGUAGE,
            [$paramOne, $paramTwo], APFService::SERVICE_TYPE_NORMAL);

      $this->assertEquals($paramOne, $service->getParamOne());
      $this->assertEquals($paramTwo, $service->getParamTwo());
   }

   /**
    * ID#317: avoid context clashes by not permitting empty context information (null values).
    */
   public function testEmptyContextCausesException1() {
      $this->expectException(InvalidArgumentException::class);
      ServiceManager::getServiceObject(DummyService::class, '', null);
   }

   /**
    * ID#317: avoid context clashes by not permitting empty context information (empty strings).
    */
   public function testEmptyContextCausesException2() {
      $this->expectException(InvalidArgumentException::class);
      ServiceManager::getServiceObject(DummyService::class, '', '');
   }

}
