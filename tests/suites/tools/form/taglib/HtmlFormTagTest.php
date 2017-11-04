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
use APF\core\pagecontroller\LanguageLabel;
use APF\core\pagecontroller\XmlParser;
use APF\tests\suites\tools\form\mock\TextFieldTagMock;
use APF\tools\form\FormException;
use APF\tools\form\HtmlForm;
use APF\tools\form\taglib\ButtonTag;
use APF\tools\form\taglib\FormGroupTag;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\taglib\RadioButtonTag;
use APF\tools\form\taglib\SelectBoxTag;
use APF\tools\form\taglib\TextFieldTag;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionMethod;
use ReflectionProperty;

class HtmlFormTagTest extends \PHPUnit_Framework_TestCase {

   /**
    * Tests form which is not sent.
    */
   public function testIsSent1() {

      $_GET = [];
      $_POST = [];
      $_REQUEST = [];

      // simple form
      $form = $this->getSimpleForm4SentCheck();
      $this->assertFalse($form->isSent());

      // form w/ groups/wrappers
      $form = $this->getComplexForm4SentCheck();
      $this->assertFalse($form->isSent());

   }

   /**
    * @return HtmlFormTag Simple form to test with.
    */
   protected function getSimpleForm4SentCheck() {
      $form = new HtmlFormTag();
      $form->setAttribute('name', 'foo');
      $form->setContent('<form:text name="user"/>
<form:text name="pass"/>
<form:button name="submit" value="submit"/>');
      $form->onParseTime();
      $form->onAfterAppend();

      return $form;
   }

   /**
    * @return HtmlFormTag Complex (=nested structure) form to test with.
    */
   protected function getComplexForm4SentCheck() {
      $form = new HtmlFormTag();
      $form->setAttribute('name', 'foo');
      $form->setContent('<form:text name="user"/>
<form:text name="pass"/>
<form:group name="stay-logged-in">
   <form:label name="stay-logged-in-label">Stay logged in?</form:label>
   <form:radio name="stay-logged-in" value="0"/>
</form:group>
<form:button name="submit" value="submit"/>');
      $form->onParseTime();
      $form->onAfterAppend();

      return $form;
   }

   /**
    * Tests form which is sent.
    */
   public function testIsSent2() {

      $_GET = [];
      $_POST['submit'] = 'submit';
      $_REQUEST = $_POST;

      // simple form
      $form = $this->getSimpleForm4SentCheck();
      $this->assertTrue($form->isSent());

      // form w/ groups/wrappers
      $form = $this->getComplexForm4SentCheck();
      $this->assertTrue($form->isSent());
   }

   /**
    * Tests validation with a sent form but empty values.
    */
   public function testIsValid1() {

      $_GET = [];
      $_POST['submit'] = 'submit';
      $_REQUEST = [];

      // simple form
      $form = $this->getSimpleForm4ValidityCheck();
      $this->assertFalse($form->isValid());

      // form w/ groups/wrappers
      $form = $this->getComplexForm4ValidityCheck();
      $this->assertFalse($form->isValid());
   }

   /**
    * @return HtmlFormTag Simple form to test with.
    */
   protected function getSimpleForm4ValidityCheck() {
      $form = new HtmlFormTag();
      $form->setAttribute('name', 'foo');
      $form->setContent('<form:text name="user"/>
<form:text name="pass"/>
<form:button name="submit" value="submit"/>
<form:addvalidator class="APF\tools\form\validator\TextLengthValidator" button="submit" control="user" />');
      $form->onParseTime();
      $form->onAfterAppend();

      return $form;
   }

   /**
    * @return HtmlFormTag Complex (=nested structure) form to test with.
    */
   protected function getComplexForm4ValidityCheck() {

      $form = new HtmlFormTag();

      // inject parent object to make recursive selection work
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttribute('name', 'foo');
      $form->setContent('<form:text name="user"/>
<form:group>
   <form:text name="pass"/>
</form:group>
<form:group name="stay-logged-in">
   <form:label name="stay-logged-in-label">Stay logged in?</form:label>"
   <form:radio name="stay-logged-in" value="0"/>
</form:group>
<form:group name="button">
   <form:button name="submit" value="submit"/>
   <form:addvalidator class="APF\tools\form\validator\TextLengthValidator" button="submit" control="user" />
   <form:addvalidator class="APF\tools\form\validator\TextLengthValidator" button="submit" control="pass" />
</form:group>');
      $form->onParseTime();
      $form->onAfterAppend();

      return $form;
   }

   /**
    * Tests successful validation with a posted form.
    */
   public function testIsValid2() {

      $_GET = [];
      $_POST['submit'] = 'submit';
      $_REQUEST = $_POST;
      $_REQUEST['user'] = 'sample value';
      $_REQUEST['pass'] = 'sample value';

      // simple form
      $form = $this->getSimpleForm4ValidityCheck();
      $this->assertTrue($form->isValid());

      // form w/ groups/wrappers
      $form = $this->getComplexForm4ValidityCheck();
      $this->assertTrue($form->isValid());
   }

   /**
    * Tests form resetting including form wrapper structures.
    */
   public function testReset() {

      $_GET = [];
      $_POST = [];
      $_REQUEST = [];

      $form = new HtmlFormTag();

      $form->setAttribute('name', 'foo');
      $form->setContent('<form:text name="user" value="some user"/>
<form:text name="pass" value="some pass"/>
<form:group>
   <form:text name="some-text" value="some text"/>
</form:group>
<form:button name="submit" value="submit"/>');
      $form->onParseTime();
      $form->onAfterAppend();

      $this->assertEquals('some user', $form->getFormElementByName('user')->getValue());
      $this->assertEquals('some user', $form->getFormElementByName('user')->getValue());
      $this->assertEquals('some text', $form->getFormElementByName('some-text')->getValue());

      $form->reset();

      $this->assertEquals('', $form->getFormElementByName('user')->getValue());
      $this->assertEquals('', $form->getFormElementByName('pass')->getValue());
      $this->assertEquals('', $form->getFormElementByName('some-text')->getValue());

   }

   /**
    * Tests form transformation with a simple and a more sophisticated example.
    */
   public function testTransform() {

      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAction('/some-url');

      // setup reflection property to be able to inject the DOM structure to test
      $children = new ReflectionProperty(Document::class, 'children');
      $children->setAccessible(true);

      // set up group
      $group = new FormGroupTag();
      $group->setObjectId(XmlParser::generateUniqID());
      $group->setParentObject($form);

      // set up fields
      $user = new TextFieldTag();
      $user->setObjectId(XmlParser::generateUniqID());
      $user->setAttributes(['name' => 'user', 'value' => 'some user']);
      $user->setParentObject($form);

      $pass = new TextFieldTag();
      $pass->setObjectId(XmlParser::generateUniqID());
      $pass->setAttributes(['name' => 'pass', 'value' => 'some pass']);
      $pass->setParentObject($group);

      $button = new ButtonTag();
      $button->setObjectId(XmlParser::generateUniqID());
      $button->setAttributes(['name' => 'submit', 'value' => 'submit']);
      $button->setParentObject($form);

      // assemble group
      $children->setValue($group, array_merge($children->getValue($group), [$pass->getObjectId() => $pass]));
      $group->setContent('<div class="pass"><' . $pass->getObjectId() . ' /></div>');

      // assemble form
      $children->setValue(
            $form,
            [
                  $user->getObjectId() => $user,
                  $group->getObjectId() => $group,
                  $button->getObjectId() => $button
            ]
      );

      $form->setContent('<p><' . $user->getObjectId() . ' /></p><' . $group->getObjectId() . ' /><' . $button->getObjectId() . ' />');

      // build children explicitly up and then check for HTML code...
      $actual = $form->transformForm();

      // general structure
      $this->assertContains('<form ', $actual);
      $this->assertContains('</form>', $actual);
      $this->assertContains('action="/some-url"', $actual);
      $this->assertContains('<input type="text" name="user" value="some user" />', $actual);
      $this->assertContains('<input type="text" name="pass" value="some pass" />', $actual);
      $this->assertContains('<input type="submit" name="submit" value="submit" />', $actual);
      $this->assertContains('<p>', $actual);
      $this->assertContains('</p>', $actual);
      $this->assertContains('<div class="pass">', $actual);
      $this->assertContains('</div>', $actual);

   }

   /**
    * Test interface complies for not-existing interface.
    */
   public function testFindById1() {
      $this->expectException(FormException::class);
      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);
      $form->getFormElementByID('not-existing');
   }

   /**
    * Test simple id selection.
    */
   public function testFindById2() {

      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);
      $form->setContent('<form:text id="foo" name="bar" value="123"/>
<form:text name="foo" />
<form:button name="submit" value="submit" />');
      $form->onParseTime();
      $form->onAfterAppend();

      $actual = $form->getFormElementByID('foo');
      $this->assertNotNull($actual);
      $this->assertInstanceOf(TextFieldTag::class, $actual);
      $this->assertEquals('123', $actual->getAttribute('value'));

   }

   /**
    * Test id selection within nested elements.
    */
   public function testFindById3() {

      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);
      $form->setContent('<form:text name="bar" />
<form:group>
   <form:group>
      <form:text id="foo" name="foo" value="123" />
   </form:group>
</form:group>
<form:button name="submit" value="submit" />');
      $form->onParseTime();
      $form->onAfterAppend();

      $actual = $form->getFormElementByID('foo');
      $this->assertNotNull($actual);
      $this->assertInstanceOf(TextFieldTag::class, $actual);
      $this->assertEquals('123', $actual->getAttribute('value'));

   }


   /**
    * Test interface complies for not-existing interface.
    */
   public function testGetLabel1() {
      $this->expectException(FormException::class);
      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);
      $form->getLabel('not-existing');
   }

   /**
    * Test simple id selection.
    */
   public function testGetLabel2() {

      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);
      $form->setContent('<form:label name="foo">Label</form:label>
<html:getstring
         name="foo"
         namespace="APF\modules\usermanagement\pres"
         config="labels.ini"
         entry="frontend.proxy.add-users-to-proxy.intro.text"/>
<form:text name="foo" />
<form:button name="submit" value="submit" />');
      $form->onParseTime();
      $form->onAfterAppend();

      $actual = $form->getLabel('foo');
      $this->assertNotNull($actual);
      $this->assertInstanceOf(LanguageLabel::class, $actual);
      $this->assertEquals('labels.ini', $actual->getAttribute('config'));

   }

   /**
    * Test id selection within nested elements.
    */
   public function testGetLabel3() {

      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);
      $form->setContent('<form:text name="bar" />
<form:group>
   <form:label name="bar">Other Label</form:label>
   <form:group>
      <form:label name="foo">Label</form:label>
      <html:getstring
               name="foo"
               namespace="APF\modules\usermanagement\pres"
               config="labels.ini"
               entry="frontend.proxy.add-users-to-proxy.intro.text"/>
   </form:group>
</form:group>
<form:button name="submit" value="submit" />');
      $form->onParseTime();
      $form->onAfterAppend();

      $actual = $form->getLabel('foo');
      $this->assertNotNull($actual);
      $this->assertInstanceOf(LanguageLabel::class, $actual);
      $this->assertEquals('labels.ini', $actual->getAttribute('config'));

   }

   /**
    * Tests API complies for non existing tags.
    */
   public function testGetFormElementsByTagName1() {
      $this->expectException(FormException::class);
      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);
      $form->getFormElementsByTagName('html:placeholder');
   }

   /**
    * Test simple form structure.
    */
   public function testGetFormElementsByTagName2() {

      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);
      $form->setContent('<form:text id="foo-1" name="bar-1" value="123"/>
<form:text id="foo-2" name="bar-2" />
<form:radio name="bar-3" value="1" />
<form:text id="foo-4" name="bar-4" />
<form:button name="submit" value="submit" />');
      $form->onParseTime();
      $form->onAfterAppend();

      $this->assertCount(5, $form->getChildren());

      $actual = $form->getFormElementsByTagName('form:text');

      $this->assertCount(3, $actual);

      foreach ($actual as $field) {
         $this->assertInstanceOf(TextFieldTag::class, $field);
         $this->assertNotEmpty($field->getAttribute('name'));
      }

      $this->assertEquals('foo-1', $actual[0]->getAttribute('id'));
      $this->assertEquals('foo-2', $actual[1]->getAttribute('id'));
      $this->assertEquals('foo-4', $actual[2]->getAttribute('id'));

   }

   /**
    * Test more complex form structure.
    */
   public function testGetFormElementsByTagName3() {

      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);
      $form->setContent('<form:text id="foo-1" name="bar-1" value="123"/>
<form:group>
   <form:group>
      <form:text id="foo-2" name="bar-2" />
   </form:group>
</form:group>
<form:radio name="bar-3" value="1" />
<form:group>
   <form:text id="foo-4" name="bar-4" />
</form:group>
<form:button name="submit" value="submit" />');
      $form->onParseTime();
      $form->onAfterAppend();

      $this->assertCount(5, $form->getChildren());

      $actual = $form->getFormElementsByTagName('form:text');

      $this->assertCount(3, $actual);

      foreach ($actual as $field) {
         $this->assertInstanceOf(TextFieldTag::class, $field);
         $this->assertNotEmpty($field->getAttribute('name'));
      }

      $this->assertEquals('foo-1', $actual[0]->getAttribute('id'));
      $this->assertEquals('foo-2', $actual[1]->getAttribute('id'));
      $this->assertEquals('foo-4', $actual[2]->getAttribute('id'));

   }

   /**
    * Tests API complies for no elements found.
    */
   public function testGetFormElementsByName1() {

      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);
      $this->assertEmpty($form->getFormElementsByName('foo:bar'));

   }

   /**
    * Test more complex use case.
    */
   public function testGetFormElementsByName2() {

      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);
      $form->setContent('<form:text id="foo-1" name="bar" value="123"/>
<form:group>
   <form:group>
      <form:text name="bar-2" value="123"/>
      <form:radio id="radio-1" name="bar" value="1" />
   </form:group>
   <form:radio id="radio-2" name="bar" value="2" />
</form:group>
<form:select name="bar" id="select-1">
   <select:option value="1">One</select:option>
   <select:option value="2">Two</select:option>
</form:select>
<form:group>
   <form:text id="foo-4" name="bar" />
</form:group>
<form:button name="submit" value="submit" />');
      $form->onParseTime();
      $form->onAfterAppend();

      $this->assertCount(5, $form->getChildren());

      $actual = $form->getFormElementsByName('bar');

      $this->assertCount(5, $actual);

      $this->assertInstanceOf(TextFieldTag::class, $actual[0]);
      $this->assertEquals('foo-1', $actual[0]->getAttribute('id'));

      $this->assertInstanceOf(RadioButtonTag::class, $actual[1]);
      $this->assertEquals('radio-1', $actual[1]->getAttribute('id'));

      $this->assertInstanceOf(RadioButtonTag::class, $actual[2]);
      $this->assertEquals('radio-2', $actual[2]->getAttribute('id'));

      $this->assertInstanceOf(SelectBoxTag::class, $actual[3]);
      $this->assertEquals('select-1', $actual[3]->getAttribute('id'));

      $this->assertInstanceOf(TextFieldTag::class, $actual[4]);
      $this->assertEquals('foo-4', $actual[4]->getAttribute('id'));

   }

   /**
    * Tests getMarker() delegates to getFormElementByName().
    */
   public function testGetMarker() {

      /* @var $form HtmlFormTag|PHPUnit_Framework_MockObject_MockObject */
      $form = $this->getMockBuilder(HtmlFormTag::class)
            ->setMethods(['getFormElementByName'])
            ->getMock();

      $form->expects($this->once())
            ->method('getFormElementByName')
            ->with('foo');

      $form->getMarker('foo');

   }

   /**
    * Checks whether form control creation complies with APF DomNode creation.
    */
   public function testCreateFormElement() {

      $name = 'foo';
      $context = 'bar';
      $language = 'en';

      $form = new HtmlFormTag();
      $form->setContext($context);
      $form->setLanguage($language);

      // register mock implementation to allow check of method execution
      Document::addTagLib(TextFieldTagMock::class, 'test', 'text');

      $method = new ReflectionMethod(HtmlFormTag::class, 'createFormElement');
      $method->setAccessible(true);

      // note: arguments to be passed ad reference MUST be explicitly referenced with &
      $actual = $method->invokeArgs($form, [&$form, 'test:text', ['name' => $name, 'class' => 'text-field']]);

      /* @var $field TextFieldTagMock */
      $field = $form->getFormElementByName($name);
      $objectId = $field->getObjectId();

      // check identity to ensure references are returned
      $this->assertEquals(spl_object_hash($actual), spl_object_hash($field));

      // test element creation as part of the applied DomNode instance
      $this->assertInstanceOf(TextFieldTag::class, $field);

      // test object id, attributes, context language,
      $this->assertNotNull($objectId);
      $this->assertEquals($name, $field->getAttribute('name'));
      $this->assertEquals($context, $field->getContext());
      $this->assertEquals($language, $field->getLanguage());

      // check whether parent object is initialized (form)
      $this->assertEquals($form, $field->getParentObject());

      // check whether onParseTime() and onAfterAppend() are called
      $this->assertTrue($field->onParseTimeExecuted);
      $this->assertTrue($field->onAfterAppendExecuted);

      // check if internal array structure is valid
      $children = $form->getChildren();
      $this->assertNotEmpty($children);
      $this->assertContains($objectId, array_keys($children));

   }

   /**
    * Tests whether the form implementation automatically renders hidden fields to
    * preserve GET parameters in action urls for convenience reasons.
    */
   public function testSubmitGetParametersInGetMode() {

      // "old" behaviour before change ID#281:
      $form = $this->getSimpleForm();
      $form->setAction('/');
      $form->setAttribute(HtmlForm::METHOD_ATTRIBUTE_NAME, HtmlForm::METHOD_GET_VALUE_NAME);
      $actual = $form->transformForm();

      $this->assertNotContains('<input type="hidden"', $actual);

      // test behaviour that no additional hidden fields are rendered in case action URL does not contain query params
      $form = $this->getSimpleForm();
      $form->setAction('/');
      $form->setAttribute(HtmlForm::METHOD_ATTRIBUTE_NAME, HtmlForm::METHOD_GET_VALUE_NAME);
      $form->setAttribute(HtmlFormTag::SUBMIT_ACTION_URL_PARAMS_ATTRIBUTE_NAME, 'true');
      $actual = $form->transformForm();

      $this->assertNotContains('<input type="hidden"', $actual);

      // test "new" behaviour with hidden fields having an action URL with query params and having the new
      // behaviour activated by form attribute
      $form = $this->getSimpleForm();
      $form->setAction('/?foo=bar&bar=baz');
      $form->setAttribute(HtmlForm::METHOD_ATTRIBUTE_NAME, HtmlForm::METHOD_GET_VALUE_NAME);
      $form->setAttribute(HtmlFormTag::SUBMIT_ACTION_URL_PARAMS_ATTRIBUTE_NAME, 'true');
      $actual = $form->transformForm();

      $this->assertContains('<input type="hidden" name="foo" value="bar" />', $actual);
      $this->assertContains('<input type="hidden" name="bar" value="baz" />', $actual);

      // test parameter order is preserved
      $fooPos = strpos($actual, 'name="foo"');
      $barPos = strpos($actual, 'name="bar"');
      $this->assertGreaterThan($fooPos, $barPos);

      // test behaviour is only working with GET requests
      $form = $this->getSimpleForm();
      $form->setAction('/?foo=bar&bar=baz');
      $form->setAttribute(HtmlForm::METHOD_ATTRIBUTE_NAME, HtmlForm::METHOD_POST_VALUE_NAME);
      $form->setAttribute(HtmlFormTag::SUBMIT_ACTION_URL_PARAMS_ATTRIBUTE_NAME, 'true');
      $actual = $form->transformForm();

      $this->assertNotContains('<input type="hidden"', $actual);

   }

   /**
    * @return HtmlFormTag
    */
   private function getSimpleForm() {
      $form = new HtmlFormTag();

      $doc = new Document();
      $form->setParentObject($doc);
      $form->setContent('<form:text id="text" name="text" value="123"/>
<form:button name="submit" value="submit" />');

      $form->onParseTime();
      $form->onAfterAppend();

      return $form;
   }

   /**
    * ID#303: test capabilities to hide form group by default from within a template.
    */
   public function testHidingByAttribute() {

      // tag is visible by default
      $tag = new FormGroupTag();
      $tag->setAttributes(['name' => 'foo']);
      $tag->onParseTime();

      $this->assertTrue($tag->isVisible());

      // hide tag with attribute
      $tag = new FormGroupTag();
      $tag->setAttributes(['name' => 'foo', 'hidden' => 'true']);
      $tag->onParseTime();

      $this->assertFalse($tag->isVisible());

   }

   /**
    * ID#326: test mapping a (valid) PHP property name to form control name.
    */
   public function testMapModelPropertyNameToFormControlName() {

      $method = new ReflectionMethod(HtmlFormTag::class, 'mapModelPropertyNameToFormControlName');
      $method->setAccessible(true);

      $tag = new HtmlFormTag();
      $this->assertEquals('foo-bar', $method->invokeArgs($tag, ['fooBar']));
      $this->assertEquals('f-oO', $method->invokeArgs($tag, ['fOO']));
      $this->assertEquals('foo_bar', $method->invokeArgs($tag, ['foo_bar']));
      $this->assertEquals('foo', $method->invokeArgs($tag, ['foo']));
      $this->assertEquals('Foo', $method->invokeArgs($tag, ['Foo']));
      $this->assertEquals('foo-bar-baz', $method->invokeArgs($tag, ['fooBarBaz']));
      $this->assertEquals('foo-bAr', $method->invokeArgs($tag, ['fooBAr']));

   }

}
