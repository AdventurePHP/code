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
namespace APF\tests\suites\core\expression;

use APF\core\expression\ArgumentParser;

/**
 * Tests the capabilities of the ArgumentParser class used in APF's template expression classes.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 24.02.2018b<br />
 */
class ArgumentParserTest extends \PHPUnit_Framework_TestCase {

   /**
    * Test the following expressions:
    *
    * - foo, bar (no quotes)
    * - 'foo', 'bar' (single quotes)
    * - "foo", "bar" (double quotes)
    * - 'foo', "bar" (mixed quotes)
    * - 'foo', 'a"b"c' (nested quotes)
    * - 'bar baz' (arguments w/ spaces)
    */
   public function testGetArguments() {

      $this->assertEquals(
            [],
            ArgumentParser::getArguments(' ')
      );

      $expected = ['foo', 'bar'];
      $this->assertEquals(
            $expected,
            ArgumentParser::getArguments('foo, bar')
      );
      $this->assertEquals(
            $expected,
            ArgumentParser::getArguments('\'foo\' , \'bar\'')
      );
      $this->assertEquals(
            $expected,
            ArgumentParser::getArguments(' "foo", "bar" ')
      );
      $this->assertEquals(
            $expected,
            ArgumentParser::getArguments('\'foo\', "bar"')
      );

      $expected = ['foo', 'bar baz'];
      $this->assertEquals(
            $expected,
            ArgumentParser::getArguments('foo , bar baz')
      );
      $this->assertEquals(
            $expected,
            ArgumentParser::getArguments(' \'foo\' , "bar baz" ')
      );

      $expected = ['foo', 'a"b"c'];
      $this->assertEquals(
            $expected,
            ArgumentParser::getArguments(' \'foo\' , \'a"b"c\' ')
      );

   }

}
