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

use APF\core\pagecontroller\ParserException;
use APF\tools\form\FormException;
use APF\tools\form\taglib\Html5DateTimeLocalFieldTag;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Tests rendering of HTML 5 date and time field.
 */
class Html5DateTimeLocalFieldTagTest extends TestCase {

   public function setUp() {
      $_REQUEST = [];
   }

   /**
    * @throws ParserException
    * @throws FormException
    */
   public function testHtmlGeneration() {

      $_REQUEST = [];

      $tag = new Html5DateTimeLocalFieldTag();
      $tag->setAttributes([
            'name' => 'foo',
            'required' => 'required'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $html = $tag->transform();

      $this->assertEquals('<input type="datetime-local" name="foo" required="required" />', $html);

   }

   /**
    * @throws FormException
    * @throws ParserException
    */
   public function testPreFilling() {

      // test presetting w/ value in request
      $today = new DateTime('now');
      $_REQUEST = ['foo' => $today->format(Html5DateTimeLocalFieldTag::DATE_TIME_FORMAT_PATTERN)];

      $tag = new Html5DateTimeLocalFieldTag();
      $tag->setAttributes([
            'name' => 'foo'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $date = $tag->getDateTime();
      $this->assertNotNull($date);
      $this->assertInstanceOf(DateTime::class, $date);
      $this->assertEquals(
            $today->format(Html5DateTimeLocalFieldTag::DATE_TIME_FORMAT_PATTERN),
            $date->format(Html5DateTimeLocalFieldTag::DATE_TIME_FORMAT_PATTERN)
      );

      $date = $tag->getValue();
      $this->assertNotNull($date);
      $this->assertInstanceOf(DateTime::class, $date);
      $this->assertEquals(
            $today->format(Html5DateTimeLocalFieldTag::DATE_TIME_FORMAT_PATTERN),
            $date->format(Html5DateTimeLocalFieldTag::DATE_TIME_FORMAT_PATTERN)
      );

      // test missing value --> no presetting
      $_REQUEST = [];

      $tag = new Html5DateTimeLocalFieldTag();
      $tag->setAttributes([
            'name' => 'foo'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $this->assertNull($tag->getDateTime());
   }

   public function testGetterAndSetter() {

      $tag = new Html5DateTimeLocalFieldTag();

      $today = new DateTime('now');

      // test explicit methods
      $tag->setDateTime($today);
      $this->assertEquals(
            $today->format(Html5DateTimeLocalFieldTag::DATE_TIME_FORMAT_PATTERN),
            $tag->getDateTime()->format(Html5DateTimeLocalFieldTag::DATE_TIME_FORMAT_PATTERN)
      );

      $tag->setDateTime(null);
      $this->assertNull($tag->getDateTime());

      // test standard form methods
      $tag->setValue($today);
      $this->assertEquals(
            $today->format(Html5DateTimeLocalFieldTag::DATE_TIME_FORMAT_PATTERN),
            $tag->getValue()->format(Html5DateTimeLocalFieldTag::DATE_TIME_FORMAT_PATTERN)
      );

      // test string initialization
      $date = '2017-10-01T15:18';

      $tag = new Html5DateTimeLocalFieldTag();
      $tag->setDateTime($date);

      $this->assertEquals(new DateTime($date), $tag->getDateTime());

   }

   /**
    * @throws FormException
    * @throws ParserException
    */
   public function testVisibility() {

      $tag = new Html5DateTimeLocalFieldTag();
      $tag->setAttribute('name', 'foo');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->hide();

      $this->assertEmpty($tag->transform());

   }

}
