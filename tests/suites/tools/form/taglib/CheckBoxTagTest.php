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

use APF\tools\form\HtmlForm;
use APF\tools\form\taglib\CheckBoxTag;
use APF\tools\form\taglib\HtmlFormTag;

class CheckBoxTagTest extends \PHPUnit_Framework_TestCase {

   const BUTTON_NAME = 'send';
   const BUTTON_VALUE = 'Send';

   /**
    * @return HtmlFormTag
    */
   protected function &getForm() {

      $form = new HtmlFormTag();
      $form->setAttribute('name', 'test-form');
      $form->setAttribute(HtmlForm::METHOD_ATTRIBUTE_NAME, HtmlForm::METHOD_POST_VALUE_NAME);

      $form->addFormElement('form:button', array('name' => self::BUTTON_NAME, 'value' => self::BUTTON_VALUE));

      return $form;
   }

   public function testInitialFormLoad() {

      $checkBox = new CheckBoxTag();
      $checkBox->setAttribute('name', 'foo');
      $checkBox->setAttribute('value', 'bar');

      $checkBox->setParentObject($this->getForm());

      $checkBox->onParseTime();
      $checkBox->onAfterAppend();

      assertFalse($checkBox->isChecked());

   }

   public function testFormSubmitUnChecked() {

      $_POST = array();
      $_POST[self::BUTTON_NAME] = self::BUTTON_VALUE;

      $checkBox = new CheckBoxTag();
      $checkBox->setAttribute('name', 'foo');
      $checkBox->setAttribute('value', 'bar');

      $checkBox->setParentObject($this->getForm());

      $checkBox->onParseTime();
      $checkBox->onAfterAppend();

      assertFalse($checkBox->isChecked());

   }

   public function testFormSubmitChecked() {

      $_POST = array();
      $_POST[self::BUTTON_NAME] = self::BUTTON_VALUE;

      $_REQUEST = array();
      $checkBoxName = 'foo';
      $checkBoxValue = 'bar';
      $_REQUEST[$checkBoxName] = $checkBoxValue;

      $checkBox = new CheckBoxTag();
      $checkBox->setAttribute('name', $checkBoxName);
      $checkBox->setAttribute('value', $checkBoxValue);

      $checkBox->setParentObject($this->getForm());

      $checkBox->onParseTime();
      $checkBox->onAfterAppend();

      assertTrue($checkBox->isChecked());

   }

}
