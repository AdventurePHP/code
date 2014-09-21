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

use APF\tools\form\taglib\SelectBoxTag;

class SelectBoxTagTest extends \PHPUnit_Framework_TestCase {

   const SELECT_BOX_NAME = 'foo';
   const COMPLEX_SELECT_BOX_NAME = 'foo[bar]';

   protected function getSelectBox($name = self::SELECT_BOX_NAME) {
      $select = new SelectBoxTag();
      $select->setAttribute('name', $name);

      return $select;
   }

   public function testControllerPresetting() {

      $_REQUEST = array();

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addOption($number, $number);
      }

      assertNull($select->getSelectedOption());

      $selectedOption = 4;
      $select->setOption2Selected($selectedOption);
      assertEquals($select->getSelectedOption()->getValue(), $selectedOption);

   }

   public function testSubmitPresetting() {

      $selectedOption = 4;

      $_REQUEST = array();
      $_REQUEST[self::SELECT_BOX_NAME] = $selectedOption;

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addOption($number, $number);
      }

      assertEquals($select->getSelectedOption()->getValue(), $selectedOption);

      // re-test with complex name
      $_REQUEST = array();
      $_REQUEST[self::SELECT_BOX_NAME]['bar'] = $selectedOption;

      $select = $this->getSelectBox(self::COMPLEX_SELECT_BOX_NAME);

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addOption($number, $number);
      }

      assertEquals($select->getSelectedOption()->getValue(), $selectedOption);

   }

   public function testControllerPresettingWithGroups() {

      $_REQUEST = array();

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addGroupOption('number-' . $number % 2, $number, $number);
      }

      assertNull($select->getSelectedOption());

      $selectedOption = 4;
      $select->setOption2Selected($selectedOption);
      assertEquals($select->getSelectedOption()->getValue(), $selectedOption);

   }

   public function testSubmitPresettingWithGroups() {

      $selectedOption = 4;

      $_REQUEST = array();
      $_REQUEST[self::SELECT_BOX_NAME] = $selectedOption;

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addGroupOption('number-' . $number % 2, $number, $number);
      }

      assertEquals($select->getSelectedOption()->getValue(), $selectedOption);

      // re-test with complex name
      $_REQUEST = array();
      $_REQUEST[self::SELECT_BOX_NAME]['bar'] = $selectedOption;

      $select = $this->getSelectBox(self::COMPLEX_SELECT_BOX_NAME);

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addGroupOption('number-' . $number % 2, $number, $number);
      }

      assertEquals($select->getSelectedOption()->getValue(), $selectedOption);

   }

}
