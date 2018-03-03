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
namespace APF\tests\suites\tools\form\mock;

use APF\core\pagecontroller\ParserException;
use APF\tools\form\FormException;
use APF\tools\form\taglib\TextFieldTag;

/**
 * Mock class that allows check whether onParseTime() and onAfterAppend() have been executed.
 */
class TextFieldTagMock extends TextFieldTag {

   public $onParseTimeExecuted = false;
   public $onAfterAppendExecuted = false;

   /**
    * @throws FormException
    */
   public function onParseTime() {
      parent::onParseTime();
      $this->onParseTimeExecuted = true;
   }

   /**
    * @throws ParserException
    */
   public function onAfterAppend() {
      parent::onAfterAppend();
      $this->onAfterAppendExecuted = true;
   }

}
