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
namespace APF\tests\suites\core\pagecontroller;

use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\provider\ini\IniConfigurationProvider;
use APF\core\loader\RootClassLoader;
use APF\core\loader\StandardClassLoader;
use APF\core\pagecontroller\LanguageLabelTag;
use APF\core\pagecontroller\TemplateTag;
use InvalidArgumentException;

/**
 * Tests all capabilities of the language label tag.
 */
class LanguageLabelTagTest extends \PHPUnit_Framework_TestCase {

   const TEST_VENDOR = 'CORE';
   const CONFIG_FILE_NAME = 'labels.ini';

   private static $originalProvider;

   /**
    * @const string Language of the application (important for language-dependent readout of language files!)
    */
   const LANGUAGE = 'de';

   public static function setUpBeforeClass() {
      // setup static configuration resource path for test purposes
      RootClassLoader::addLoader(new StandardClassLoader(self::TEST_VENDOR, __DIR__ . '/test-config'));

      // setup configuration provider for this test (remember previous one)
      self::$originalProvider = ConfigurationManager::retrieveProvider('ini');

      $provider = new IniConfigurationProvider();
      $provider->setOmitContext(true);
      $provider->setOmitConfigSubFolder(true);
      ConfigurationManager::registerProvider('ini', $provider);
   }

   public static function tearDownAfterClass() {
      // restore previous configuration provider to not disturb other tests
      ConfigurationManager::registerProvider('ini', self::$originalProvider);

      // remove static configuration class loader used for test purposes only
      RootClassLoader::removeLoader(RootClassLoader::getLoaderByVendor(self::TEST_VENDOR));
   }

   public function testMissingAttribute1() {
      $this->expectException(InvalidArgumentException::class);
      $node = $this->getTag();
      $node->transform();
   }

   public function testMissingAttribute2() {
      $this->expectException(InvalidArgumentException::class);
      $node = $this->getTag();
      $node->setAttributes([
            'namespace' => 'dummy'
      ]);
      $node->transform();
   }

   public function testMissingAttribute3() {
      $this->expectException(InvalidArgumentException::class);
      $node = $this->getTag();
      $node->setAttributes([
            'namespace' => 'dummy',
            'config' => 'dummy'
      ]);
      $node->transform();
   }

   public function testLanguageLabel1() {

      $node = $this->getTag();
      $node->setAttributes([
            'namespace' => self::TEST_VENDOR,
            'config' => self::CONFIG_FILE_NAME,
            'entry' => 'simple'
      ]);

      // Exclusion test that setting a place holder does not influence static text
      $node->setPlaceHolder('place-holder', 'test');

      $this->assertEquals('This is a test!', $node->transform());

   }

   public function testLanguageLabel2() {

      $node = $this->getTag();
      $node->setAttributes([
            'namespace' => self::TEST_VENDOR,
            'config' => self::CONFIG_FILE_NAME,
            'entry' => 'complex'
      ]);
      $node->setPlaceHolder('place-holder', 'test');

      $this->assertEquals('This is a test!', $node->transform());

   }

   public function testLanguageLabel3() {

      $this->expectException(InvalidArgumentException::class);
      $node = $this->getTag();
      $node->setAttributes([
            'namespace' => self::TEST_VENDOR,
            'config' => self::CONFIG_FILE_NAME,
            'entry' => 'not-existing'
      ]);
      $node->transform();

   }

   /**
    * Complex use case within a template. Checks whether place holder in place holder works.
    */
   public function testLanguageLabel4() {

      $node = new TemplateTag();
      $node->setLanguage(self::LANGUAGE);
      $node->setContent('<h2><html:getstring id="foo" '
            . 'namespace="' . self::TEST_VENDOR . '" '
            . 'config="' . self::CONFIG_FILE_NAME . '"'
            . ' entry="complex" /></h2>');
      $node->onParseTime();
      $node->onAfterAppend();

      $node->getChildNode('id', 'foo', LanguageLabelTag::class)->setPlaceHolder('place-holder', 'test');

      $this->assertEquals('<h2>This is a test!</h2>', $node->transformTemplate());
   }

   /**
    * @return LanguageLabelTag
    */
   private function getTag() {
      $tag = new LanguageLabelTag();
      $tag->setLanguage(self::LANGUAGE);
      return $tag;
   }

}
