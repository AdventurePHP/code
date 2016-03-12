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
use APF\tools\html\taglib\HtmlIteratorTag;

class HtmlIteratorItemTagTest extends \PHPUnit_Framework_TestCase {

   /**
    * Tests whether place holders are returned correctly.
    */
   public function testPlaceHolders() {

      $tag = new HtmlIteratorItemTag();
      $tag->setContent('${foo}<html:template name="bar" />${baz}');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $children = $tag->getChildren();
      $this->assertCount(3, $children);

      $keys = array_keys($children);

      // note: due to the fact that tags are parsed during
      // onParseTime() and expression tags during onAfterAppend()
      // the order is template -> place holder -> place holder
      $this->assertInstanceOf(Template::class, $children[$keys[0]]);
      $this->assertInstanceOf(PlaceHolder::class, $children[$keys[1]]);
      $this->assertInstanceOf(PlaceHolder::class, $children[$keys[2]]);

      $placeHolders = $tag->getPlaceHolderNames();
      $this->assertCount(2, $placeHolders);
   }

   /**
    * Test whether empty list is returned in case no place holder is defined.
    */
   public function testEmptyList() {
      $tag = new HtmlIteratorItemTag();
      $tag->onParseTime();
      $tag->onAfterAppend();
      $this->assertEmpty($tag->getPlaceHolderNames());
   }

   public function testListWithPlaceHolders() {

      $tag = new HtmlIteratorTag();
      $tag->setContent('<ul><iterator:item><li>${content}</li></iterator:item></ul>');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->fillDataContainer([
            ['content' => 'test'],
            ['content' => 'test'],
            ['content' => 'test']
      ]);

      // transformOnPlace=false
      $this->assertEmpty($tag->transform());

      $tag->transformOnPlace();
      $actual = $tag->transform();

      $this->assertContains('<ul>', $actual, 'List structure wrong!');
      $this->assertContains('</ul>', $actual, 'List structure wrong!');
      $this->assertEquals(
            3,
            substr_count($actual, '<li>test</li>'),
            'Output "' . $actual . '" does not contain sufficient number of list items.'
      );
   }

   /**
    * Tests iterations with different content per iteration and including the iteration number place holder.
    */
   public function testIterationNumber() {

      $tag = new HtmlIteratorTag();
      $tag->setContent('<ul><iterator:item><li>${IterationNumber}|${content}</li></iterator:item></ul>');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->fillDataContainer([
            ['content' => 'test-1'],
            ['content' => 'test-2'],
            ['content' => 'test-3']
      ]);

      $tag->transformOnPlace();

      $actual = $tag->transform();

      $this->assertContains('<ul>', $actual, 'List structure wrong!');
      $this->assertContains('</ul>', $actual, 'List structure wrong!');
      $this->assertEquals(
            3,
            substr_count($actual, '<li>'),
            'Output "' . $actual . '" does not contain sufficient number of list items.'
      );
      $this->assertEquals(
            3,
            substr_count($actual, '</li>'),
            'Output "' . $actual . '" does not contain sufficient number of list items.'
      );
      $this->assertContains('1|test-1', $actual);
      $this->assertContains('2|test-2', $actual);
      $this->assertContains('3|test-3', $actual);
   }

   public function testGenericGettersForPlaceHolders() {

      $tag = new HtmlIteratorTag();
      $tag->setContent('<ul><iterator:item getter="get"><li>${IterationNumber}|${content}</li></iterator:item></ul>');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->fillDataContainer([
            new GenericGetterModel(['content' => 'test-1']),
            new GenericGetterModel(['content' => 'test-2']),
            new GenericGetterModel(['content' => 'test-3'])
      ]);

      $tag->transformOnPlace();

      $actual = $tag->transform();

      $this->assertContains('<ul>', $actual, 'List structure wrong!');
      $this->assertContains('</ul>', $actual, 'List structure wrong!');
      $this->assertEquals(
            3,
            substr_count($actual, '<li>'),
            'Output "' . $actual . '" does not contain sufficient number of list items.'
      );
      $this->assertEquals(
            3,
            substr_count($actual, '</li>'),
            'Output "' . $actual . '" does not contain sufficient number of list items.'
      );
      $this->assertContains('1|test-1', $actual);
      $this->assertContains('2|test-2', $actual);
      $this->assertContains('3|test-3', $actual);
   }

}
