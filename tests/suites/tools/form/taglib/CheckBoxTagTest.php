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
use APF\tools\form\taglib\CheckBoxTag;
use APF\tools\form\taglib\HtmlFormTag;
use PHPUnit\Framework\TestCase;

class CheckBoxTagTest extends TestCase {

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
    * @throws FormException
    */
   public function testInitialFormLoad() {

      $checkBox = new CheckBoxTag();
      $checkBox->setAttribute('name', 'foo');
      $checkBox->setAttribute('value', 'bar');

      $checkBox->setParentObject($this->getForm());

      $checkBox->onParseTime();
      $checkBox->onAfterAppend();

      $this->assertFalse($checkBox->isChecked());

   }

   /**
    * @throws FormException
    */
   public function testFormSubmitUnChecked() {

      $_POST = [];
      $_POST[self::BUTTON_NAME] = self::BUTTON_VALUE;

      $checkBox = new CheckBoxTag();
      $checkBox->setAttribute('name', 'foo');
      $checkBox->setAttribute('value', 'bar');

      $checkBox->setParentObject($this->getForm());

      $checkBox->onParseTime();
      $checkBox->onAfterAppend();

      $this->assertFalse($checkBox->isChecked());

   }

   /**
    * @throws FormException
    */
   public function testFormSubmitChecked() {

      $_POST = [];
      $_POST[self::BUTTON_NAME] = self::BUTTON_VALUE;

      $_REQUEST = [];
      $checkBoxName = 'foo';
      $checkBoxValue = 'bar';
      $_REQUEST[$checkBoxName] = $checkBoxValue;

      $checkBox = new CheckBoxTag();
      $checkBox->setAttribute('name', $checkBoxName);
      $checkBox->setAttribute('value', $checkBoxValue);

      $checkBox->setParentObject($this->getForm());

      $checkBox->onParseTime();
      $checkBox->onAfterAppend();

      $this->assertTrue($checkBox->isChecked());

   }

   /**
    * @param string $name Name of the check box.
    * @param string $value Value of the check box.
    * @param bool $checked Whether to att checked="checked" attribute.
    * @return HtmlFormTag The generated form tag.
    * @throws ParserException
    */
   protected function &getFormWithCheckBox($name, $value, $checked) {

      $form = new HtmlFormTag();
      $form->setAttribute('name', 'test-form');
      $form->setAttribute(HtmlForm::METHOD_ATTRIBUTE_NAME, HtmlForm::METHOD_POST_VALUE_NAME);

      $checkedAttribute = $checked ? ' checked="checked"' : '';
      $form->setContent('<form:checkbox name="' . $name . '" value="' . $value . '"' . $checkedAttribute . ' />
<form:button name="' . self::BUTTON_NAME . '" value="' . self::BUTTON_VALUE . '" />');

      $form->onParseTime();
      $form->onAfterAppend();

      return $form;
   }

   /**
    * Tests checking check boxes for all combinations and including form definition in template.
    * @throws ParserException
    * @throws FormException
    */
   public function testStaticChecking() {

      $checkBoxName = 'foo';
      $checkBoxValue = 'bar';

      // checked with form definition, form not sent
      $_POST = [];
      $_REQUEST = [];

      $form = $this->getFormWithCheckBox($checkBoxName, $checkBoxValue, true);
      $this->assertTrue($form->getFormElementByName($checkBoxName)->isChecked());

      // not checked with form definition, form not sent
      $_POST = [];
      $_REQUEST = [];

      $form = $this->getFormWithCheckBox($checkBoxName, $checkBoxValue, false);
      $this->assertFalse($form->getFormElementByName($checkBoxName)->isChecked());

      // not checked with form definition, form sent, checkbox present
      $_POST = [];
      $_POST[self::BUTTON_NAME] = self::BUTTON_VALUE;

      $_REQUEST[$checkBoxName] = $checkBoxValue;

      $form = $this->getFormWithCheckBox($checkBoxName, $checkBoxValue, false);
      $this->assertTrue($form->getFormElementByName($checkBoxName)->isChecked());

      // not checked with form definition, form sent, checkbox not present
      $_REQUEST = [];

      $form = $this->getFormWithCheckBox($checkBoxName, $checkBoxValue, false);
      $this->assertFalse($form->getFormElementByName($checkBoxName)->isChecked());

      // checked with form definition, form sent and checkbox not present
      $form = $this->getFormWithCheckBox($checkBoxName, $checkBoxValue, true);
      $this->assertFalse($form->getFormElementByName($checkBoxName)->isChecked());

      // checked with form definition, form sent and checkbox present
      $_REQUEST[$checkBoxName] = $checkBoxValue;

      $form = $this->getFormWithCheckBox($checkBoxName, $checkBoxValue, true);
      $this->assertTrue($form->getFormElementByName($checkBoxName)->isChecked());

   }

}
