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
namespace APF\tests\suites\core\configuration;

use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\provider\ini\IniConfiguration;
use APF\core\configuration\provider\ini\IniConfigurationProvider;
use APF\core\loader\RootClassLoader;
use APF\core\loader\StandardClassLoader;

/**
 * Tests the INI file based configuration provider.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 11.03.2015 (see ID#243 for requirement details)<br />
 */
class IniConfigurationProviderTest extends \PHPUnit_Framework_TestCase {

   const TEST_VENDOR = 'TEST';
   const TEST_NAMESPACE = '';
   const TEST_ENVIRONMENT = 'DEFAULT';
   const TEST_CONFIG_NAME = 'test-config.ini';
   const TEST_NEW_CONFIG_NAME = 'test-config-new.ini';

   const CONFIG_ROOT_FOLDER = 'test-config';

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

   }

   public function testSubSectionResolution() {

      $config = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_CONFIG_NAME
      );

      $this->assertEquals('foo', $config->getSection('Section')->getValue('SubSection.ValueOne')); // should not fail with removing direct access support
      $this->assertEquals('foo', $config->getValue('Section.SubSection.ValueOne'));
      $this->assertEquals('foo', $config->getSection('Section')->getSection('SubSection')->getValue('ValueOne'));

      $this->assertEquals('bar', $config->getSection('Section')->getValue('SubSection.ValueTwo')); // should not fail with removing direct access support
      $this->assertEquals('bar', $config->getValue('Section.SubSection.ValueTwo'));
      $this->assertEquals('bar', $config->getSection('Section')->getSection('SubSection')->getValue('ValueTwo'));

   }

   public function testSavingComplexStructure() {

      $config = new IniConfiguration();

      $values = new IniConfiguration();
      $values->setValue('ValueOne', 'foo');
      $values->setValue('ValueTwo', 'bar');

      $subSection = new IniConfiguration();
      $subSection->setSection('SubSection', $values);

      $config->setSection('Section', $subSection);

      ConfigurationManager::saveConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_NEW_CONFIG_NAME,
            $config
      );

      $storedConfig = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_NEW_CONFIG_NAME
      );

      $this->assertEquals(
            $config->getSection('Section')->getSection('SubSection')->getValue('ValueOne'),
            $storedConfig->getSection('Section')->getSection('SubSection')->getValue('ValueOne')
      );
      $this->assertEquals(
            $config->getSection('Section')->getSection('SubSection')->getValue('ValueTwo'),
            $storedConfig->getSection('Section')->getSection('SubSection')->getValue('ValueTwo')
      );

      ConfigurationManager::deleteConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_NEW_CONFIG_NAME
      );
   }

   public function testSectionAccessWithPath() {
      $config = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_CONFIG_NAME
      );

      $this->assertEquals('foo', $config->getSection('Section.SubSection')->getValue('ValueOne'));
      $this->assertEquals('foo', $config->getSection('Section')->getSection('SubSection')->getValue('ValueOne'));
   }

   public function testValueAccessWithPath() {
      $config = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_CONFIG_NAME
      );

      $this->assertEquals('foo', $config->getValue('Section.SubSection.ValueOne'));
   }

   public static function tearDownAfterClass() {

      // remove configuration provider to not disturb other tests
      ConfigurationManager::registerProvider('ini', self::$originalIniProvider);

      // remove static configuration class loader used for test purposes only
      RootClassLoader::removeLoader(RootClassLoader::getLoaderByVendor(self::TEST_VENDOR));

   }

}
