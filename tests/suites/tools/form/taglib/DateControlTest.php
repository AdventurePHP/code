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

use APF\tools\form\taglib\DateSelectorTag;

/**
 * @package APF\tests\suites\tools\form\taglib
 * @class DateControlTest
 *
 * Implements tests for the DateSelectorTag control.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.07.2012<br />
 */
class DateControlTest extends \PHPUnit_Framework_TestCase {

   public function testPrependEmptyOptions() {

      $tag = new DateSelectorTag();
      $tag->setAttribute('name', 'date1');
      $tag->setAttribute('yearrange', '2002-2012');
      $tag->setAttribute('prepend-empty-options', 'true');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $result = '<span id="date1"><select name="date1[Day]" id="date1[Day]"><option value="" selected="selected"></option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select><select name="date1[Month]" id="date1[Month]"><option value="" selected="selected"></option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option></select><select name="date1[Year]" id="date1[Year]"><option value="" selected="selected"></option><option value="2002">2002</option><option value="2003">2003</option><option value="2004">2004</option><option value="2005">2005</option><option value="2006">2006</option><option value="2007">2007</option><option value="2008">2008</option><option value="2009">2009</option><option value="2010">2010</option><option value="2011">2011</option><option value="2012">2012</option></select></span>';
      assertEquals($result, $tag->transform());
   }

   public function testSimplePresetting() {
      $tag = new DateSelectorTag();
      $tag->setAttribute('name', 'date1');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $today = new \DateTime();
      assertEquals($today->format('Y-m-d'), $tag->getDate());
   }

   public function testPresettingWithPrependEmptyOptions() {
      $tag = new DateSelectorTag();
      $tag->setAttribute('name', 'date1');
      $tag->setAttribute('prepend-empty-options', 'true');

      $tag->onParseTime();
      $tag->onAfterAppend();

      assertEquals(null, $tag->getDate());
   }

   public function testPresettingWithPrependEmptyOptionsAfterPost() {
      $tag = new DateSelectorTag();
      $tag->setAttribute('name', 'date1');
      $tag->setAttribute('prepend-empty-options', 'true');

      unset($_REQUEST['date1']);
      $_REQUEST['date1']['Day'] = '';
      $_REQUEST['date1']['Month'] = '';
      $_REQUEST['date1']['Year'] = '';

      $tag->onParseTime();
      $tag->onAfterAppend();

      assertEquals(null, $tag->getDate());
   }

   public function testGetDateWithCorrectDate() {
      $tag = new DateSelectorTag();
      $tag->setAttribute('name', 'date1');
      $tag->setAttribute('prepend-empty-options', 'true');

      unset($_REQUEST['date1']);
      $day = '01';
      $month = '01';
      $year = '2012';
      $_REQUEST['date1']['Day'] = $day;
      $_REQUEST['date1']['Month'] = $month;
      $_REQUEST['date1']['Year'] = $year;

      $tag->onParseTime();
      $tag->onAfterAppend();

      $expected = \DateTime::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $day);
      assertEquals($expected->format('Y-m-d'), $tag->getDate());
   }

   public function testGetDateWithImplausibleDate() {
      $tag = new DateSelectorTag();
      $tag->setAttribute('name', 'date1');
      $tag->setAttribute('prepend-empty-options', 'true');

      unset($_REQUEST['date1']);
      $_REQUEST['date1']['Day'] = '31';
      $_REQUEST['date1']['Month'] = '02';
      $_REQUEST['date1']['Year'] = '2012';

      $tag->onParseTime();
      $tag->onAfterAppend();

      $expected = \DateTime::createFromFormat('Y-m-d', '2012-02-31');
      assertEquals($expected->format('Y-m-d'), $tag->getDate());
   }

   public function testPresettingWithImplausibleDate() {
      $tag = new DateSelectorTag();
      $tag->setAttribute('name', 'date1');
      $tag->setAttribute('prepend-empty-options', 'true');

      unset($_REQUEST['date1']);
      $_REQUEST['date1']['Day'] = '31';
      $_REQUEST['date1']['Month'] = '02';
      $_REQUEST['date1']['Year'] = '2012';

      $tag->onParseTime();
      $tag->onAfterAppend();

      $result = $tag->transform();

      assertTrue(preg_match('/<option value="02" selected="selected">02<\/option>/', $result) === 1);
      assertTrue(preg_match('/<option value="03" selected="selected">03<\/option>/', $result) === 1);
      assertTrue(preg_match('/<option value="2012" selected="selected">2012<\/option>/', $result) === 1);
   }

   public function testPresettingWithImplausibleDateAndPrependEmptyOptions() {
      $tag = new DateSelectorTag();
      $tag->setAttribute('name', 'date1');
      $tag->setAttribute('prepend-empty-options', 'true');

      unset($_REQUEST['date1']);

      $tag->onParseTime();
      $tag->onAfterAppend();

      $result = $tag->transform();

      assertTrue(preg_match('/<option value="" selected="selected"><\/option>/', $result) === 1);
      assertTrue(preg_match('/<option value="" selected="selected"><\/option>/', $result) === 1);
      assertTrue(preg_match('/<option value="" selected="selected"><\/option>/', $result) === 1);
   }

}
