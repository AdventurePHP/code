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

use APF\core\pagecontroller\Document;
use APF\tests\suites\tools\form\model\FormValuesModel;
use APF\tools\form\mapping\StandardModelToFormControlMapper;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\taglib\MultiSelectBoxTag;
use APF\tools\form\taglib\SelectBoxOptionTag;
use APF\tools\form\taglib\SelectBoxTag;
use DateTime;
use ReflectionProperty;

/**
 * Tests the model form mapping capabilities.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.06.2016 (ID#297: introduced model to form mappings)<br />
 */
class ModelToFormControlMappingTest extends \PHPUnit_Framework_TestCase {

   const BUTTON_NAME = 'button';
   const BUTTON_VALUE = 'Send';

   /**
    * Test mapping with empty form does not fail.
    */
   public function testEmptyForm() {

      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');

      $model = new FormValuesModel();
      $form->fillForm($model);

   }

   /**
    * Tests adding and clearing of model-to-form mapping configurations.
    */
   public function testMappingConfiguration() {

      $property = new ReflectionProperty(HtmlFormTag::class, 'modelToFormMappers');
      $property->setAccessible(true);

      $original = $property->getValue();
      $this->assertCount(5, $original);

      HtmlFormTag::clearModelToFormControlMapper();
      $actual = $property->getValue();
      $this->assertCount(0, $actual);

      HtmlFormTag::addModelToFormControlMapper(StandardModelToFormControlMapper::class);
      $actual = $property->getValue();
      $this->assertCount(1, $actual);

      $property->setValue(null, $original);
   }

   /**
    * Tests empty fields in model.
    */
   public function testExistingFields1() {

      $form = $this->getSimpleForm();

      $model = new FormValuesModel();

      $form->fillForm($model);

      $this->assertEmpty($form->getFormElementByName('foo')->getValue());
      $this->assertEmpty($form->getFormElementByName('bar')->getValue());
      $this->assertEmpty($form->getFormElementByName('baz')->getValue());
   }

   /**
    * @return HtmlFormTag
    */
   private function getSimpleForm() {
      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');
      $form->setContent('<form:text name="foo" /><form:text name="bar" /><form:text name="baz" />');
      $form->onParseTime();
      $form->onAfterAppend();

      return $form;
   }

   /**
    * Test filled fields in model.
    */
   public function testExistingFields2() {

      $form = $this->getSimpleForm();

      $model = new FormValuesModel();
      $model->setFoo('foo');
      $model->setBar('bar');
      $model->setBaz('baz');

      $form->fillForm($model);

      $this->assertEquals('foo', $form->getFormElementByName('foo')->getValue());
      $this->assertEquals('bar', $form->getFormElementByName('bar')->getValue());
      $this->assertEquals('baz', $form->getFormElementByName('baz')->getValue());
   }

   /**
    * Tests explicit mapping of sub-list of model fields.
    */
   public function testExplicitMapping() {

      // test mapping of existing values
      $model = new FormValuesModel();
      $model->setFoo('foo');
      $model->setBar('bar');
      $model->setBaz('baz');

      $form = $this->getSimpleForm();

      $form->fillForm($model, ['foo', 'bar']);

      $this->assertEquals('foo', $form->getFormElementByName('foo')->getValue());
      $this->assertEquals('bar', $form->getFormElementByName('bar')->getValue());
      $this->assertEmpty($form->getFormElementByName('baz')->getValue());

      // test mapping of non-existing field
      $form = $this->getSimpleForm();

      $model = new FormValuesModel();

      $form->fillForm($model, ['not-existing']);

      $this->assertEmpty($form->getFormElementByName('foo')->getValue());
      $this->assertEmpty($form->getFormElementByName('bar')->getValue());
      $this->assertEmpty($form->getFormElementByName('baz')->getValue());
   }

   /**
    * Tests mapping for select fields and multi-select fields.
    */
   public function testValueMappingOfSelectField() {

      // simple select field
      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');
      $form->setContent('<form:select name="foo">
   <select:option value="1">One</select:option>
   <select:option value="2">Two</select:option>
   <select:option value="3">Three</select:option>
</form:select>');
      $form->onParseTime();
      $form->onAfterAppend();

      /* @var $select SelectBoxTag */
      $select = $form->getFormElementByName('foo');
      $option = $select->getSelectedOption();
      $this->assertEmpty($option);

      $model = new FormValuesModel();
      $model->setFoo('2');

      $form->fillForm($model);

      /* @var $select SelectBoxTag */
      $select = $form->getFormElementByName('foo');
      $option = $select->getSelectedOption();
      $this->assertNotEmpty($option);
      $this->assertInstanceOf(SelectBoxOptionTag::class, $option);
      $this->assertEquals('Two', $option->getContent());

      // multi select field
      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');
      $form->setContent('<form:multiselect name="foo">
   <select:option value="1">One</select:option>
   <select:option value="2">Two</select:option>
   <select:option value="3">Three</select:option>
</form:multiselect>');
      $form->onParseTime();
      $form->onAfterAppend();

      /* @var $select MultiSelectBoxTag */
      $select = $form->getFormElementByName('foo');
      $options = $select->getSelectedOptions();
      $this->assertEmpty($options);

      $model = new FormValuesModel();
      $model->setFoo(['2', '3']);

      $form->fillForm($model);

      /* @var $select MultiSelectBoxTag */
      $select = $form->getFormElementByName('foo');
      $options = $select->getSelectedOptions();
      $this->assertNotEmpty($options);
      $this->assertCount(2, $options);
      $this->assertInstanceOf(SelectBoxOptionTag::class, $options[0]);
      $this->assertInstanceOf(SelectBoxOptionTag::class, $options[1]);
      $this->assertEquals('Two', $options[0]->getContent());
      $this->assertEquals('Three', $options[1]->getContent());

   }

   /**
    * Tests mapping of model values to radio buttons. Difficulty here is that radio buttons typically have
    * two or more options with the same name but with different ids.
    */
   public function testValueMappingOfRadioButtons() {

      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');
      $form->setContent('<form:group>
<form:radio name="foo" value="value-1" id="foo-1" />
<form:radio name="foo" value="value-2" id="foo-2" />
<form:radio name="foo" value="value-3" id="foo-3" />
</form:group>');
      $form->onParseTime();
      $form->onAfterAppend();

      $this->assertFalse($form->getFormElementByID('foo-1')->isChecked());
      $this->assertFalse($form->getFormElementByID('foo-2')->isChecked());
      $this->assertFalse($form->getFormElementByID('foo-3')->isChecked());

      $model = new FormValuesModel();
      $model->setFoo('value-2');

      $form->fillForm($model);

      $this->assertFalse($form->getFormElementByID('foo-1')->isChecked());
      $this->assertTrue($form->getFormElementByID('foo-2')->isChecked());
      $this->assertFalse($form->getFormElementByID('foo-3')->isChecked());

   }

   /**
    * Tests mapping of check boxes.
    */
   public function testValueMappingOfCheckBox() {

      // box is unchecked
      $form = $this->getFormForCheckBoxTest();

      $this->assertFalse($form->getFormElementByName('foo')->isChecked());

      $model = new FormValuesModel();
      $model->setFoo(true);

      $form->fillForm($model);
      $this->assertTrue($form->getFormElementByName('foo')->isChecked());

      // box is checked
      $form = $this->getFormForCheckBoxTest();
      $form->getFormElementByName('foo')->check();

      $this->assertTrue($form->getFormElementByName('foo')->isChecked());

      $model = new FormValuesModel();
      $model->setFoo(false);

      $form->fillForm($model);
      $this->assertFalse($form->getFormElementByName('foo')->isChecked());

   }

   /**
    * @return HtmlFormTag
    */
   private function getFormForCheckBoxTest() {
      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');
      $form->setContent('<form:checkbox name="foo" value="bar" />');
      $form->onParseTime();
      $form->onAfterAppend();

      return $form;
   }

   /**
    * Test mapping of dates.
    */
   public function testValueMappingOfDateControls() {

      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');
      $form->setContent('<form:date name="foo" />');
      $form->onParseTime();
      $form->onAfterAppend();

      // test with string
      $model = new FormValuesModel();
      $date = '2016-07-03';
      $model->setFoo($date);

      $form->fillForm($model);
      $this->assertEquals($date, $form->getFormElementByName('foo')->getValue()->format('Y-m-d'));

      // test with DateTime instance
      $model = new FormValuesModel();
      $date = DateTime::createFromFormat('Y-m-d', '2016-07-03');
      $model->setFoo($date);

      $form->fillForm($model);
      $this->assertEquals($date, $form->getFormElementByName('foo')->getValue());

   }

   /**
    * Test mapping of time.
    */
   public function testValueMappingOfTimeControls() {

      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');
      $form->setContent('<form:time name="foo" showseconds="false" />');
      $form->onParseTime();
      $form->onAfterAppend();

      // test with string format
      $model = new FormValuesModel();
      $time = '14:24';
      $model->setFoo($time);

      $form->fillForm($model);
      $this->assertEquals($time, $form->getFormElementByName('foo')->getValue()->format('H:i'));

      // test with DateTime instance
      $model = new FormValuesModel();
      $time = DateTime::createFromFormat('H:i', '14:24');
      $model->setFoo($time);

      $form->fillForm($model);
      $this->assertEquals($time, $form->getFormElementByName('foo')->getValue());
   }

}
