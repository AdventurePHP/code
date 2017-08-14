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
namespace APF\tests\suites\tools\form\taglib;

use APF\tools\form\taglib\TimeSelectorTag;
use DateTime;

/**
 * Implements tests for the DateSelectorTag control.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.07.2014<br />
 */
class TimeControlTest extends \PHPUnit_Framework_TestCase {

   public function testSimplePresetting() {
      $tag = new TimeSelectorTag();
      $tag->setAttribute('name', 'time1');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $today = new DateTime();
      $current = $tag->getTime();

      $this->assertInstanceOf(DateTime::class, $current);
      $pattern = 'Y-m-d\TH:i:s';
      $this->assertEquals($today->format($pattern), $current->format($pattern));
   }

   public function testGetTimeWithCorrectTime() {

      unset($_REQUEST['time1']);
      $hour = '8';
      $minute = '13';
      $second = '30';
      $_REQUEST['time1']['Hours'] = $hour;
      $_REQUEST['time1']['Minutes'] = $minute;
      $_REQUEST['time1']['Seconds'] = $second;

      $tag = new TimeSelectorTag();
      $tag->setAttribute('name', 'time1');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $expected = DateTime::createFromFormat('H:i:s', $hour . ':' . $minute . ':' . $second);
      $this->assertEquals($expected, $tag->getTime());

      // same test again for the showSeconds=false case
      unset($_REQUEST['time1']['Seconds']);

      $tag = new TimeSelectorTag();
      $tag->setAttribute('name', 'time1');
      $tag->setAttribute('showseconds', 'false');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $expected = DateTime::createFromFormat('H:i', $hour . ':' . $minute);
      $this->assertEquals($expected, $tag->getTime());
   }

   public function testGetDateWithImplausibleTime() {
      $tag = new TimeSelectorTag();
      $tag->setAttribute('name', 'time1');

      unset($_REQUEST['time1']);
      $_REQUEST['time1']['Hours'] = '25';
      $_REQUEST['time1']['Minutes'] = '2';
      $_REQUEST['time1']['Seconds'] = '3';

      $tag->onParseTime();
      $tag->onAfterAppend();

      $this->assertEquals(null, $tag->getTime());
      $this->assertEquals(null, $tag->getTime());
   }

   public function testPresettingWithImplausibleDate() {
      $tag = new TimeSelectorTag();
      $tag->setAttribute('name', 'time1');

      unset($_REQUEST['time1']);
      $_REQUEST['time1']['Hours'] = '25';
      $_REQUEST['time1']['Minutes'] = '2';
      $_REQUEST['time1']['Seconds'] = '3';

      $tag->onParseTime();
      $tag->onAfterAppend();

      $result = $tag->transform();

      $this->assertTrue(preg_match('/<option value="02" selected="selected">02<\/option>/', $result) === 1);
      $this->assertTrue(preg_match('/<option value="03" selected="selected">03<\/option>/', $result) === 1);
   }

   public function testSetTime() {

      // set time with full format
      $tag = new TimeSelectorTag();
      $tag->setAttribute('name', 'time1');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $time = '23:02:29';
      $tag->setTime($time);

      $expected = DateTime::createFromFormat('H:i:s', $time);
      $this->assertEquals($expected, $tag->getTime());

      // set time without seconds
      $tag = new TimeSelectorTag();
      $tag->setAttribute('name', 'time1');
      $tag->setAttribute('showseconds', 'false');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $time = '23:02';
      $tag->setTime($time);

      $expected = DateTime::createFromFormat('H:i', $time);
      $this->assertEquals($expected, $tag->getTime());
   }

}
