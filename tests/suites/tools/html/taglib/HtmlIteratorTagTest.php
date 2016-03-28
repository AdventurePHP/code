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
use InvalidArgumentException;

class HtmlIteratorTagTest extends \PHPUnit_Framework_TestCase {

   private static $testData = [
         ['content' => 'test-1'],
         ['content' => 'test-2'],
         ['content' => 'test-3']
   ];

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

      $tag->fillDataContainer(self::$testData);

      // transformOnPlace=false
      $this->assertEmpty($tag->transform());

      $tag->transformOnPlace();
      $actual = $tag->transform();

      $this->assertContains('<ul>', $actual, 'List structure wrong!');
      $this->assertContains('</ul>', $actual, 'List structure wrong!');
      $this->assertEquals(
            3,
            substr_count($actual, '<li>test-'),
            'Output "' . $actual . '" does not contain sufficient number of list items.'
      );
   }

   /**
    * Tests iterations with different content per iteration and including the iteration number place holder.
    */
   public function testIterationNumber() {

      $tag = new HtmlIteratorTag();
      $tag->setContent('<ul><iterator:item><li>${' . HtmlIteratorTag::ITERATION_NUMBER_PLACE_HOLDER . '}|${content}</li></iterator:item></ul>');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->fillDataContainer(self::$testData);

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
      $tag->setContent('<ul><iterator:item getter="get"><li>${' . HtmlIteratorTag::ITERATION_NUMBER_PLACE_HOLDER . '}|${content}</li></iterator:item></ul>');
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

   /**
    * Test missing <iterator:item /> definition.
    */
   public function testMissingIteratorItem() {
      $this->expectException(InvalidArgumentException::class);

      $tag = new HtmlIteratorTag();
      $tag->onParseTime();
      $tag->onAfterAppend();
      $tag->transformIterator();
   }

   /**
    * Empty data container should result in an empty iterator (w/ static HTML).
    */
   public function testEmptyIterator() {
      $tag = new HtmlIteratorTag();
      $tag->setContent('<ul><iterator:item /></ul>');
      $tag->onParseTime();
      $tag->onAfterAppend();
      $this->assertEquals('<ul></ul>', $tag->transformIterator());
   }

   /**
    * Test exclusive fallback content.
    */
   public function testFallbackContent1() {
      $expected = 'fallback';

      $tag = new HtmlIteratorTag();
      $tag->setContent('<ul><iterator:fallback>' . $expected . '</iterator:fallback><iterator:item /></ul>');
      $tag->setAttribute('fallback-mode', HtmlIteratorTag::FALLBACK_MODE_REPLACE);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $actual = $tag->transformIterator();
      $this->assertEquals($expected, $actual);
      $this->assertNotContains('<ul>', $actual);
      $this->assertNotContains('</ul>', $actual);
   }

   /**
    * Test integrated fallback content.
    */
   public function testFallbackContent2() {
      $expected = 'fallback';

      $tag = new HtmlIteratorTag();
      $tag->setContent('<ul><iterator:fallback>' . $expected . '</iterator:fallback><iterator:item /></ul>');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $actual = $tag->transformIterator();
      $this->assertContains($expected, $actual);
      $this->assertContains('<ul>', $actual);
      $this->assertContains('</ul>', $actual);
   }

   /**
    * Tests iteration status model capabilities. Includes tests for using expression language accessing
    * the status through ${status} and the current item through ${item}.
    */
   public function testIterationStatus() {

      $tag = new HtmlIteratorTag();
      $tag->setContent('<ul>'
            . '<iterator:item>'
            . '<li>'
            . '${item[\'content\']}|${status->isFirst(true)}|${status->isLast(true)}|${status->getItemCount()}|${status->getCounter()}|${status->getCssClass()}'
            . '</li>'
            . '</iterator:item>'
            . '</ul>');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->fillDataContainer(self::$testData);

      $tag->transformOnPlace();

      $actual = $tag->transform();

      $this->assertContains('<li>test-1|1|0|3|1|' . HtmlIteratorTag::DEFAULT_CSS_CLASS_FIRST . '</li>', $actual);
      $this->assertContains('<li>test-2|0|0|3|2|' . HtmlIteratorTag::DEFAULT_CSS_CLASS_MIDDLE . '</li>', $actual);
      $this->assertContains('<li>test-3|0|1|3|3|' . HtmlIteratorTag::DEFAULT_CSS_CLASS_LAST . '</li>', $actual);
   }

   /**
    * Tests whether the CSS classes for first, middle, and last entries in status model can be overwritten.
    */
   public function testCssClassDefinition() {

      $tag = new HtmlIteratorTag();
      $tag->setContent('<iterator:item>${status->getCssClass()}</iterator:item>');
      $tag->setAttributes([
            'first-element-css-class'  => 'foo',
            'middle-element-css-class' => 'bar',
            'last-element-css-class'   => 'baz'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->fillDataContainer(self::$testData);

      $tag->transformOnPlace();

      $actual = $tag->transform();

      $this->assertContains('foobarbaz', $actual);
   }

   /**
    * Tests whether setting an initial iteration number overwrites the internal value for iteration.
    */
   public function testIterationNumberDefinition() {
      $tag = new HtmlIteratorTag();
      $tag->setContent('<iterator:item>${' . HtmlIteratorTag::ITERATION_NUMBER_PLACE_HOLDER . '}</iterator:item>');
      $tag->setIterationNumber(7);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->fillDataContainer(self::$testData);

      $tag->transformOnPlace();
      $this->assertContains('789', $tag->transformIterator());
   }

   /**
    * Tests whether an arbitrary tag is processed within the iterator - e.g. place holder.
    */
   public function testArbitraryTagIsProcessed() {
      $tag = new HtmlIteratorTag();
      $tag->setContent('${placeHolder}<iterator:item />');

      // allow direct transformation w/o controller interaction (no transformOnPlace() call required!)
      $tag->setAttribute('transform-on-place', 'true');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $expected = 'foo';
      $tag->setPlaceHolder('placeHolder', $expected);

      // use transform() as iterator should be displayed directly with transform-on-place=true
      $this->assertEquals($expected, $tag->transform());
   }

}
