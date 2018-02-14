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
namespace APF\tests\suites\tools\form\validator;

use APF\core\pagecontroller\Document;
use APF\tools\form\HtmlForm;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\validator\TextFieldValidator;
use APF\tools\form\validator\TextLengthValidator;

/**
 * Tests form validation capabilities for
 *
 * - static form definitions
 * - completely dynamic forms
 * - mixed setup
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.08.2016 (ID#XXX)<br />
 */
class FormValidationTest extends \PHPUnit_Framework_TestCase {

   /**
    * Tests validation of a statically defined form. This ensures that
    * the form is fully created when accessing it within a controller.
    *
    * Use Case: form is not sent.
    */
   public function testStaticFormNotSent() {

      $_GET = [];
      $_POST = [];
      $_REQUEST = [];

      $form = $this->getStaticForm();

      $this->assertFalse($form->isSent());
      $this->assertTrue($form->isValid());

      // test form that is sent but with empty values
      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = [];

      $form = $this->getStaticForm();

      $this->assertTrue($form->isSent());
      $this->assertFalse($form->isValid());

   }

   /**
    * @return HtmlFormTag A static form to be tested.
    */
   private function getStaticForm() {

      $form = new HtmlFormTag();

      // inject parent object to make recursive selection work
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttributes(['method' => HtmlForm::METHOD_POST_VALUE_NAME]);

      $form->setContent('<form:text name="field-1" />
<form:text name="field-2" optional="true" />
<form:text name="field-3" hidden="true"/>
<form:button name="submit" value="submit" />
<form:addvalidator
   class="APF\tools\form\validator\TextLengthValidator"
   control="field-1"
   button="submit"
/>
<form:addvalidator
   class="APF\tools\form\validator\TextLengthValidator"
   control="field-2"
   button="submit"
/>
<form:addvalidator
   class="APF\tools\form\validator\TextLengthValidator"
   control="field-3"
   button="submit"
/>');

      $form->onParseTime();
      $form->onAfterAppend();

      return $form;
   }

   /**
    * Tests validation of a statically defined form. This ensures that
    * the form is fully created when accessing it within a controller.
    *
    * Use Case: test mandatory validation of field-1
    */
   public function testStaticFormMandatoryFieldOne() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = ['field-1' => 'foobar'];

      $form = $this->getStaticForm();

      $this->assertTrue($form->isSent());
      $this->assertTrue($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertTrue($form->getFormElementByName('field-2')->isValid());
      $this->assertTrue($form->getFormElementByName('field-3')->isValid()); // hidden fields are always valid

   }

   /**
    * Tests validation of a statically defined form. This ensures that
    * the form is fully created when accessing it within a controller.
    *
    * Use Case: optional field submitted with valid data
    */
   public function testStaticFormOptionalFieldSubmittedCorrectly() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = [
            'field-1' => 'foobar',
            'field-2' => 'foobar',
            'field-3' => 'foobar'
      ];

      $form = $this->getStaticForm();

      $this->assertTrue($form->isSent());
      $this->assertTrue($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertTrue($form->getFormElementByName('field-2')->isValid());
      $this->assertTrue($form->getFormElementByName('field-3')->isValid()); // hidden fields are always valid

   }

   /**
    * Tests validation of a statically defined form. This ensures that
    * the form is fully created when accessing it within a controller.
    *
    * Use Case: optional field not fulfilling validation requirements
    */
   public function testStaticFormOptionalFieldSubmittedErroneous() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = [
            'field-1' => 'foobar',
            'field-2' => 'f',
            'field-3' => 'foobar'
      ];

      $form = $this->getStaticForm();

      $this->assertTrue($form->isSent());
      $this->assertFalse($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertFalse($form->getFormElementByName('field-2')->isValid());
      $this->assertTrue($form->getFormElementByName('field-3')->isValid()); // hidden fields are always valid
   }

   /**
    * Tests validation of a form composed of purely dynamic elements manipulated within a
    * controller. Ensures that validation mechanisms also apply as with dynamic forms.
    *
    * Use Case: form is not sent
    */
   public function testDynamicFormNotSent() {

      $_GET = [];
      $_POST = [];
      $_REQUEST = [];

      $form = $this->getDynamicForm();

      $this->assertFalse($form->isSent());
      $this->assertTrue($form->isValid());
   }

   /**
    * @param bool $fieldTwoOptional True in case field-2 should be set to hidden, false otherwise.
    *
    * @return HtmlFormTag A form skeleton for dynamic configuration within controllers.
    */
   private function getDynamicForm($fieldTwoOptional = true) {

      $form = new HtmlFormTag();

      // inject parent object to make recursive selection work
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttributes(['method' => HtmlForm::METHOD_POST_VALUE_NAME]);

      $form->setContent('<form:marker name="fields" />');

      $form->onParseTime();
      $form->onAfterAppend();

      $fieldOne = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-1']);
      $fieldTwo = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-2', 'optional' => $fieldTwoOptional ? 'true' : 'false']);
      $fieldThree = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-3', 'hidden' => 'true']);

      $button = $form->addFormElementAfterMarker('fields', 'form:button', ['name' => 'submit', 'value' => 'submit']);

      $validatorOne = new TextLengthValidator($fieldOne, $button);
      $fieldOne->addValidator($validatorOne);
      $validatorTwo = new TextLengthValidator($fieldTwo, $button);
      $fieldTwo->addValidator($validatorTwo);
      $validatorThree = new TextLengthValidator($fieldThree, $button);
      $fieldThree->addValidator($validatorThree);

      return $form;
   }

   /**
    * Tests validation of a form composed of purely dynamic elements manipulated within a
    * controller. Ensures that validation mechanisms also apply as with dynamic forms.
    *
    * Use Case: form sent but with empty values
    */
   public function testDynamicFormSentWithEmptyValues() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = [];

      $form = $this->getDynamicForm();

      $this->assertTrue($form->isSent());
      $this->assertFalse($form->isValid());

   }

   /**
    * Tests validation of a form composed of purely dynamic elements manipulated within a
    * controller. Ensures that validation mechanisms also apply as with dynamic forms.
    *
    * Use Case: mandatory validation of field-1
    */
   public function testDynamicFormMandatoryFieldOne() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = ['field-1' => 'foobar'];

      $form = $this->getDynamicForm();

      $this->assertTrue($form->isSent());
      $this->assertTrue($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertTrue($form->getFormElementByName('field-2')->isValid());
      $this->assertTrue($form->getFormElementByName('field-3')->isValid()); // hidden fields are always valid
   }

   /**
    * Tests validation of a form composed of purely dynamic elements manipulated within a
    * controller. Ensures that validation mechanisms also apply as with dynamic forms.
    *
    * Use Case: mandatory validation of field-1 with field-2 visible
    */
   public function testDynamicFormValidationFieldTwoVisible() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = ['field-1' => 'foobar'];

      $form = $this->getDynamicForm(false);

      $this->assertTrue($form->isSent());
      $this->assertFalse($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertFalse($form->getFormElementByName('field-2')->isValid());
      $this->assertTrue($form->getFormElementByName('field-3')->isValid()); // hidden fields are always valid
   }

   /**
    * Tests validation of a form composed of purely dynamic elements manipulated within a
    * controller. Ensures that validation mechanisms also apply as with dynamic forms.
    *
    * Use Case: mandatory validation of field-1 with field-2 hidden by controller
    */
   public function testDynamicFormFieldTwoHiddenByController() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = ['field-1' => 'foobar'];

      $form = $this->getDynamicForm(false);
      $form->getFormElementByName('field-2')->hide(); // non-optional field gets hidden

      $this->assertTrue($form->isSent());
      $this->assertTrue($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertTrue($form->getFormElementByName('field-2')->isValid());
      $this->assertTrue($form->getFormElementByName('field-3')->isValid()); // hidden fields are always valid

   }

   /**
    * Tests validation of a form composed of purely dynamic elements manipulated within a
    * controller. Ensures that validation mechanisms also apply as with dynamic forms.
    *
    * Use Case: optional field submitted with correct value
    */
   public function testDynamicFormOptionalFieldSubmittedCorrectly() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = [
            'field-1' => 'foobar',
            'field-2' => 'foobar',
            'field-3' => 'foobar'
      ];

      $form = $this->getDynamicForm();

      $this->assertTrue($form->isSent());
      $this->assertTrue($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertTrue($form->getFormElementByName('field-2')->isValid());
      $this->assertTrue($form->getFormElementByName('field-3')->isValid()); // hidden fields are always valid
   }

   /**
    * Tests validation of a form composed of purely dynamic elements manipulated within a
    * controller. Ensures that validation mechanisms also apply as with dynamic forms.
    *
    * Use Case: optional field not fulfilling validation requirements
    */
   public function testDynamicFormOptionalFieldSubmittedErroneous() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = [
            'field-1' => 'foobar',
            'field-2' => 'f',
            'field-3' => 'foobar'
      ];

      $form = $this->getDynamicForm();

      $this->assertTrue($form->isSent());
      $this->assertFalse($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertFalse($form->getFormElementByName('field-2')->isValid());
      $this->assertTrue($form->getFormElementByName('field-3')->isValid()); // hidden fields are always valid
   }

   /**
    * Tests validation of a form composed of static and dynamic elements enhanced/re-configured
    * within a controller. Ensures that validation mechanisms also apply as with semi-dynamic forms.
    *
    * Use Case: form not sent
    */
   public function testSemiDynamicFormNotSent() {

      $_GET = [];
      $_POST = [];
      $_REQUEST = [];

      $form = $this->getSemiDynamicForm();

      $fieldTwo = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-2', 'optional' => 'true']);
      $fieldThree = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-3', 'hidden' => 'true']);

      $button = $form->getFormElementByName('submit');

      $validatorOne = new TextLengthValidator($fieldTwo, $button);
      $fieldTwo->addValidator($validatorOne);
      $validatorTwo = new TextLengthValidator($fieldThree, $button);
      $fieldThree->addValidator($validatorTwo);

      $this->assertFalse($form->isSent());
      $this->assertTrue($form->isValid());
   }

   /**
    * @return HtmlFormTag A form skeleton for dynamic configuration within controllers.
    */
   private function getSemiDynamicForm() {

      $form = new HtmlFormTag();

      // inject parent object to make recursive selection work
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttributes(['method' => HtmlForm::METHOD_POST_VALUE_NAME]);

      $form->setContent('<form:text name="field-1" />
<form:marker name="fields" />
<form:button name="submit" value="submit" />
<form:addvalidator
   class="APF\tools\form\validator\TextLengthValidator"
   control="field-1"
   button="submit"
/>');

      $form->onParseTime();
      $form->onAfterAppend();

      return $form;
   }

   /**
    * Tests validation of a form composed of static and dynamic elements enhanced/re-configured
    * within a controller. Ensures that validation mechanisms also apply as with semi-dynamic forms.
    *
    * Use Case: form sent with empty values
    */
   public function testSemiDynamicFormSentWithEmptyValues() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = [];

      $form = $this->getSemiDynamicForm();

      $fieldTwo = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-2', 'optional' => 'true']);
      $fieldThree = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-3', 'hidden' => 'true']);

      $button = $form->getFormElementByName('submit');

      $validatorOne = new TextLengthValidator($fieldTwo, $button);
      $fieldTwo->addValidator($validatorOne);
      $validatorTwo = new TextLengthValidator($fieldThree, $button);
      $fieldThree->addValidator($validatorTwo);

      $this->assertTrue($form->isSent());
      $this->assertFalse($form->isValid());

   }

   /**
    * Tests validation of a form composed of static and dynamic elements enhanced/re-configured
    * within a controller. Ensures that validation mechanisms also apply as with semi-dynamic forms.
    *
    * Use Case: mandatory validation of field-1
    */
   public function testSemiDynamicFormMandatoryFieldOne() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = ['field-1' => 'foobar'];

      $form = $this->getSemiDynamicForm();

      $fieldTwo = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-2', 'optional' => 'true']);
      $fieldThree = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-3', 'hidden' => 'true']);

      $button = $form->getFormElementByName('submit');

      $validatorOne = new TextLengthValidator($fieldTwo, $button);
      $fieldTwo->addValidator($validatorOne);
      $validatorTwo = new TextLengthValidator($fieldThree, $button);
      $fieldThree->addValidator($validatorTwo);

      $this->assertTrue($form->isSent());
      $this->assertTrue($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertTrue($form->getFormElementByName('field-2')->isValid());
      $this->assertTrue($form->getFormElementByName('field-3')->isValid()); // hidden fields are always valid

   }

   /**
    * Tests validation of a form composed of static and dynamic elements enhanced/re-configured
    * within a controller. Ensures that validation mechanisms also apply as with semi-dynamic forms.
    *
    * Use Case: mandatory validation of field-1 with field-2 hidden by controller
    */
   public function testSemiDynamicFormFieldTwoHiddenByController() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = ['field-1' => 'foobar'];

      $form = $this->getSemiDynamicForm();

      $fieldTwo = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-2']);
      $fieldThree = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-3', 'hidden' => 'true']);

      $button = $form->getFormElementByName('submit');

      $validatorOne = new TextLengthValidator($fieldTwo, $button);
      $fieldTwo->addValidator($validatorOne);
      $validatorTwo = new TextLengthValidator($fieldThree, $button);
      $fieldThree->addValidator($validatorTwo);

      $fieldTwo->hide(); // non-optional field gets hidden

      $this->assertTrue($form->isSent());
      $this->assertTrue($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertTrue($form->getFormElementByName('field-2')->isValid());
      $this->assertTrue($form->getFormElementByName('field-3')->isValid()); // hidden fields are always valid

   }

   /**
    * Tests validation of a form composed of static and dynamic elements enhanced/re-configured
    * within a controller. Ensures that validation mechanisms also apply as with semi-dynamic forms.
    *
    * Use Case: optional field submitted with correct data
    */
   public function testSemiDynamicFormOptionalFieldSubmittedCorrectly() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = [
            'field-1' => 'foobar',
            'field-2' => 'foobar',
            'field-3' => 'foobar'
      ];

      $form = $this->getSemiDynamicForm();

      $fieldTwo = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-2', 'optional' => 'true']);
      $fieldThree = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-3', 'hidden' => 'true']);

      $button = $form->getFormElementByName('submit');

      $validatorOne = new TextLengthValidator($fieldTwo, $button);
      $fieldTwo->addValidator($validatorOne);
      $validatorTwo = new TextLengthValidator($fieldThree, $button);
      $fieldThree->addValidator($validatorTwo);

      $this->assertTrue($form->isSent());
      $this->assertTrue($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertTrue($form->getFormElementByName('field-2')->isValid());
      $this->assertTrue($form->getFormElementByName('field-3')->isValid()); // hidden fields are always valid

   }

   /**
    * Tests validation of a form composed of static and dynamic elements enhanced/re-configured
    * within a controller. Ensures that validation mechanisms also apply as with semi-dynamic forms.
    *
    * Use Case: optional field not fulfilling validation requirements
    */
   public function testSemiDynamicFormOptionalFieldSubmittedErroneous() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = [
            'field-1' => 'foobar',
            'field-2' => 'f',
            'field-3' => 'foobar'
      ];

      $form = $this->getSemiDynamicForm();

      $fieldTwo = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-2', 'optional' => 'true']);
      $fieldThree = $form->addFormElementAfterMarker('fields', 'form:text', ['name' => 'field-3', 'hidden' => 'true']);

      $button = $form->getFormElementByName('submit');

      $validatorOne = new TextLengthValidator($fieldTwo, $button);
      $fieldTwo->addValidator($validatorOne);
      $validatorTwo = new TextLengthValidator($fieldThree, $button);
      $fieldThree->addValidator($validatorTwo);

      $this->assertTrue($form->isSent());
      $this->assertFalse($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertFalse($form->getFormElementByName('field-2')->isValid());
      $this->assertTrue($form->getFormElementByName('field-3')->isValid()); // hidden fields are always valid

   }

   /**
    * Tests whether field set in controller gets not validated after hiding in controller.
    */
   public function testValidationWithFormGroupHiddenInController() {

      $_GET = [];
      $_POST = ['submit' => 'submit'];
      $_REQUEST = ['field-1' => 'foobar'];

      $form = new HtmlFormTag();

      // inject parent object to make recursive selection work
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttributes(['method' => HtmlForm::METHOD_POST_VALUE_NAME]);

      $form->setContent('<form:text name="field-1" />
<form:group name="group-2">
   <form:text name="field-2" />
</form:group>
<form:button name="submit" value="submit" />
<form:addvalidator
   class="APF\tools\form\validator\TextLengthValidator"
   control="field-1"
   button="submit"
/>
<form:addvalidator
   class="APF\tools\form\validator\TextLengthValidator"
   control="field-2"
   button="submit"
/>');

      $form->onParseTime();
      $form->onAfterAppend();

      // in case group group-2 gets hidden in controller form should be valid even if field-2 is not filled
      $form->getFormElementByName('group-2')->hide();

      $this->assertTrue($form->isSent());
      $this->assertTrue($form->isValid());

      $this->assertTrue($form->getFormElementByName('field-1')->isValid());
      $this->assertTrue($form->getFormElementByName('field-2')->isValid()); // hidden field must be valid

   }

   /**
    * ID#318: Expect all validation listeners to be notified in case of invalid form control content.
    */
   public function testListenerNotification() {

      $expectedCssClass = TextFieldValidator::$DEFAULT_MARKER_CLASS;

      // assume form sent with empty values
      $_GET = [];
      $_POST = ['send' => 'GO'];

      $expectedOne = 'Test1';
      $expectedTwo = 'Test2';

      // *** Test form w/ one add validator tag and two form controls ***
      $form = new HtmlFormTag();

      // inject parent object to make recursive selection work
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttributes(['method' => HtmlForm::METHOD_POST_VALUE_NAME]);

      $form->setContent('<form:text name="test1"/>
<form:listener control="test1" id="listener1">' . $expectedOne . '</form:listener>
<form:text name="test2"/>
<form:listener control="test2" id="listener2">' . $expectedTwo . '</form:listener>
<form:button name="send" value="GO"/>
<form:addvalidator 
   class="APF\tools\form\validator\TextLengthValidator" 
   button="send" 
   control="test1|test2"
/>');

      $form->onParseTime();
      $form->onAfterAppend();

      $this->assertTrue($form->isSent());
      $this->assertFalse($form->isValid());

      $listenerOne = $form->getFormElementByID('listener1');
      $listenerTwo = $form->getFormElementByID('listener2');

      // Ensure both validation listeners are displaying
      $this->assertEquals($expectedOne, $listenerOne->transform(), 'Validation listener one not displayed!');
      $this->assertEquals($expectedTwo, $listenerTwo->transform(), 'Validation listener two not displayed!');

      // Ensure both fields are marked w/ error CSS class
      $fieldOne = $form->getFormElementByName('test1')->getAttribute('class');
      $this->assertEquals($expectedCssClass, $fieldOne, 'No error marker CSS class for field one!');
      $fieldTwo = $form->getFormElementByName('test2')->getAttribute('class');
      $this->assertEquals($expectedCssClass, $fieldTwo, 'No error marker CSS class for field two!');


      // *** Test form w/ two add validator tags and one form control each ***
      $form = new HtmlFormTag();

      // inject parent object to make recursive selection work
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttributes(['method' => HtmlForm::METHOD_POST_VALUE_NAME]);

      $form->setContent('<form:text name="test1"/>
<form:listener control="test1" id="listener1">' . $expectedOne . '</form:listener>
<form:text name="test2"/>
<form:listener control="test2" id="listener2">' . $expectedTwo . '</form:listener>
<form:button name="send" value="GO"/>
<form:addvalidator 
   class="APF\tools\form\validator\TextLengthValidator" 
   button="send" 
   control="test1"
/>
<form:addvalidator 
   class="APF\tools\form\validator\TextLengthValidator" 
   button="send" 
   control="test2"
/>');

      $form->onParseTime();
      $form->onAfterAppend();

      $this->assertTrue($form->isSent());
      $this->assertFalse($form->isValid());

      $listenerOne = $form->getFormElementByID('listener1');
      $listenerTwo = $form->getFormElementByID('listener2');

      // Ensure both validation listeners are displaying
      $this->assertEquals($expectedOne, $listenerOne->transform(), 'Validation listener one not displayed!');
      $this->assertEquals($expectedTwo, $listenerTwo->transform(), 'Validation listener two not displayed!');

      // Ensure both fields are marked w/ error CSS class
      $fieldOne = $form->getFormElementByName('test1')->getAttribute('class');
      $this->assertEquals($expectedCssClass, $fieldOne, 'No error marker CSS class for field one!');
      $fieldTwo = $form->getFormElementByName('test2')->getAttribute('class');
      $this->assertEquals($expectedCssClass, $fieldTwo, 'No error marker CSS class for field two!');

   }

}
