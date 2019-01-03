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
namespace APF\tests\suites\tools\form\taglib;

use APF\tools\form\taglib\Html5WeekFieldTag;
use DateTime;

/**
 * Tests rendering of HTML 5 week field.
 */
class Html5WeekFieldTagTest extends \PHPUnit_Framework_TestCase {

   public function setUp() {
      $_REQUEST = [];
   }

   public function testHtmlGeneration() {

      $_REQUEST = [];

      $tag = new Html5WeekFieldTag();
      $tag->setAttributes([
            'name' => 'foo',
            'required' => 'required'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $html = $tag->transform();

      $this->assertEquals('<input type="week" name="foo" required="required" />', $html);

   }

   public function testPreFilling() {

      // test presetting w/ value in request
      $today = new DateTime('2018-10-1');
      $_REQUEST = ['foo' => $today->format(Html5WeekFieldTag::WEEK_FORMAT_PATTERN)];

      $tag = new Html5WeekFieldTag();
      $tag->setAttributes([
            'name' => 'foo'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $date = $tag->getWeek();
      $this->assertNotNull($date);
      $this->assertInstanceOf(DateTime::class, $date);
      $this->assertEquals(
            $today->format(Html5WeekFieldTag::WEEK_FORMAT_PATTERN),
            $date->format(Html5WeekFieldTag::WEEK_FORMAT_PATTERN)
      );

      $date = $tag->getValue();
      $this->assertNotNull($date);
      $this->assertInstanceOf(DateTime::class, $date);
      $this->assertEquals(
            $today->format(Html5WeekFieldTag::WEEK_FORMAT_PATTERN),
            $date->format(Html5WeekFieldTag::WEEK_FORMAT_PATTERN)
      );

      // test missing value --> no presetting
      $_REQUEST = [];

      $tag = new Html5WeekFieldTag();
      $tag->setAttributes([
            'name' => 'foo'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $this->assertNull($tag->getWeek());
   }

   public function testGetterAndSetter() {

      $tag = new Html5WeekFieldTag();

      $today = new DateTime('2018-10-1');

      // test explicit methods
      $tag->setWeek($today);
      $this->assertEquals(
            $today->format(Html5WeekFieldTag::WEEK_FORMAT_PATTERN),
            $tag->getWeek()->format(Html5WeekFieldTag::WEEK_FORMAT_PATTERN)
      );

      $tag->setWeek(null);
      $this->assertNull($tag->getWeek());

      // test standard form methods
      $tag->setValue($today);
      $this->assertEquals(
            $today->format(Html5WeekFieldTag::WEEK_FORMAT_PATTERN),
            $tag->getValue()->format(Html5WeekFieldTag::WEEK_FORMAT_PATTERN)
      );

      // test string initialization
      $date = '2017-W10';

      $tag = new Html5WeekFieldTag();
      $tag->setWeek($date);

      $this->assertEquals(new DateTime($date), $tag->getWeek());

   }

   public function testVisibility() {

      $tag = new Html5WeekFieldTag();
      $tag->setAttribute('name', 'foo');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->hide();

      $this->assertEmpty($tag->transform());

   }

}
