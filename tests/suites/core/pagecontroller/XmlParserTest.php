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
namespace APF\tests\suites\core\pagecontroller;

use APF\core\pagecontroller\ParserException;
use APF\core\pagecontroller\XmlParser;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * Tests the XmlParser's capabilities.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 23.12.2013<br />
 */
class XmlParserTest extends TestCase {

   public function testSelfClosingTagString() {

      // basic tag attributes
      $prefix = 'core';
      $name = 'importdesign';

      $namespaceAttribute = 'namespace';
      $namespaceValue = 'VENDOR\foo\bar';
      $templateAttribute = 'template';
      $templateValue = 'baz';

      // assemble expected result
      $expectedAttributes = [
            $namespaceAttribute => $namespaceValue,
            $templateAttribute => $templateValue

      ];
      $expectedContent = '';

      // test simple on-liner
      $tagString = '<' . $prefix . ':' . $name . ' ' . $namespaceAttribute . '="' . $namespaceValue . '" ' . $templateAttribute . '="' . $templateValue . '" />';
      $attributes = XmlParser::getTagAttributes($prefix, $name, $tagString);
      $this->assertEquals($expectedAttributes, $attributes['attributes']);
      $this->assertEquals($expectedContent, $attributes['content']);

      // test multi-liner
      $tagString = '<' . $prefix . ':' . $name . ' ' . PHP_EOL . $namespaceAttribute . '="' . $namespaceValue . '" ' . $templateAttribute . '="' . $templateValue . '"' . PHP_EOL . '/>';
      $attributes = XmlParser::getTagAttributes($prefix, $name, $tagString);
      $this->assertEquals($expectedAttributes, $attributes['attributes']);
      $this->assertEquals($expectedContent, $attributes['content']);

   }

   public function testExplicitClosingTagString() {

      // basic tag attributes
      $prefix = 'html';
      $name = 'template';

      $nameAttribute = 'name';
      $nameValue = 'MyTemplate';

      // assemble expected result
      $expectedAttributes = [
            $nameAttribute => $nameValue
      ];
      $expectedContent = '<p>My name is <template:placeholder name="Name" />! I am 20 years old and I live in Berlin.</p>';

      // test simple on-liner
      $tagString = '<' . $prefix . ':' . $name . ' ' . $nameAttribute . '="' . $nameValue . '">' . $expectedContent . '</' . $prefix . ':' . $name . '>';
      $attributes = XmlParser::getTagAttributes($prefix, $name, $tagString);
      $this->assertEquals($expectedAttributes, $attributes['attributes']);
      $this->assertEquals($expectedContent, $attributes['content']);

      // test multi-liner
      $tagString = '<' . $prefix . ':' . $name . ' ' . PHP_EOL . $nameAttribute . '="' . $nameValue . '">' . PHP_EOL . $expectedContent . PHP_EOL . '</' . $prefix . ':' . $name . '>';
      $attributes = XmlParser::getTagAttributes($prefix, $name, $tagString);
      $this->assertEquals($expectedAttributes, $attributes['attributes']);
      $this->assertEquals(PHP_EOL . $expectedContent . PHP_EOL, $attributes['content']);

      // test whether parer preserves content as-is
      $expectedContent = '<p>
      My name is <template:placeholder name="Name" />!
      I am 20 years old and I live in Berlin.
      </p>';

      $tagString = '<' . $prefix . ':' . $name . ' ' . PHP_EOL . $nameAttribute . '="' . $nameValue . '">' . PHP_EOL . $expectedContent . PHP_EOL . '</' . $prefix . ':' . $name . '>';
      $attributes = XmlParser::getTagAttributes($prefix, $name, $tagString);
      $this->assertEquals($expectedAttributes, $attributes['attributes']);
      $this->assertEquals(PHP_EOL . $expectedContent . PHP_EOL, $attributes['content']);

   }

   public function testExplicitClosingTagStringWithNestedTags() {

      // basic tag attributes
      $prefix = 'fs';
      $name = 'structure';

      $tagString = '<' . $prefix . ':' . $name . ' key="foo">

   <' . $prefix . ':' . $name . '>
      <' . $prefix . ':' . $name . ' key="start">12345</' . $prefix . ':' . $name . '>
      <' . $prefix . ':' . $name . ' key="end">67890</' . $prefix . ':' . $name . '>
      <' . $prefix . ':' . $name . ' key="message">
         ...
      </' . $prefix . ':' . $name . '>
   </' . $prefix . ':' . $name . '>

</' . $prefix . ':' . $name . '>';

      // assemble expected result
      $expectedAttributes = [
            'key' => 'foo'
      ];
      $expectedContent = '

   <' . $prefix . ':' . $name . '>
      <' . $prefix . ':' . $name . ' key="start">12345</' . $prefix . ':' . $name . '>
      <' . $prefix . ':' . $name . ' key="end">67890</' . $prefix . ':' . $name . '>
      <' . $prefix . ':' . $name . ' key="message">
         ...
      </' . $prefix . ':' . $name . '>
   </' . $prefix . ':' . $name . '>

';
      $attributes = XmlParser::getTagAttributes('fs', 'structure', $tagString);
      $this->assertEquals($expectedAttributes, $attributes['attributes']);
      $this->assertEquals($expectedContent, $attributes['content']);

   }

   public function testMissingEndTag() {
      $this->expectException(ParserException::class);
      XmlParser::getTagAttributes('foo', 'bar', '<foo:bar bar="baz"');
   }

   public function testWrongMethodCall() {

      $message = 'Test returned without a ParserException';

      try {
         XmlParser::getTagAttributes('foo', 'bar', '');
         $this->fail($message);
      } catch (ParserException $e) {
         $this->assertTrue(true);
      }

      try {
         XmlParser::getTagAttributes('foo', '', '<foo:bar bar="baz" ');
         $this->fail($message);
      } catch (ParserException $e) {
         $this->assertTrue(true);
      }

      try {
         XmlParser::getTagAttributes('', 'bar', '<foo:bar bar="baz" ');
         $this->fail($message);
      } catch (ParserException $e) {
         $this->assertTrue(true);
      }

      try {
         XmlParser::getTagAttributes('', 'bar', '<foo:bar bar="baz"></foo:bar>');
         $this->fail($message);
      } catch (ParserException $e) {
         $this->assertTrue(true);
      }

      try {
         XmlParser::getTagAttributes('foo', '', '<foo:bar bar="baz"></foo:bar>');
         $this->fail($message);
      } catch (ParserException $e) {
         $this->assertTrue(true);
      }

   }

   public function testTagClosingSignInAttribute() {

      $expressionAttributeName = 'expression';

      $expressions = [
            'foo',
            'foo-bar',
            'model[0]',
            'model[0]->getFoo()',
            'model->getFoo()->getBar()->getBaz()',
            'model->getFoo()->getBar()[5]',
            'self->getBar()[2]->getFoo()[3]->getBaz()'
      ];

      foreach ($expressions as $expression) {
         $selfClosingTagString = '<dyn:expr ' . PHP_EOL . '   ' . $expressionAttributeName . '="' . $expression . '"' . PHP_EOL . '/>';
         $attributes = XmlParser::getTagAttributes('dyn', 'expr', $selfClosingTagString);

         $this->assertEquals($attributes['attributes'][$expressionAttributeName], $expression);

         $explicitClosingTagString = '<dyn:expr ' . PHP_EOL . '   ' . $expressionAttributeName . '="' . $expression . '"' . PHP_EOL . '>'
               . PHP_EOL . 'this is tag content ' . PHP_EOL
               . '</dyn:expr>';
         $attributes = XmlParser::getTagAttributes('dyn', 'expr', $explicitClosingTagString);

         $this->assertEquals($attributes['attributes'][$expressionAttributeName], $expression);

      }

   }

   public function testGenerateUniqueId() {

      // reset counter to avoid influence from other unit tests using this static counter
      $counter = new ReflectionProperty(XmlParser::class, 'domNodeCounter');
      $counter->setAccessible(true);

      $original = $counter->getValue();
      $counter->setValue(1);

      // test starting point and format
      $id = XmlParser::generateUniqID();
      $this->assertTrue(strlen($id) === 32);
      $this->assertEquals('node-000000000000000000000000001', $id);

      // test incrementation by 123 => expect counter at 124
      for ($i = 1; $i < 124; $i++) {
         $id = XmlParser::generateUniqID();
      }

      $this->assertEquals('node-000000000000000000000000124', $id);

      // reset to state before the test
      $counter->setValue($original);
   }

}
