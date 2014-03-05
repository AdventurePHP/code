<?php
namespace APF\tests\suites\core\pagecontroller;

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
use APF\core\loader\RootClassLoader;
use APF\core\loader\StandardClassLoader;
use APF\core\pagecontroller\Document;
use ReflectionMethod;

/**
 * @package APF\tests\suites\core\pagecontroller
 * @class DocumentTest
 *
 * Tests the <em>Document::getTemplateFilePath()</em> regarding class loader usage.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.02.2014<br />
 */
class DocumentTest extends \PHPUnit_Framework_TestCase {

   const VENDOR = 'VENDOR';
   const SOURCE_PATH = '/var/www/html/src';

   protected function setUp() {
      RootClassLoader::addLoader(new StandardClassLoader(self::VENDOR, self::SOURCE_PATH));
   }

   /**
    * @return ReflectionMethod The <em>APF\core\pagecontroller\Document::getTemplateFilePath()</em> method.
    */
   private function getMethod() {
      $method = new ReflectionMethod('APF\core\pagecontroller\Document', 'getTemplateFilePath');
      $method->setAccessible(true);

      return $method;
   }

   public function testWithNormalNamespace() {

      $method = $this->getMethod();
      $document = new Document();

      $filePath = $method->invokeArgs($document, array(self::VENDOR . '\foo', 'bar'));
      assertEquals(self::SOURCE_PATH . '/foo/bar.html', $filePath);

      $filePath = $method->invokeArgs($document, array(self::VENDOR . '\foo\bar', 'baz'));
      assertEquals(self::SOURCE_PATH . '/foo/bar/baz.html', $filePath);

   }

   public function testWithVendorOnly() {
      $filePath = $this->getMethod()->invokeArgs(new Document(), array(self::VENDOR, 'foo'));
      assertEquals(self::SOURCE_PATH . '/foo.html', $filePath);
   }

}
