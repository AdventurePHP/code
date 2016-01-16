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
namespace APF\tests\suites\core\service;

use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\provider\ini\IniConfigurationProvider;
use APF\core\configuration\provider\php\PhpConfigurationProvider;
use APF\core\loader\RootClassLoader;
use APF\core\loader\StandardClassLoader;
use APF\core\service\APFService;
use APF\core\service\DIServiceManager;
use InvalidArgumentException;

/**
 * Tests service object creation capabilities via DI container.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 10.04.2015<br />
 */
class DIServiceManagerTest extends \PHPUnit_Framework_TestCase {

   const TEST_VENDOR = 'TEST';
   const CONFIG_ROOT_FOLDER = 'test-config';
   const CONTEXT = 'dummy';
   const LANGUAGE = 'dummy';

   private static $originalIniProvider;

   public static function setUpBeforeClass() {

      // setup static configuration resource path for test purposes
      RootClassLoader::addLoader(new StandardClassLoader(self::TEST_VENDOR, __DIR__ . '/' . self::CONFIG_ROOT_FOLDER));

      // setup configuration provider for this test
      /* @var $provider IniConfigurationProvider */
      $provider = ConfigurationManager::retrieveProvider('ini');

      // store for further re-store
      self::$originalIniProvider = $provider;

      $provider->setOmitContext(true);
      $provider->setOmitConfigSubFolder(true);
      ConfigurationManager::registerProvider('ini', $provider);

      // setup configuration provider for this test
      $provider = new PhpConfigurationProvider();
      $provider->setOmitContext(true);
      $provider->setOmitConfigSubFolder(true);
      ConfigurationManager::registerProvider('php', $provider);

   }

   public static function tearDownAfterClass() {

      // remove configuration provider to not disturb other tests
      ConfigurationManager::registerProvider('ini', self::$originalIniProvider);

      // remove configuration provider to not disturb other tests
      ConfigurationManager::removeProvider('php');

      // remove static configuration class loader used for test purposes only
      RootClassLoader::removeLoader(RootClassLoader::getLoaderByVendor(self::TEST_VENDOR));

   }

   public function testSetterInjection() {

      /* @var $service DummyService */
      $service = DIServiceManager::getServiceObject(self::TEST_VENDOR, 'DummyService-setter', self::CONTEXT,
            self::LANGUAGE);

      $this->assertEquals(self::CONTEXT, $service->getContext());
      $this->assertEquals(self::LANGUAGE, $service->getLanguage());
      $this->assertEquals(APFService::SERVICE_TYPE_SINGLETON, $service->getServiceType());

      $this->assertEquals('foo', $service->getParamOne());
      $this->assertInstanceOf(DummyServiceTwo::class, $service->getParamTwo());

   }

   public function testConstructorInjection() {

      /* @var $service DummyService */
      $service = DIServiceManager::getServiceObject(self::TEST_VENDOR, 'DummyService-constructor', self::CONTEXT,
            self::LANGUAGE);

      $this->assertEquals(self::CONTEXT, $service->getContext());
      $this->assertEquals(self::LANGUAGE, $service->getLanguage());
      $this->assertEquals(APFService::SERVICE_TYPE_SINGLETON, $service->getServiceType());

      $this->assertEquals('foo', $service->getParamOne());
      $this->assertInstanceOf(DummyServiceTwo::class, $service->getParamTwo());

   }

   public function testConstructorInjectionFails() {
      $serviceName = 'DummyService-constructor-fail';
      try {
         DIServiceManager::getServiceObject(self::TEST_VENDOR, $serviceName, self::CONTEXT,
               self::LANGUAGE);
         $this->fail('service creation should not be successful!');
      } catch (InvalidArgumentException $e) {
         $this->assertContains('"' . $serviceName . '"', $e->getMessage());
         $this->assertContains('"service"', $e->getMessage());
      }
   }

   public function testConstructorInjectionWithPHPFormat() {

      DIServiceManager::$configurationExtension = 'php';

      /* @var $service DummyService */
      $service = DIServiceManager::getServiceObject(self::TEST_VENDOR, 'DummyService-constructor', self::CONTEXT,
            self::LANGUAGE);

      $this->assertEquals(self::CONTEXT, $service->getContext());
      $this->assertEquals(self::LANGUAGE, $service->getLanguage());
      $this->assertEquals(APFService::SERVICE_TYPE_SINGLETON, $service->getServiceType());

      $this->assertEquals('foo', $service->getParamOne());
      $this->assertInstanceOf(DummyServiceTwo::class, $service->getParamTwo());

      DIServiceManager::$configurationExtension = 'ini';

   }

   public function testSetterInjectionFailsWithMissingMethod() {
      $this->setExpectedException(InvalidArgumentException::class);
      DIServiceManager::getServiceObject(self::TEST_VENDOR, 'DummyService-setter-method-fail-1', self::CONTEXT,
            self::LANGUAGE);
   }

   public function testSetterInjectionFailsWithUnknownMethod() {
      $this->setExpectedException(InvalidArgumentException::class);
      DIServiceManager::getServiceObject(self::TEST_VENDOR, 'DummyService-setter-method-fail-2', self::CONTEXT,
            self::LANGUAGE);
   }

   public function testSetterInjectionFailsWithMissingValueSubSection() {
      $this->setExpectedException(InvalidArgumentException::class);
      DIServiceManager::getServiceObject(self::TEST_VENDOR, 'DummyServiceThree-missing-value-section', self::CONTEXT,
            self::LANGUAGE);
   }

   public function testSetterInjectionMultipleParameters() {
      /* @var $service DummyServiceThree */
      $service = DIServiceManager::getServiceObject(self::TEST_VENDOR, 'DummyServiceThree', self::CONTEXT,
            self::LANGUAGE);
      $this->assertEquals('foo', $service->getParamOne());
      $this->assertEquals('bar', $service->getParamTwo());
      $this->assertEquals('baz', $service->getParamThree());
   }

}
