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

use APF\core\pagecontroller\AddTaglibTag;
use APF\core\pagecontroller\Document;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * Tests AddTaglibTag.
 */
class AddTaglibTagTest extends TestCase {

   /**
    * Test incomplete tag definition throws exception.
    */
   public function testIncompleteTagDefinition1() {

      $this->expectException(InvalidArgumentException::class);

      $tag = new AddTaglibTag();
      $tag->setAttributes(
            [
                  'prefix' => 'dummy'
            ]
      );
      $tag->onParseTime();
   }

   /**
    * Test incomplete tag definition throws exception.
    */
   public function testIncompleteTagDefinition2() {

      $this->expectException(InvalidArgumentException::class);
      $tag = new AddTaglibTag();
      $tag->setAttributes(
            [
                  'prefix' => 'dummy',
                  'name' => 'tag'
            ]
      );
      $tag->onParseTime();
   }

   /**
    * Test tag registration.
    */
   public function testAddTagLib3() {

      $property = new ReflectionProperty(Document::class, 'knownTags');
      $property->setAccessible(true);
      $knownTags = $property->getValue();

      $this->assertFalse(isset($knownTags['dummy:tag']));

      $tag = new AddTaglibTag();
      $tag->setAttributes(
            [
                  'prefix' => 'dummy',
                  'name' => 'tag',
                  'class' => DummyTag::class,
            ]
      );
      $tag->onParseTime();

      $knownTags = $property->getValue();
      $this->assertTrue(isset($knownTags['dummy:tag']));

      $this->assertEmpty($tag->getAttributes());
      $this->assertEmpty($tag->transform());

   }
}
