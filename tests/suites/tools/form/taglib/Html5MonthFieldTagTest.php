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
use APF\tools\form\taglib\Html5MonthFieldTag;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Tests rendering of HTML 5 month field.
 */
class Html5MonthFieldTagTest extends TestCase {

   public function setUp() {
      $_REQUEST = [];
   }

   /**
    * @throws ParserException
    * @throws FormException
    */
   public function testHtmlGeneration() {

      $_REQUEST = [];

      $tag = new Html5MonthFieldTag();
      $tag->setAttributes([
            'name' => 'foo',
            'required' => 'required'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $html = $tag->transform();

      $this->assertEquals('<input type="month" name="foo" required="required" />', $html);

   }

   /**
    * @throws FormException
    * @throws ParserException
    */
   public function testPreFilling() {

      // test presetting w/ value in request
      $today = new DateTime('now');
      $_REQUEST = ['foo' => $today->format(Html5MonthFieldTag::MONTH_FORMAT_PATTERN)];

      $tag = new Html5MonthFieldTag();
      $tag->setAttributes([
            'name' => 'foo'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $date = $tag->getMonth();
      $this->assertNotNull($date);
      $this->assertInstanceOf(DateTime::class, $date);
      $this->assertEquals(
            $today->format(Html5MonthFieldTag::MONTH_FORMAT_PATTERN),
            $date->format(Html5MonthFieldTag::MONTH_FORMAT_PATTERN)
      );

      $date = $tag->getValue();
      $this->assertNotNull($date);
      $this->assertInstanceOf(DateTime::class, $date);
      $this->assertEquals(
            $today->format(Html5MonthFieldTag::MONTH_FORMAT_PATTERN),
            $date->format(Html5MonthFieldTag::MONTH_FORMAT_PATTERN)
      );

      // test missing value --> no presetting
      $_REQUEST = [];

      $tag = new Html5MonthFieldTag();
      $tag->setAttributes([
            'name' => 'foo'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $this->assertNull($tag->getMonth());
   }

   public function testGetterAndSetter() {

      $tag = new Html5MonthFieldTag();

      $today = new DateTime('now');

      // test explicit methods
      $tag->setMonth($today);
      $this->assertEquals(
            $today->format(Html5MonthFieldTag::MONTH_FORMAT_PATTERN),
            $tag->getMonth()->format(Html5MonthFieldTag::MONTH_FORMAT_PATTERN)
      );

      $tag->setMonth(null);
      $this->assertNull($tag->getMonth());

      // test standard form methods
      $tag->setValue($today);
      $this->assertEquals(
            $today->format(Html5MonthFieldTag::MONTH_FORMAT_PATTERN),
            $tag->getValue()->format(Html5MonthFieldTag::MONTH_FORMAT_PATTERN)
      );

      // test string initialization
      $date = '2017-10';

      $tag = new Html5MonthFieldTag();
      $tag->setMonth($date);

      $this->assertEquals(new DateTime($date), $tag->getMonth());

   }

   /**
    * @throws FormException
    * @throws ParserException
    */
   public function testVisibility() {

      $tag = new Html5MonthFieldTag();
      $tag->setAttribute('name', 'foo');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->hide();

      $this->assertEmpty($tag->transform());

   }

}
