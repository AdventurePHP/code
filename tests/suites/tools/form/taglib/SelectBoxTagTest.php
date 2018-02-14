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

use APF\core\pagecontroller\DomNode;
use APF\core\pagecontroller\XmlParser;
use APF\tools\form\HtmlForm;
use APF\tools\form\taglib\ButtonTag;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\taglib\SelectBoxGroupTag;
use APF\tools\form\taglib\SelectBoxOptionTag;
use APF\tools\form\taglib\SelectBoxTag;
use APF\tools\form\validator\SimpleSelectControlValidator;
use ReflectionMethod;
use ReflectionProperty;

class SelectBoxTagTest extends \PHPUnit_Framework_TestCase {

   const SELECT_BOX_NAME = 'foo';
   const COMPLEX_SELECT_BOX_NAME = 'foo[bar]';

   public function testControllerPresetting() {

      $_REQUEST = [];

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addOption($number, $number);
      }

      $this->assertNull($select->getSelectedOption());

      $selectedOption = 4;
      $select->setOption2Selected($selectedOption);
      $this->assertEquals($select->getSelectedOption()->getValue(), $selectedOption);

   }

   protected function getSelectBox($name = self::SELECT_BOX_NAME) {
      $select = new SelectBoxTag();
      $select->setAttribute('name', $name);

      return $select;
   }

   public function testSubmitPresetting() {

      $selectedOption = 4;

      $_REQUEST = [];
      $_REQUEST[self::SELECT_BOX_NAME] = $selectedOption;

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addOption($number, $number);
      }

      $this->assertEquals($select->getSelectedOption()->getValue(), $selectedOption);

      // re-test with complex name
      $_REQUEST = [];
      $_REQUEST[self::SELECT_BOX_NAME]['bar'] = $selectedOption;

      $select = $this->getSelectBox(self::COMPLEX_SELECT_BOX_NAME);

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addOption($number, $number);
      }

      $this->assertEquals($select->getSelectedOption()->getValue(), $selectedOption);

   }

   public function testControllerPresettingWithGroups() {

      $_REQUEST = [];

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addGroupOption('number-' . $number % 2, $number, $number);
      }

      $this->assertNull($select->getSelectedOption());

      $selectedOption = 4;
      $select->setOption2Selected($selectedOption);
      $this->assertEquals($select->getSelectedOption()->getValue(), $selectedOption);

   }

   public function testSubmitPresettingWithGroups() {

      $selectedOption = 4;

      $_REQUEST = [];
      $_REQUEST[self::SELECT_BOX_NAME] = $selectedOption;

      $select = $this->getSelectBox();

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addGroupOption('number-' . $number % 2, $number, $number);
      }

      $this->assertEquals($select->getSelectedOption()->getValue(), $selectedOption);

      // re-test with complex name
      $_REQUEST = [];
      $_REQUEST[self::SELECT_BOX_NAME]['bar'] = $selectedOption;

      $select = $this->getSelectBox(self::COMPLEX_SELECT_BOX_NAME);

      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('', ''); // empty option
      foreach (range(1, 5) as $number) {
         $select->addGroupOption('number-' . $number % 2, $number, $number);
      }

      $this->assertEquals($select->getSelectedOption()->getValue(), $selectedOption);

   }

   /**
    * Test whether getGroup() responds correctly.
    */
   public function testGroupRetrieving() {

      // test no group defined
      $tag = new SelectBoxTag();
      $this->assertNull($tag->getGroup('foo'));

      // test groups defined
      $tag = new SelectBoxTag();
      $tag->setContent('<select:group label="foo">
   <group:option value="0">Zero</group:option>
   <group:option value="1" selected="selected">One</group:option>
</select:group>
<select:group label="bar">
   <group:option value="2">Two</group:option>
   <group:option value="3" selected="selected">Three</group:option>
</select:group>');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $this->assertNull($tag->getGroup('not-existing'));
      $this->assertNotNull($tag->getGroup('foo'));
      $this->assertNotNull($tag->getGroup('bar'));

      // test references returned not copies
      $children = $tag->getChildren();
      $keys = array_keys($children);

      $this->assertEquals(
            spl_object_hash($children[$keys[0]]),
            spl_object_hash($tag->getGroup('foo'))
      );

   }

   /**
    * Test whether other options are unselected during selection of one
    * specific option in simple select boxes.
    */
   public function testRemoveSelectedOptions() {

      $tag = new SelectBoxTag();

      $selectedOption = new SelectBoxOptionTag();
      $selectedOption->setAttribute('selected', 'selected');

      $unselectedOption = new SelectBoxOptionTag();

      $firstId = XmlParser::generateUniqID();
      $secondId = XmlParser::generateUniqID();
      $thirdId = XmlParser::generateUniqID();

      // inject child structure as basis for un-selection
      $property = new ReflectionProperty(SelectBoxTag::class, 'children');
      $property->setAccessible(true);
      $property->setValue($tag, [
            $firstId => clone $selectedOption->setObjectId($firstId),
            $secondId => clone $unselectedOption->setObjectId($secondId),
            $thirdId => clone $selectedOption->setObjectId($thirdId)
      ]);

      $method = new ReflectionMethod(SelectBoxTag::class, 'removeSelectedOptions');
      $method->setAccessible(true);
      $method->invokeArgs($tag, [$firstId]);

      /* @var $children DomNode[] */
      $children = $property->getValue($tag);
      $this->assertEquals('selected', $children[$firstId]->getAttribute('selected'));
      $this->assertNull($children[$secondId]->getAttribute('selected'));
      $this->assertNull($children[$thirdId]->getAttribute('selected'));

   }

   /**
    * Tests resetting a select box including option groups.
    */
   public function testReset() {

      $tag = new SelectBoxTag();
      $tag->setContent('<select:option value="1" selected="selected">One</select:option>
<select:group label="foo">
   <group:option value="2" selected="selected">Two</group:option>
   <group:option value="3" selected="selected">Three</group:option>
</select:group>
<select:option value="4" selected="selected">Four</select:option>');
      $tag->onParseTime();
      $tag->onAfterAppend();

      // test whether all items are selected
      foreach ($tag->getChildren() as &$child) {
         if ($child instanceof SelectBoxGroupTag) {
            foreach ($child->getChildren() as &$optionChild) {
               $this->assertEquals('selected', $optionChild->getAttribute('selected'));
            }
         } else {
            $this->assertEquals('selected', $child->getAttribute('selected'));
         }
      }

      $tag->reset();

      // test whether all items are un-selected
      foreach ($tag->getChildren() as &$child) {
         if ($child instanceof SelectBoxGroupTag) {
            foreach ($child->getChildren() as &$optionChild) {
               $this->assertNull($optionChild->getAttribute('selected'));
            }
         } else {
            $this->assertNull($child->getAttribute('selected'));
         }
      }

   }

   /**
    * Tests whether validation is omitted with optional="true" and empty selection
    */
   public function testOptionalValidation() {

      $_REQUEST = [];

      $tag = new SelectBoxTag();
      $tag->setAttribute('optional', 'true');

      $tag->setContent('<select:option value="1">One</select:option>
<select:option value="2">Two</select:option>
<select:option value="3">Three</select:option>');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $button = new ButtonTag();
      $button->markAsSent();

      $validator = new SimpleSelectControlValidator($tag, $button);
      $tag->addValidator($validator);

      $this->assertTrue($tag->isValid());

   }

   /**
    * Tests whether validation for mandatory field works.
    */
   public function testMandatoryValidation() {

      $_REQUEST = [];
      $_POST = ['submit' => 'submit'];

      $form = new HtmlFormTag();
      $form->setAttributes([
            HtmlForm::METHOD_ATTRIBUTE_NAME => HtmlForm::METHOD_POST_VALUE_NAME,
            'name' => 'foo'
      ]);
      $form->setContent('<form:select name="foo">
<select:option value="1">One</select:option>
<select:option value="2">Two</select:option>
<select:option value="3">Three</select:option>
</form:select>
<form:button name="submit" value="submit" />
<form:addvalidator
   class="APF\tools\form\validator\SimpleSelectControlValidator"
   button="submit"
   control="foo"
/>');

      $form->onParseTime();
      $form->onAfterAppend();

      $this->assertFalse($form->isValid());
      $this->assertFalse($form->getFormElementByName('foo')->isValid());

   }

   /**
    * ID#319: test whether method complies w/ interface definition in case no option is present and/or selected.
    */
   public function testIsSelected() {

      $_REQUEST = [];
      $_POST = [];

      // use case 1: no values
      $select = new SelectBoxTag();
      $select->onParseTime();
      $select->onAfterAppend();

      $this->assertFalse($select->isSelected());
      $this->assertNull($select->getSelectedOption());

      // use case 2: values but no pre-selection
      $select = new SelectBoxTag();
      $select->setAttribute('name', 'foo');
      $select->setContent('<select:option value="One">One</select:option>
<select:option value="Two">Two</select:option>
<select:option value="Three">Three</select:option>');
      $select->onParseTime();
      $select->onAfterAppend();

      $this->assertFalse($select->isSelected());
      $this->assertNull($select->getSelectedOption());

      // use case 3: values and pre-selection
      $_REQUEST = ['foo' => 'Two'];
      $_POST = [];

      $select = new SelectBoxTag();
      $select->setAttribute('name', 'foo');
      $select->setContent('<select:option value="One">One</select:option>
<select:option value="Two">Two</select:option>
<select:option value="Three">Three</select:option>');
      $select->onParseTime();
      $select->onAfterAppend();

      $this->assertTrue($select->isSelected());
      $this->assertInstanceOf(SelectBoxOptionTag::class, $select->getSelectedOption());

   }

   /**
    * ID#324: test single option removal
    */
   public function testRemoveOption() {

      // test removal of static (single) options
      $select = $this->getSelectBoxTagForRemovalTest();

      $select->removeOption('2'); // selection by value
      $select->removeOption('Four'); // selection by display name

      $html = $select->transform();
      $this->assertContains('value="1"', $html);
      $this->assertNotContains('value="2"', $html);
      $this->assertContains('value="3"', $html);
      $this->assertNotContains('value="4"', $html);

      // test removal of dynamic (single) options
      $select = $this->getDynamicSelectBoxForRemovalTest();

      $select->removeOption('2'); // selection by value
      $select->removeOption('Four'); // selection by display name

      $html = $select->transform();
      $this->assertContains('value="1"', $html);
      $this->assertNotContains('value="2"', $html);
      $this->assertContains('value="3"', $html);
      $this->assertNotContains('value="4"', $html);

      // test removal of mixed (single) options
      $select = $this->getSelectBoxTagForRemovalTest();

      $select->addOption('Five', '5');
      $select->addOption('Six', '6');

      $select->removeOption('2'); // selection by value
      $select->removeOption('Five'); // selection by display name

      $html = $select->transform();
      $this->assertContains('value="1"', $html);
      $this->assertNotContains('value="2"', $html);
      $this->assertContains('value="3"', $html);
      $this->assertContains('value="4"', $html);
      $this->assertNotContains('value="5"', $html);
      $this->assertContains('value="6"', $html);

   }

   /**
    * ID#324: test group option removal
    */
   public function testRemoveGroupOptions() {

      // test removal of static (single) options
      $select = $this->getSelectBoxTagWithGroupForRemovalTest();

      $select->removeOption('2'); // selection by value
      $select->removeOption('Four'); // selection by display name

      $html = $select->transform();
      $this->assertContains('value="1"', $html);
      $this->assertNotContains('value="2"', $html);
      $this->assertContains('value="3"', $html);
      $this->assertNotContains('value="4"', $html);

      // test removal of dynamic (single) options
      $select = $this->getDynamicSelectBoxWithGroupForRemovalTest();

      $select->removeOption('2'); // selection by value
      $select->removeOption('Four'); // selection by display name

      $html = $select->transform();
      $this->assertContains('value="1"', $html);
      $this->assertNotContains('value="2"', $html);
      $this->assertContains('value="3"', $html);
      $this->assertNotContains('value="4"', $html);

      // test removal of mixed (single) options
      $select = $this->getSelectBoxTagWithGroupForRemovalTest();

      $select->addOption('Five', '5');
      $select->addGroupOption('Further', 'Six', '6');

      $select->removeOption('2'); // selection by value
      $select->removeOption('Five'); // selection by display name

      $html = $select->transform();
      $this->assertContains('value="1"', $html);
      $this->assertNotContains('value="2"', $html);
      $this->assertContains('value="3"', $html);
      $this->assertContains('value="4"', $html);
      $this->assertNotContains('value="5"', $html);
      $this->assertContains('value="6"', $html);

   }

   /**
    * ID#324: test all option removal
    */
   public function testRemoveAllOptions() {

      // test removal of all static (single) options
      $select = $this->getSelectBoxTagForRemovalTest();

      $select->removeAllOptions();

      $html = $select->transform();
      $this->assertEquals('<select name="foo"></select>', str_replace("\n", '', $html));
      $this->assertNotContains('value="1"', $html);
      $this->assertNotContains('value="2"', $html);
      $this->assertNotContains('value="3"', $html);
      $this->assertNotContains('value="4"', $html);

      // test removal of all dynamic (single) options
      $select = $this->getDynamicSelectBoxForRemovalTest();

      $select->removeAllOptions();

      $html = $select->transform();

      $this->assertEquals('<select name="foo"></select>', str_replace("\n", '', $html));
      $this->assertNotContains('value="1"', $html);
      $this->assertNotContains('value="3"', $html);
      $this->assertNotContains('value="2"', $html);
      $this->assertNotContains('value="4"', $html);

      // test remove of all mixed group options
      $select = $this->getSelectBoxTagWithGroupForRemovalTest();

      $select->addOption('Five', '5');
      $select->addGroupOption('Further', 'Six', '6');

      $select->removeAllOptions();

      $html = $select->transform();
      $this->assertEquals('<select name="foo"></select>', str_replace("\n", '', $html));
      $this->assertNotContains('value="1"', $html);
      $this->assertNotContains('value="3"', $html);
      $this->assertNotContains('value="2"', $html);
      $this->assertNotContains('value="4"', $html);
      $this->assertNotContains('value="5"', $html);
      $this->assertNotContains('value="6"', $html);

   }

   /**
    * @return SelectBoxTag
    */
   protected function getSelectBoxTagForRemovalTest() {
      $select = new SelectBoxTag();
      $select->setAttribute('name', 'foo');
      $select->setContent('<select:option value="1">One</select:option>
<select:option value="2">Two</select:option>
<select:option value="3">Three</select:option>
<select:option value="4">Four</select:option>');
      $select->onParseTime();
      $select->onAfterAppend();
      return $select;
   }

   /**
    * @return SelectBoxTag
    */
   protected function getSelectBoxTagWithGroupForRemovalTest() {
      $select = new SelectBoxTag();
      $select->setAttribute('name', 'foo');
      $select->setContent('<select:option value="1">One</select:option>
<select:option value="2">Two</select:option>
<select:group label="Further">
   <select:option value="3">Three</select:option>
   <select:option value="4">Four</select:option>
</select:group>');
      $select->onParseTime();
      $select->onAfterAppend();
      return $select;
   }

   /**
    * @return SelectBoxTag
    */
   protected function getDynamicSelectBoxForRemovalTest() {
      $select = new SelectBoxTag();
      $select->setAttribute('name', 'foo');
      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('One', '1');
      $select->addOption('Two', '2');
      $select->addOption('Three', '3');
      $select->addOption('Four', '4');
      return $select;
   }

   /**
    * @return SelectBoxTag
    */
   protected function getDynamicSelectBoxWithGroupForRemovalTest() {
      $select = new SelectBoxTag();
      $select->setAttribute('name', 'foo');
      $select->onParseTime();
      $select->onAfterAppend();

      $select->addOption('One', '1');
      $select->addOption('Two', '2');
      $select->addGroupOption('Further', 'Three', '3');
      $select->addGroupOption('Further', 'Four', '4');
      return $select;
   }

}
