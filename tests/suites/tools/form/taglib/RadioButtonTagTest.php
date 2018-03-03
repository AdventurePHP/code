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
use APF\tools\form\HtmlForm;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\taglib\RadioButtonTag;
use PHPUnit\Framework\TestCase;

class RadioButtonTagTest extends TestCase {

   const BUTTON_NAME = 'send';
   const BUTTON_VALUE = 'Send';

   /**
    * @return HtmlFormTag
    * @throws FormException
    */
   protected function &getForm() {

      $form = new HtmlFormTag();
      $form->setAttribute('name', 'test-form');
      $form->setAttribute(HtmlForm::METHOD_ATTRIBUTE_NAME, HtmlForm::METHOD_POST_VALUE_NAME);

      $form->addFormElement('form:button', ['name' => self::BUTTON_NAME, 'value' => self::BUTTON_VALUE]);

      return $form;
   }

   /**
    * @param string $id
    * @param string $name
    * @param string $value
    *
    * @return RadioButtonTag
    */
   protected function getRadioButton($id, $name, $value) {
      $button = new RadioButtonTag();
      $button->setAttribute('id', $id);
      $button->setAttribute('name', $name);
      $button->setAttribute('value', $value);

      return $button;
   }

   /**
    * @throws FormException
    * @throws ParserException
    */
   public function testInitialFormLoad() {

      $buttonOne = $this->getRadioButton('delete-yes', 'delete', '1');
      $buttonOne->setParentObject($this->getForm());
      $buttonOne->onParseTime();
      $buttonOne->onAfterAppend();

      $buttonTwo = $this->getRadioButton('delete-no', 'delete', '0');
      $buttonTwo->setParentObject($this->getForm());
      $buttonTwo->onParseTime();
      $buttonTwo->onAfterAppend();

      $this->assertFalse($buttonOne->isChecked());
      $this->assertFalse($buttonTwo->isChecked());

   }

   /**
    * @throws FormException
    * @throws ParserException
    */
   public function testFormSubmitUnChecked() {

      $_POST = [];
      $_POST[self::BUTTON_NAME] = self::BUTTON_VALUE;

      $buttonOne = $this->getRadioButton('delete-yes', 'delete', '1');
      $buttonOne->setParentObject($this->getForm());
      $buttonOne->onParseTime();
      $buttonOne->onAfterAppend();

      $buttonTwo = $this->getRadioButton('delete-no', 'delete', '0');
      $buttonTwo->setParentObject($this->getForm());
      $buttonTwo->onParseTime();
      $buttonTwo->onAfterAppend();

      $this->assertFalse($buttonOne->isChecked());
      $this->assertFalse($buttonTwo->isChecked());

   }

   /**
    * @throws FormException
    * @throws ParserException
    */
   public function testFormSubmitOneChecked() {

      $_POST = [];
      $_POST[self::BUTTON_NAME] = self::BUTTON_VALUE;

      $_REQUEST = [];
      $radioName = 'delete';
      $_REQUEST[$radioName] = '1';

      $buttonOne = $this->getRadioButton('delete-yes', 'delete', '1');
      $buttonOne->setParentObject($this->getForm());
      $buttonOne->onParseTime();
      $buttonOne->onAfterAppend();

      $buttonTwo = $this->getRadioButton('delete-no', 'delete', '0');
      $buttonTwo->setParentObject($this->getForm());
      $buttonTwo->onParseTime();
      $buttonTwo->onAfterAppend();

      $this->assertTrue($buttonOne->isChecked());
      $this->assertFalse($buttonTwo->isChecked());

   }

   /**
    * @throws FormException
    * @throws ParserException
    */
   public function testFormSubmitTwoChecked() {

      $_POST = [];
      $_POST[self::BUTTON_NAME] = self::BUTTON_VALUE;

      $_REQUEST = [];
      $radioName = 'delete';
      $_REQUEST[$radioName] = '0';

      $buttonOne = $this->getRadioButton('delete-yes', 'delete', '1');
      $buttonOne->setParentObject($this->getForm());
      $buttonOne->onParseTime();
      $buttonOne->onAfterAppend();

      $buttonTwo = $this->getRadioButton('delete-no', 'delete', '0');
      $buttonTwo->setParentObject($this->getForm());
      $buttonTwo->onParseTime();
      $buttonTwo->onAfterAppend();


      $this->assertFalse($buttonOne->isChecked());
      $this->assertTrue($buttonTwo->isChecked());

   }

}
