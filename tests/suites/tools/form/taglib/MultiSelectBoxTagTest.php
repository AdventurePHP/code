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

use APF\tools\form\taglib\MultiSelectBoxTag;

class MultiSelectBoxTagTest extends \PHPUnit_Framework_TestCase {

   const SELECT_BOX_NAME = 'foo';

   protected function getSelectBox() {
      $select = new MultiSelectBoxTag();
      $select->setAttribute('name', self::SELECT_BOX_NAME);

      return $select;
   }

   public function testControllerPresetting() {

      $_REQUEST = [];

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addOption($number, $number);
      }

      assertEmpty($select->getSelectedOptions());

      $select->setOption2Selected(2);
      $select->setOption2Selected(4);

      assertEquals($select->getSelectedOptions()[0]->getValue(), 2);
      assertEquals($select->getSelectedOptions()[1]->getValue(), 4);

   }

   public function testSubmitPresetting() {

      $_REQUEST = [];
      $_REQUEST[self::SELECT_BOX_NAME][] = 2;
      $_REQUEST[self::SELECT_BOX_NAME][] = 4;

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addOption($number, $number);
      }

      assertEquals($select->getSelectedOptions()[0]->getValue(), 2);
      assertEquals($select->getSelectedOptions()[1]->getValue(), 4);

   }

   public function testControllerPresettingWithGroups() {

      $_REQUEST = [];

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addGroupOption('number-' . $number % 2, $number, $number);
      }

      assertEmpty($select->getSelectedOptions());

      $select->setOption2Selected(2);
      $select->setOption2Selected(4);

      assertEquals($select->getSelectedOptions()[0]->getValue(), 2);
      assertEquals($select->getSelectedOptions()[1]->getValue(), 4);

   }


   public function testSubmitPresettingWithGroups() {

      $_REQUEST = [];
      $_REQUEST[self::SELECT_BOX_NAME][] = 2;
      $_REQUEST[self::SELECT_BOX_NAME][] = 4;

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addGroupOption('number-' . $number % 2, $number, $number);
      }

      assertEquals($select->getSelectedOptions()[0]->getValue(), 2);
      assertEquals($select->getSelectedOptions()[1]->getValue(), 4);

   }

}
