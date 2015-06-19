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

use APF\core\configuration\provider\ini\IniConfigurationProvider;
use APF\core\loader\RootClassLoader;
use APF\core\loader\StandardClassLoader;
use ReflectionMethod;

/**
 * Tests the file path generation of file-based configuration providers.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.03.2014<br />
 */
class FilePathGenerationTest extends \PHPUnit_Framework_TestCase {

   /**
    * @const The standard vendor registered for testing purposes.
    */
   const VENDOR_NAME = 'VENDOR';

   /**
    * @var string The configuration root path registered for <em>self::VENDOR_NAME</em>.
    */
   private $configRootPath;

   public function setUp() {
      // register config-only class loader
      $this->configRootPath = dirname(__FILE__) . '/config';
      RootClassLoader::addLoader(new StandardClassLoader(self::VENDOR_NAME, null, $this->configRootPath));
   }

   /**
    * @return ReflectionMethod The <em>BaseConfigurationProvider::getFilePath()</em> method instance.
    */
   private function getFilePathMethod() {
      $method = new ReflectionMethod('APF\core\configuration\provider\ini\IniConfigurationProvider', 'getFilePath');
      $method->setAccessible(true);

      return $method;
   }

   /**
    * @return IniConfigurationProvider The provider instance to test.
    */
   private function getProvider() {
      $provider = new IniConfigurationProvider();
      $provider->setExtension('ini'); // normally this is done by the ConfigurationManager
      return $provider;
   }

   public function testVendorOnlyFilePath() {

      $provider = $this->getProvider();

      $filePath = $this->getFilePathMethod()->invokeArgs(
         $provider,
         // $namespace, $context, $language, $environment, $name
         array(self::VENDOR_NAME, 'foo', null, 'DEFAULT', 'test.ini')
      );

      assertEquals($this->configRootPath . '/config/foo/DEFAULT_test.ini', $filePath);

      $provider->setOmitContext(true);
      $filePath = $this->getFilePathMethod()->invokeArgs(
         $provider,
         // $namespace, $context, $language, $environment, $name
         array(self::VENDOR_NAME, 'foo', null, 'DEFAULT', 'test.ini')
      );

      assertEquals($this->configRootPath . '/config/DEFAULT_test.ini', $filePath);

   }

   /**
    * Test happy case with all parameters.
    */
   public function testFullyQualifiedFilePath() {

      $provider = $this->getProvider();

      $filePath = $this->getFilePathMethod()->invokeArgs(
         $provider,
         // $namespace, $context, $language, $environment, $name
         array(self::VENDOR_NAME . '\foo\bar', 'baz', 'en', 'DEFAULT', 'test.ini')
      );

      assertEquals($this->configRootPath . '/config/foo/bar/baz/DEFAULT_test.ini', $filePath);

   }

   /**
    * Test context omitted.
    */
   public function testContextOmitted() {

      $provider = $this->getProvider();
      $provider->setOmitContext(true);

      $filePath = $this->getFilePathMethod()->invokeArgs(
         $provider,
         // $namespace, $context, $language, $environment, $name
         array(self::VENDOR_NAME . '\foo\bar', 'baz', 'en', 'DEFAULT', 'test.ini')
      );

      assertEquals($this->configRootPath . '/config/foo/bar/DEFAULT_test.ini', $filePath);

   }

   /**
    * Test context omitted.
    */
   public function testEnvironmentOmitted() {

      $provider = $this->getProvider();
      $provider->setOmitEnvironment(true);

      $filePath = $this->getFilePathMethod()->invokeArgs(
         $provider,
         // $namespace, $context, $language, $environment, $name
         array(self::VENDOR_NAME . '\foo\bar', 'baz', 'en', 'DEFAULT', 'test.ini')
      );

      assertEquals($this->configRootPath . '/config/foo/bar/baz/test.ini', $filePath);

   }

   /**
    * Test base path omitted.
    */
   public function testConfigSubFilterOmitted() {

      $provider = $this->getProvider();
      $provider->setOmitConfigSubFolder(true);

      $filePath = $this->getFilePathMethod()->invokeArgs(
         $provider,
         // $namespace, $context, $language, $environment, $name
         array(self::VENDOR_NAME . '\foo\bar', 'baz', 'en', 'DEFAULT', 'test.ini')
      );

      assertEquals($this->configRootPath . '/foo/bar/baz/DEFAULT_test.ini', $filePath);

   }

   /**
    * Test context omitted.
    */
   public function testOmitAll() {

      $provider = $this->getProvider();
      $provider->setOmitContext(true);
      $provider->setOmitEnvironment(true);

      $filePath = $this->getFilePathMethod()->invokeArgs(
         $provider,
         // $namespace, $context, $language, $environment, $name
         array(self::VENDOR_NAME . '\foo\bar', 'baz', 'en', 'DEFAULT', 'test.ini')
      );

      assertEquals($this->configRootPath . '/config/foo/bar/test.ini', $filePath);

   }

}
