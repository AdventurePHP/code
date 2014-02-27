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

   public function testWithNormalNamespace() {

      $doc = new Document();

      assertEquals(
         self::SOURCE_PATH . '/foo/bar.html',
         $doc->publicGetTemplateFilePath(self::VENDOR . '\foo', 'bar')
      );

      assertEquals(
         self::SOURCE_PATH . '/foo/bar/baz.html',
         $doc->publicGetTemplateFilePath(self::VENDOR . '\foo\bar', 'baz')
      );

   }

   public function testWithVendorOnly() {

      $doc = new Document();

      assertEquals(
         self::SOURCE_PATH . '/foo.html',
         $doc->publicGetTemplateFilePath(self::VENDOR, 'foo')
      );

   }

}
