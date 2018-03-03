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
use APF\tools\form\taglib\TextAreaTag;
use PHPUnit\Framework\TestCase;

class TextAreaTagTest extends TestCase {

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

   /**
    * @throws ParserException
    * @throws FormException
    */
   public function testControllerPresetting() {

      $_REQUEST = [];

      $area = $this->getTextField();

      $area->onParseTime();
      $area->onAfterAppend();

      $area->setContent(self::FIELD_VALUE);

      $this->assertEquals($area->getContent(), self::FIELD_VALUE);
      $this->assertTrue($area->isFilled());

   }

   /**
    * @throws FormException
    * @throws ParserException
    */
   public function testSubmitPresetting() {

      $_REQUEST = [];

      $userInput = 'This is a user input...';
      $_REQUEST[self::FIELD_NAME] = $userInput;

      $area = $this->getTextField();
      $area->setContent(self::FIELD_VALUE);

      $area->onParseTime();
      $area->onAfterAppend();

      $this->assertEquals($area->getContent(), $userInput);
      $this->assertTrue($area->isFilled());

      // re-test with "0" as input in URL
      $userInput = '0';
      $_REQUEST[self::FIELD_NAME] = $userInput;

      $area->onParseTime();
      $area->onAfterAppend();

      $this->assertEquals($area->getContent(), $userInput);
      $this->assertTrue($area->isFilled());

   }

}
