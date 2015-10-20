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
namespace APF\tests\suites\tools\html\taglib;

use APF\core\pagecontroller\PlaceHolder;
use APF\core\pagecontroller\Template;
use APF\tools\html\taglib\HtmlIteratorItemTag;

class HtmlIteratorItemTagTest extends \PHPUnit_Framework_TestCase {

   /**
    * Tests whether place holders are returned correctly.
    */
   public function testPlaceHolders() {

      $tag = new HtmlIteratorItemTag();
      $tag->setContent('${foo}<html:template name="bar" />${baz}');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $children = &$tag->getChildren();
      $this->assertCount(3, $children);

      $keys = array_keys($children);

      // note: due to the fact that tags are parsed during
      // onParseTime() and expression tags during onAfterAppend()
      // the order is template -> place holder -> place holder
      $this->assertInstanceOf(Template::class, $children[$keys[0]]);
      $this->assertInstanceOf(PlaceHolder::class, $children[$keys[1]]);
      $this->assertInstanceOf(PlaceHolder::class, $children[$keys[2]]);

      $placeHolders = $tag->getPlaceHolders();

      $this->assertInstanceOf(PlaceHolder::class, $placeHolders[0]);
      $this->assertInstanceOf(PlaceHolder::class, $placeHolders[1]);

      // check whether references are returns instead of copies
      $this->assertEquals(
            spl_object_hash($children[$keys[1]]),
            spl_object_hash($placeHolders[0])
      );
      $this->assertEquals(
            spl_object_hash($children[$keys[2]]),
            spl_object_hash($placeHolders[1])
      );

   }

   /**
    * Test whether empty list is returned in case no place holder is defined.
    */
   public function testEmptyList() {
      $tag = new HtmlIteratorItemTag();
      $tag->onParseTime();
      $tag->onAfterAppend();
      $this->assertEmpty($tag->getPlaceHolders());
   }

}
