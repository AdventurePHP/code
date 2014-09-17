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

use APF\tools\form\taglib\TextAreaTag;

class TextAreaTagTest extends \PHPUnit_Framework_TestCase {

   const FIELD_NAME = 'foo';
   const FIELD_VALUE = 'bar';

   /**
    * @return TextAreaTag
    */
   protected function getTextField() {
      $area = new TextAreaTag();
      $area->setAttribute('name', self::FIELD_NAME);

      return $area;
   }

   public function testControllerPresetting() {

      $_REQUEST = array();

      $area = $this->getTextField();

      $area->onParseTime();
      $area->onAfterAppend();

      $area->setContent(self::FIELD_VALUE);

      assertEquals($area->getContent(), self::FIELD_VALUE);
      assertTrue($area->isFilled());

   }

   public function testSubmitPresetting() {

      $_REQUEST = array();

      $userInput = 'This is a user input...';
      $_REQUEST[self::FIELD_NAME] = $userInput;

      $area = $this->getTextField();
      $area->setContent(self::FIELD_VALUE);

      $area->onParseTime();
      $area->onAfterAppend();

      assertEquals($area->getContent(), $userInput);
      assertTrue($area->isFilled());

      // re-test with "0" as input in URL
      $userInput = '0';
      $_REQUEST[self::FIELD_NAME] = $userInput;

      $area->onParseTime();
      $area->onAfterAppend();

      assertEquals($area->getContent(), $userInput);
      assertTrue($area->isFilled());

   }

}
