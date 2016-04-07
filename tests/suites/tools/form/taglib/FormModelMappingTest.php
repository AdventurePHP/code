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
use APF\tools\form\mapping\StandardValueMapper;
use APF\tools\form\taglib\HtmlFormTag;
use DateTime;
use ReflectionProperty;

/**
 * Tests the form model mapping capabilities.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 06.04.2016 (ID#275: introduced form to model mappings)<br />
 */
class FormModelMappingTest extends \PHPUnit_Framework_TestCase {

   /**
    * Tests adding and clearing of form-to-model mapping configuratons.
    */
   public function testMappingConfiguration() {

      $property = new ReflectionProperty(HtmlFormTag::class, 'formDataMappers');
      $property->setAccessible(true);

      $original = $property->getValue();
      $this->assertCount(4, $original);

      HtmlFormTag::clearFormValueMappers();
      $actual = $property->getValue();
      $this->assertCount(0, $actual);

      HtmlFormTag::addFormValueMapper(StandardValueMapper::class);
      $actual = $property->getValue();
      $this->assertCount(1, $actual);

      $property->setValue(null, $original);
   }

   /**
    * Test mapping with empty form does not fail.
    */
   public function testEmptyForm() {

      $_REQUEST = [];

      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');

      $model = new FormValuesModel();
      $form->fillModel($model);
      $this->assertEmpty($model->getFoo());
      $this->assertEmpty($model->getBar());
      $this->assertEmpty($model->getBaz());
   }

   /**
    * Tests existing fields with empty form content.
    */
   public function testExistingFields1() {

      $_REQUEST = [];

      $form = $this->getSimpleForm();

      $model = new FormValuesModel();
      $form->fillModel($model);
      $this->assertEmpty($model->getFoo());
      $this->assertEmpty($model->getBar());
      $this->assertEmpty($model->getBaz());
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
    * Test existing fields with filled data.
    */
   public function testExistingFields2() {

      $_REQUEST = [];
      $_REQUEST['foo'] = 'foo';
      $_REQUEST['bar'] = 'bar';
      $_REQUEST['baz'] = 'baz';

      $form = $this->getSimpleForm();

      $model = new FormValuesModel();
      $form->fillModel($model);
      $this->assertEquals('foo', $model->getFoo());
      $this->assertEquals('bar', $model->getBar());
      $this->assertEquals('baz', $model->getBaz());
   }

   /**
    * Tests explicit mapping of sub-list of model fields.
    */
   public function testExplicitMapping() {

      // test mapping of existing values
      $_REQUEST = [];
      $_REQUEST['foo'] = 'foo';
      $_REQUEST['bar'] = 'bar';
      $_REQUEST['baz'] = 'baz';

      $form = $this->getSimpleForm();

      $model = new FormValuesModel();
      $form->fillModel($model, ['foo', 'bar']);
      $this->assertEquals('foo', $model->getFoo());
      $this->assertEquals('bar', $model->getBar());
      $this->assertEmpty($model->getBaz());

      // test mapping of non-existing values
      $_REQUEST = [];
      $_REQUEST['bar'] = 'bar';

      $form = $this->getSimpleForm();

      $model = new FormValuesModel();
      $form->fillModel($model, ['foo']);
      $this->assertEmpty($model->getFoo());
      $this->assertEmpty($model->getBar());
      $this->assertEmpty($model->getBaz());
   }

   /**
    * Tests model mapping for select fields and multi-select fields.
    */
   public function testValueMappingOfSelectField() {

      $_REQUEST = [];

      // simple select field
      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');
      $form->setContent('<form:select name="foo">
   <select:option value="1">One</select:option>
   <select:option value="2" selected="selected">Two</select:option>
   <select:option value="3">Three</select:option>
</form:select>');
      $form->onParseTime();
      $form->onAfterAppend();

      $model = new FormValuesModel();
      $form->fillModel($model);
      $this->assertTrue(is_string($model->getFoo()));
      $this->assertEquals('2', $model->getFoo());
      $this->assertEmpty($model->getBar());
      $this->assertEmpty($model->getBaz());

      // multi select field
      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');
      $form->setContent('<form:multiselect name="foo">
   <select:option value="1">One</select:option>
   <select:option value="2" selected="selected">Two</select:option>
   <select:option value="3" selected="selected">Three</select:option>
</form:multiselect>');
      $form->onParseTime();
      $form->onAfterAppend();

      $model = new FormValuesModel();
      $form->fillModel($model);
      $this->assertTrue(is_array($model->getFoo()));
      $this->assertEquals(['2', '3'], $model->getFoo());
      $this->assertEmpty($model->getBar());
      $this->assertEmpty($model->getBaz());

   }

   /**
    * Tests model mapping for radio buttons. Difficulty here is that radio buttons typically have
    * two or more options with the same name but with different ids.
    *
    * Question: how to find out which is the active one?
    */
   public function testValueMappingOfRadioButtons() {

      $_REQUEST = [];

      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');
      $form->setContent('<form:group>
<form:radio name="foo" value="value-1" id="foo-1" />
<form:radio name="foo" value="value-2" id="foo-2" checked="checked" />
<form:radio name="foo" value="value-3" id="foo-3" />
</form:group>');
      $form->onParseTime();
      $form->onAfterAppend();

      $model = new FormValuesModel();
      $form->fillModel($model);
      $this->assertTrue(is_string($model->getFoo()));
      $this->assertEquals('value-2', $model->getFoo());
      $this->assertEmpty($model->getBar());
      $this->assertEmpty($model->getBaz());
   }

   /**
    * Test mapping of dates.
    */
   public function testValueMappingOfDateControls() {

      $year = '2016';
      $month = '04';
      $day = '07';

      $_REQUEST = [];
      $_REQUEST['foo']['Year'] = $year;
      $_REQUEST['foo']['Month'] = $month;
      $_REQUEST['foo']['Day'] = $day;

      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');
      $form->setContent('<form:date name="foo" />');
      $form->onParseTime();
      $form->onAfterAppend();

      $model = new FormValuesModel();
      $form->fillModel($model);

      /* @var $date DateTime */
      $date = $model->getFoo();
      $this->assertInstanceOf(DateTime::class, $date);
      $this->assertEquals($year . '-' . $month . '-' . $day, $date->format('Y-m-d'));

   }

   /**
    * Test mapping of time.
    */
   public function testValueMappingOfTimeControls() {

      $hour = '04';
      $minutes = '07';

      $_REQUEST = [];
      $_REQUEST['foo']['Hours'] = $hour;
      $_REQUEST['foo']['Minutes'] = $minutes;

      $form = new HtmlFormTag();
      $form->setParentObject(new Document());
      $form->setAttribute('name', 'test');
      $form->setContent('<form:time name="foo" showseconds="false" />');
      $form->onParseTime();
      $form->onAfterAppend();

      $model = new FormValuesModel();
      $form->fillModel($model);

      /* @var $time DateTime */
      $time = $model->getFoo();
      $this->assertInstanceOf(DateTime::class, $time);
      $this->assertEquals($hour . ':' . $minutes . ':00', $time->format('H:i:s'));
   }

}
