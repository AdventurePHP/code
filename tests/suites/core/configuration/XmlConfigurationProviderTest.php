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

use APF\core\configuration\ConfigurationException;
use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\provider\xml\XmlConfiguration;
use APF\core\configuration\provider\xml\XmlConfigurationProvider;
use APF\core\loader\RootClassLoader;
use APF\core\loader\StandardClassLoader;
use PHPUnit\Framework\TestCase;

/**
 * Tests the XML file based configuration provider.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.03.2018<br />
 */
class XmlConfigurationProviderTest extends TestCase {

   const TEST_VENDOR = 'TEST';
   const TEST_NAMESPACE = '';
   const TEST_ENVIRONMENT = 'DEFAULT';
   const TEST_CONFIG_NAME = 'test-config.xml';
   const TEST_NEW_CONFIG_NAME = 'test-config-new.xml';

   const CONFIG_ROOT_FOLDER = 'test-config';

   /**
    * @throws ConfigurationException
    */
   public static function setUpBeforeClass() {

      // setup static configuration resource path for test purposes
      RootClassLoader::addLoader(new StandardClassLoader(self::TEST_VENDOR, __DIR__ . '/' . self::CONFIG_ROOT_FOLDER));

      // register provider
      $provider = new XmlConfigurationProvider();
      $provider->setOmitContext(true);
      $provider->setOmitConfigSubFolder(true);

      ConfigurationManager::registerProvider('xml', $provider);
   }

   public static function tearDownAfterClass() {

      // remove configuration provider to not disturb other tests
      ConfigurationManager::removeProvider('xml');

      // remove static configuration class loader used for test purposes only
      RootClassLoader::removeLoader(RootClassLoader::getLoaderByVendor(self::TEST_VENDOR));

   }

   public function testSubSectionResolution() {

      $config = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_CONFIG_NAME
      );

      $this->assertEquals('foo', $config->getSection('Section')->getValue('SubSection.ValueOne'));
      $this->assertEquals('foo', $config->getValue('Section.SubSection.ValueOne'));
      $this->assertEquals('foo', $config->getSection('Section')->getSection('SubSection')->getValue('ValueOne'));

      $this->assertEquals('bar', $config->getSection('Section')->getValue('SubSection.ValueTwo'));
      $this->assertEquals('bar', $config->getValue('Section.SubSection.ValueTwo'));
      $this->assertEquals('bar', $config->getSection('Section')->getSection('SubSection')->getValue('ValueTwo'));

   }

   public function testSavingComplexStructure() {

      $config = new XmlConfiguration();

      $values = new XmlConfiguration();
      $values->setValue('ValueOne', 'foo');
      $values->setValue('ValueTwo', 'bar');

      $subSection = new XmlConfiguration();
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

   public function testSectionAccess() {

      $config = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_CONFIG_NAME
      );

      $this->assertEquals('foo', $config->getValue('Section.SubSection.ValueOne'));
      $this->assertEquals('foo', $config->getSection('Section.SubSection')->getValue('ValueOne'));
      $this->assertEquals('foo', $config->getSection('Section')->getSection('SubSection')->getValue('ValueOne'));
   }

}
