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

use APF\tools\form\taglib\Html5RangeFieldTag;
use InvalidArgumentException;

/**
 * Tests rendering of HTML 5 range field.
 */
class Html5RangeFieldTagTest extends \PHPUnit_Framework_TestCase {

   public function setUp() {
      $_REQUEST = [];
   }

   public function testHtmlGeneration() {

      $tag = new Html5RangeFieldTag();
      $tag->setAttributes([
            'name' => 'foo',
            'min' => '10',
            'max' => '100',
            'bar' => 'baz'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $html = $tag->transform();

      $this->assertEquals('<input type="range" name="foo" min="10" max="100" />', $html);

   }

   public function testInvalidRangeDefinition() {
      $this->expectException(InvalidArgumentException::class);

      $tag = new Html5RangeFieldTag();
      $tag->setAttributes([
            'name' => 'foo',
            'min' => '100',
            'max' => '10'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->transform();

   }

   public function testVisibility() {

      $tag = new Html5RangeFieldTag();
      $tag->setAttributes([
            'name' => 'foo',
            'min' => '1',
            'max' => '10'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      $tag->hide();

      $this->assertEmpty($tag->transform());

   }

}
