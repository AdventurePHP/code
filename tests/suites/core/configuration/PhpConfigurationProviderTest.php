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
use APF\core\configuration\provider\php\PhpConfigurationProvider;
use APF\core\loader\RootClassLoader;
use APF\core\loader\StandardClassLoader;

/**
 * Tests the PHP file based configuration provider.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 07.03.2015 (see ID#243 for requirement details)<br />
 */
class PhpConfigurationProviderTest extends \PHPUnit_Framework_TestCase {

   const TEST_VENDOR = 'TEST';
   const TEST_NAMESPACE = '';
   const TEST_ENVIRONMENT = 'DEFAULT';
   const TEST_CONFIG_NAME = 'test-config.php';
   const TEST_NEW_CONFIG_NAME = 'test-config-new.php';

   const CONFIG_ROOT_FOLDER = 'test-config';

   public static function setUpBeforeClass() {

      // setup static configuration resource path for test purposes
      RootClassLoader::addLoader(new StandardClassLoader(self::TEST_VENDOR, __DIR__ . '/' . self::CONFIG_ROOT_FOLDER));

      // setup configuration provider for this test
      $provider = new PhpConfigurationProvider();
      $provider->setOmitContext(true);
      $provider->setOmitConfigSubFolder(true);
      ConfigurationManager::registerProvider('php', $provider);

   }

   public function testLoadConfigurationFile() {
      $config = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_CONFIG_NAME
      );

      assertEquals($config->getValue('value 1'), 'foo');
      assertEquals($config->getValue('value 2'), 'bar');
      assertEquals($config->getValue('php-version'), PHP_VERSION_ID);

      assertInstanceOf('APF\core\configuration\Configuration', $config->getSection('section 1'));
      assertInstanceOf('APF\core\configuration\Configuration', $config->getSection('section 1')->getSection('subsection 1'));
      assertInstanceOf('APF\core\configuration\Configuration', $config->getSection('section 1')->getSection('subsection 2'));
   }

   public function testFailToLoadConfigurationFile() {
      $this->setExpectedException('APF\core\configuration\ConfigurationException');
      ConfigurationManager::loadConfiguration(self::TEST_VENDOR, null, null, self::TEST_ENVIRONMENT, 'non-existing.php');
   }

   public function testSaveConfiguration() {
      $config = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_CONFIG_NAME
      );

      ConfigurationManager::saveConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_NEW_CONFIG_NAME,
            $config
      );

      $newConfig = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            null,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_NEW_CONFIG_NAME
      );

      assertEquals($config, $newConfig);

      assertEquals($config->getValue('value 1'), $newConfig->getValue('value 1'));
      assertEquals($config->getValue('value 2'), $newConfig->getValue('value 2'));
      assertEquals($config->getValue('php-version'), $newConfig->getValue('php-version'));

      assertInstanceOf('APF\core\configuration\Configuration', $newConfig->getSection('section 1'));
      assertInstanceOf('APF\core\configuration\Configuration', $newConfig->getSection('section 1')->getSection('subsection 1'));
      assertInstanceOf('APF\core\configuration\Configuration', $newConfig->getSection('section 1')->getSection('subsection 2'));

      assertEquals(
            $config->getSection('section 1')->getSection('subsection 1')->getValue('value 1'),
            $newConfig->getSection('section 1')->getSection('subsection 1')->getValue('value 1')
      );
      assertEquals(
            $config->getSection('section 1')->getSection('subsection 1')->getValue('value 2'),
            $newConfig->getSection('section 1')->getSection('subsection 1')->getValue('value 2')
      );
      assertEquals(
            $config->getSection('section 1')->getSection('subsection 2')->getValue('value 1'),
            $newConfig->getSection('section 1')->getSection('subsection 2')->getValue('value 1')
      );
      assertEquals(
            $config->getSection('section 1')->getSection('subsection 2')->getValue('value 2'),
            $newConfig->getSection('section 1')->getSection('subsection 2')->getValue('value 2')
      );

   }

   public function testDeleteConfigurationFile() {

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
         assertTrue(true);
      }

   }

   public static function tearDownAfterClass() {

      // remove configuration provider to not disturb other tests
      ConfigurationManager::removeProvider('php');

      // remove static configuration class loader used for test purposes only
      RootClassLoader::removeLoader(RootClassLoader::getLoaderByVendor(self::TEST_VENDOR));

   }

}
