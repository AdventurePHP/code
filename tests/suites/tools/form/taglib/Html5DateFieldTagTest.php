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

use APF\tools\form\taglib\Html5DateFieldTag;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Tests rendering of HTML 5 date field.
 */
class Html5DateFieldTagTest extends TestCase {

   public function setUp() {
      $_REQUEST = [];
   }

   public function testHtmlGeneration() {

      $_REQUEST = [];

      $tag = new Html5DateFieldTag();
      $tag->setAttributes([
            'name' => 'foo',
            'required' => 'required'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $html = $tag->transform();

      $this->assertEquals('<input type="date" name="foo" required="required" />', $html);

   }

   public function testPreFilling() {

      // test presetting w/ value in request
      $today = new DateTime('now');
      $_REQUEST = ['foo' => $today->format(Html5DateFieldTag::DATE_FORMAT_PATTERN)];

      $tag = new Html5DateFieldTag();
      $tag->setAttributes([
            'name' => 'foo'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $date = $tag->getDate();
      $this->assertNotNull($date);
      $this->assertInstanceOf(DateTime::class, $date);
      $this->assertEquals(
            $today->format(Html5DateFieldTag::DATE_FORMAT_PATTERN),
            $date->format(Html5DateFieldTag::DATE_FORMAT_PATTERN)
      );

      $date = $tag->getValue();
      $this->assertNotNull($date);
      $this->assertInstanceOf(DateTime::class, $date);
      $this->assertEquals(
            $today->format(Html5DateFieldTag::DATE_FORMAT_PATTERN),
            $date->format(Html5DateFieldTag::DATE_FORMAT_PATTERN)
      );

      // test missing value --> no presetting
      $_REQUEST = [];

      $tag = new Html5DateFieldTag();
      $tag->setAttributes([
            'name' => 'foo'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $this->assertNull($tag->getDate());
   }

   public function testGetterAndSetter() {

      $tag = new Html5DateFieldTag();

      $today = new DateTime('now');

      // test explicit methods
      $tag->setDate($today);
      $this->assertEquals(
            $today->format(Html5DateFieldTag::DATE_FORMAT_PATTERN),
            $tag->getDate()->format(Html5DateFieldTag::DATE_FORMAT_PATTERN)
      );

      $tag->setDate(null);
      $this->assertNull($tag->getDate());

      // test standard form methods
      $tag->setValue($today);
      $this->assertEquals(
            $today->format(Html5DateFieldTag::DATE_FORMAT_PATTERN),
            $tag->getValue()->format(Html5DateFieldTag::DATE_FORMAT_PATTERN)
      );

      // test string initialization
      $date = '2017-10-01';

      $tag = new Html5DateFieldTag();
      $tag->setDate($date);

      $this->assertEquals(new DateTime($date), $tag->getDate());

   }

   public function testVisibility() {

      $tag = new Html5DateFieldTag();
      $tag->setAttribute('name', 'foo');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->hide();

      $this->assertEmpty($tag->transform());

   }

}
