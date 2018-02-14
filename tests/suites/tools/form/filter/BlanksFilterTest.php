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
namespace APF\tests\suites\tools\form\filter;

use APF\tools\form\filter\BlanksFilter;
use APF\tools\form\taglib\ButtonTag;
use APF\tools\form\taglib\TextFieldTag;

/**
 * Tests the BlanksFilter capabilities.
 */
class BlanksFilterTest extends \PHPUnit_Framework_TestCase {

   public function testNoBlanks() {

      $filter = $this->getFilter();

      $input = 'asdfghkl';

      $this->assertEquals($input, $filter->filter($input));

   }

   public function testBlanks() {

      $filter = $this->getFilter();

      // blanks somewhere in between
      $this->assertEquals('asdfghkl', $filter->filter('as df ghkl'));

      // leading blanks
      $this->assertEquals('asdfghkl', $filter->filter(' asdf ghkl'));

      // multiple leading blanks
      $this->assertEquals('asdfghkl', $filter->filter('  as dfghkl'));

      // trailing blanks
      $this->assertEquals('asdfghkl', $filter->filter('as dfghkl '));

      // multiple trailing blanks
      $this->assertEquals('asdfghkl', $filter->filter('as df ghkl  '));

   }

   protected function getFilter() {
      return new BlanksFilter(new TextFieldTag(), new ButtonTag());
   }

}
