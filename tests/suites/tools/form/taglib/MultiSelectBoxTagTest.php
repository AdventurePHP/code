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
use APF\tools\form\taglib\SelectBoxGroupTag;
use APF\tools\form\taglib\SelectBoxOptionTag;

class MultiSelectBoxTagTest extends \PHPUnit_Framework_TestCase {

   const SELECT_BOX_NAME = 'foo';

   public function testControllerPresetting() {

      $_REQUEST = [];

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addOption($number, $number);
      }

      $this->assertEmpty($select->getSelectedOptions());

      $select->setOption2Selected(2);
      $select->setOption2Selected(4);

      $this->assertEquals($select->getSelectedOptions()[0]->getValue(), 2);
      $this->assertEquals($select->getSelectedOptions()[1]->getValue(), 4);

   }

   protected function getSelectBox() {
      $select = new MultiSelectBoxTag();
      $select->setAttribute('name', self::SELECT_BOX_NAME);

      return $select;
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

      $this->assertEquals($select->getSelectedOptions()[0]->getValue(), 2);
      $this->assertEquals($select->getSelectedOptions()[1]->getValue(), 4);

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

      $this->assertEmpty($select->getSelectedOptions());

      $select->setOption2Selected(2);
      $select->setOption2Selected(4);

      $this->assertEquals($select->getSelectedOptions()[0]->getValue(), 2);
      $this->assertEquals($select->getSelectedOptions()[1]->getValue(), 4);

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

      $this->assertEquals($select->getSelectedOptions()[0]->getValue(), 2);
      $this->assertEquals($select->getSelectedOptions()[1]->getValue(), 4);

   }

   /**
    * Tests transformation of a complex multi-select box.
    */
   public function testTransformation() {

      $select = $this->getSelectBox();

      $group = new SelectBoxGroupTag();
      $group->setAttribute('label', 'foo');

      $option1 = new SelectBoxOptionTag();
      $option1->setAttribute('value', '1');
      $option1->setContent('1');
      $option1->setAttribute('selected', 'selected');

      $option2 = new SelectBoxOptionTag();
      $option2->setAttribute('value', '2');
      $option2->setContent('2');

      $option3 = new SelectBoxOptionTag();
      $option3->setAttribute('value', '3');
      $option3->setContent('3');
      $option3->setAttribute('selected', 'selected');

      $option4 = new SelectBoxOptionTag();
      $option4->setAttribute('value', '4');
      $option4->setContent('4');
      $option4->setAttribute('selected', 'selected');

      $group->addOptionTag($option1);
      $group->addOptionTag($option2);
      $group->addOptionTag($option3);

      $select->addGroupTag($group);

      $select->addOptionTag($option4);

      $actual = $select->transform();

      // general structure
      $this->assertContains('<select multiple="multiple" name="' . self::SELECT_BOX_NAME . '[]"', $actual);
      $this->assertContains('</select>', $actual);

      $this->assertContains('<optgroup label="foo">', $actual);
      $this->assertContains('</optgroup>', $actual);

      // selected items
      $this->assertContains('<option value="1" selected="selected">1</option>', $actual);
      $this->assertContains('<option value="2"', $actual);
      $this->assertContains('<option value="3" selected="selected">3</option>', $actual);
      $this->assertContains('<option value="4" selected="selected">4</option>', $actual);

   }

}
