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
namespace APF\tests\suites\tools\form\filter;

use APF\core\pagecontroller\Document;
use APF\tools\form\filter\OnlyIntegersFilter;
use APF\tools\form\filter\OnlyLettersFilter;
use APF\tools\form\filter\String2LowerFilter;
use APF\tools\form\HtmlForm;
use APF\tools\form\taglib\HtmlFormTag;

/**
 * Tests filter capabilities for form controls.
 */
class FormFilterTest extends \PHPUnit_Framework_TestCase {

   /**
    * Test execution of statically assigned filter (assigned in form definition).
    */
   public function testStaticFilter() {

      // assume form sent with dedicated values
      $_GET = [];
      $_POST = ['send' => 'GO'];
      $_REQUEST = ['test' => 'Value-(/$§$&%/)=?~9876543456789'];

      $form = new HtmlFormTag();

      // inject parent object to make recursive selection work
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttributes(['method' => HtmlForm::METHOD_POST_VALUE_NAME]);

      $form->setContent('<form:text name="test"/>
<form:button name="send" value="GO"/>
<form:addfilter 
   class="\APF\tools\form\filter\NoSpecialCharactersFilter" 
   button="send" 
   control="test"
/>');

      $form->onParseTime();
      $form->onAfterAppend();

      $this->assertEquals('Value-&9876543456789', $form->getFormElementByName('test')->getValue());
   }

   /**
    * Test execution of dynamically assigned filter (assigned in controller).
    */
   public function testDynamicFilter() {

      // assume form sent with dedicated values
      $_GET = [];
      $_POST = ['send' => 'GO'];
      $_REQUEST = ['test' => '123Test-(/$§456$&%/)=Foo?~789'];

      $form = new HtmlFormTag();

      // inject parent object to make recursive selection work
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttributes(['method' => HtmlForm::METHOD_POST_VALUE_NAME]);

      $form->setContent('<form:text name="test"/>
<form:button name="send" value="GO"/>');

      $form->onParseTime();
      $form->onAfterAppend();

      $control = $form->getFormElementByName('test');
      $button = $form->getFormElementByName('send');

      $control->addFilter(new OnlyIntegersFilter($control, $button));

      $this->assertEquals('123456789', $control->getValue());
   }

   /**
    * Test filtering one form control w/ multiple filters (assigned in form).
    */
   public function testMultipleStaticFilters() {

      // assume form sent with dedicated values
      $_GET = [];
      $_POST = ['send' => 'GO'];
      $_REQUEST = ['test' => 'Value-(/$§$%/)=?~123'];

      $form = new HtmlFormTag();

      // inject parent object to make recursive selection work
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttributes(['method' => HtmlForm::METHOD_POST_VALUE_NAME]);

      $form->setContent('<form:text name="test"/>
<form:button name="send" value="GO"/>
<form:addfilter 
   class="\APF\tools\form\filter\OnlyLettersFilter" 
   button="send" 
   control="test"
/>
<form:addfilter 
   class="\APF\tools\form\filter\String2LowerFilter" 
   button="send" 
   control="test"
/>');

      $form->onParseTime();
      $form->onAfterAppend();

      $this->assertEquals('value', $form->getFormElementByName('test')->getValue());

   }

   /**
    * Test filtering one form control w/ multiple filters (assigned in controller).
    */
   public function testMultipleDynamicFilters() {


      // assume form sent with dedicated values
      $_GET = [];
      $_POST = ['send' => 'GO'];
      $_REQUEST = ['test' => 'Value-(/$§$%/)=?~123'];

      $form = new HtmlFormTag();

      // inject parent object to make recursive selection work
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttributes(['method' => HtmlForm::METHOD_POST_VALUE_NAME]);

      $form->setContent('<form:text name="test"/>
<form:button name="send" value="GO"/>');

      $form->onParseTime();
      $form->onAfterAppend();

      $control = $form->getFormElementByName('test');
      $button = $form->getFormElementByName('send');

      // proof only one filter is executed at a time
      $control->addFilter(new OnlyLettersFilter($control, $button));
      $this->assertEquals('Value', $form->getFormElementByName('test')->getValue());

      // proof second filter is executed on demand using output of first run
      $control->addFilter(new String2LowerFilter($control, $button));
      $this->assertEquals('value', $form->getFormElementByName('test')->getValue());

   }

}
