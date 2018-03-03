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
namespace APF\tests\suites\core\configuration;

use APF\core\configuration\Configuration;
use APF\core\configuration\ConfigurationException;
use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\provider\apc\ApcConfigurationProvider;
use APF\core\configuration\provider\ini\IniConfigurationProvider;
use APF\core\loader\RootClassLoader;
use APF\core\loader\StandardClassLoader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the APC configuration provider.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 31.12.2016 (ID#313 added tests while switching to apcu_*() methods)<br />
 */
class ApcConfigurationProviderTest extends TestCase {

   const TEST_VENDOR = 'TEST';
   const TEST_ENVIRONMENT = 'DEFAULT';
   const TEST_CONFIG_NAME = 'test-config.apc';
   const TEST_NEW_CONFIG_NAME = 'test-config-new.apc';

   const CONFIG_ROOT_FOLDER = 'test-config';

   const INTERNAL_CACHE_KEY = 'cache-key';

   private static $originalIniProvider;

   /**
    * @throws ConfigurationException
    */
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

   }

   public static function tearDownAfterClass() {

      // remove configuration provider to not disturb other tests
      ConfigurationManager::registerProvider('ini', self::$originalIniProvider);

      // remove apc configuration provider to not disturb other tests
      ConfigurationManager::removeProvider('apc');

      // remove static configuration class loader used for test purposes only
      RootClassLoader::removeLoader(RootClassLoader::getLoaderByVendor(self::TEST_VENDOR));

   }

   /**
    * Tests loading a configuration file. On first attempt, this should be a physical load,
    * with "warmed cache" this should be an APCu fetch only.
    */
   public function testLoadConfigurationFile() {

      /* @var $provider ApcConfigurationProvider|MockObject */
      $provider = $this->getMockBuilder(ApcConfigurationProvider::class)
            ->setConstructorArgs(['ini'])
            ->setMethods(['getStoreIdentifier'])
            ->getMock();
      $provider->method('getStoreIdentifier')
            ->willReturn(self::INTERNAL_CACHE_KEY);

      ConfigurationManager::registerProvider('apc', $provider);

      $config = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_CONFIG_NAME
      );

      $this->assertEquals($config->getValue('Section.SubSection.ValueOne'), 'foo');
      $this->assertEquals($config->getValue('Section.SubSection.ValueTwo'), 'bar');

      $this->assertInstanceOf(Configuration::class, $config->getSection('Section'));
      $this->assertInstanceOf(Configuration::class, $config->getSection('Section')->getSection('SubSection'));

      // check whether APCu store includes cache entry:
      $this->assertTrue(apcu_exists(self::INTERNAL_CACHE_KEY), 'APCu entry dues not exist!');

   }

   public function testFailToLoadConfigurationFile() {

      $provider = new ApcConfigurationProvider('ini');
      ConfigurationManager::registerProvider('apc', $provider);

      $this->expectException(ConfigurationException::class);
      ConfigurationManager::loadConfiguration(self::TEST_VENDOR, null, null, self::TEST_ENVIRONMENT, 'non-existing.apc');
   }

   public function testSaveConfiguration() {

      // ensure that APCu cache is empty
      apcu_delete(self::INTERNAL_CACHE_KEY);
      $this->assertFalse(apcu_exists(self::INTERNAL_CACHE_KEY), 'APCu cache entry not empty. Thus test pre-condition not given.');

      /* @var $provider ApcConfigurationProvider|MockObject */
      $provider = $this->getMockBuilder(ApcConfigurationProvider::class)
            ->setConstructorArgs(['ini'])
            ->setMethods(['getStoreIdentifier'])
            ->getMock();
      $provider->method('getStoreIdentifier')
            ->willReturn(self::INTERNAL_CACHE_KEY);

      ConfigurationManager::registerProvider('apc', $provider);

      $config = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_CONFIG_NAME
      );

      // Ensure cache is primed
      $this->assertTrue(apcu_exists(self::INTERNAL_CACHE_KEY), 'APCu cache not filled.');

      // Add third option to check whether transparent through ini provider writing works
      $config->setValue('Section.SubSection.ValueThree', 'baz');

      ConfigurationManager::saveConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_NEW_CONFIG_NAME,
            $config
      );

      // Ensure cache is updated accordingly
      $this->assertEquals($config, apcu_fetch(self::INTERNAL_CACHE_KEY));

      $newConfig = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_NEW_CONFIG_NAME
      );

      $this->assertInstanceOf(Configuration::class, $config->getSection('Section'));
      $this->assertInstanceOf(Configuration::class, $config->getSection('Section')->getSection('SubSection'));

      $this->assertInstanceOf(Configuration::class, $newConfig->getSection('Section'));
      $this->assertInstanceOf(Configuration::class, $newConfig->getSection('Section')->getSection('SubSection'));

      $this->assertEquals(
            $config->getValue('Section.SubSection.ValueOne'),
            $newConfig->getValue('Section.SubSection.ValueOne')
      );
      $this->assertEquals(
            $config->getValue('Section.SubSection.ValueTwo'),
            $newConfig->getValue('Section.SubSection.ValueTwo')
      );
      $this->assertEquals(
            'baz',
            $newConfig->getValue('Section.SubSection.ValueThree')
      );

   }

   public function testDeleteConfigurationFile() {

      /* @var $provider ApcConfigurationProvider|MockObject */
      $provider = $this->getMockBuilder(ApcConfigurationProvider::class)
            ->setConstructorArgs(['ini'])
            ->setMethods(['getStoreIdentifier'])
            ->getMock();
      $provider->method('getStoreIdentifier')
            ->willReturn(self::INTERNAL_CACHE_KEY);

      ConfigurationManager::registerProvider('apc', $provider);

      $fileName = __DIR__ . '/' . self::CONFIG_ROOT_FOLDER . '/'
            . self::TEST_ENVIRONMENT . '_' . self::TEST_NEW_CONFIG_NAME;

      ConfigurationManager::deleteConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_NEW_CONFIG_NAME
      );

      if (file_exists($fileName)) {
         $this->fail('File "' . $fileName . '" has not been deleted by configuration manager!');
      } else {
         $this->assertTrue(true);
      }

      // Ensure cache is cleared
      $this->assertFalse(apcu_exists(self::INTERNAL_CACHE_KEY), 'APCu cache not cleared.');

   }

}
