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
namespace APF\tests\suites\core\pagecontroller;

use APF\core\expression\taglib\ExpressionEvaluationTag;
use APF\core\loader\RootClassLoader;
use APF\core\loader\StandardClassLoader;
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\DomNode;
use APF\core\pagecontroller\ParserException;
use APF\core\pagecontroller\PlaceHolderTag;
use APF\core\pagecontroller\Template;
use APF\core\pagecontroller\TemplateTag;
use APF\modules\usermanagement\pres\documentcontroller\registration\RegistrationController;
use APF\tests\suites\core\pagecontroller\expression\TestTemplateExpressionOne;
use APF\tests\suites\core\pagecontroller\expression\TestTemplateExpressionTwo;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Tests the <em>Document::getTemplateFilePath()</em> regarding class loader usage.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.02.2014<br />
 */
class DocumentTest extends \PHPUnit_Framework_TestCase {

   const VENDOR = 'VENDOR';
   const SOURCE_PATH = '/var/www/html/src';

   public function testWithNormalNamespace() {

      $method = $this->getFilePathMethod();
      $document = new Document();

      $filePath = $method->invokeArgs($document, [self::VENDOR . '\foo', 'bar']);
      $this->assertEquals(self::SOURCE_PATH . '/foo/bar.html', $filePath);

      $filePath = $method->invokeArgs($document, [self::VENDOR . '\foo\bar', 'baz']);
      $this->assertEquals(self::SOURCE_PATH . '/foo/bar/baz.html', $filePath);

   }

   /**
    * @return ReflectionMethod The <em>APF\core\pagecontroller\Document::getTemplateFilePath()</em> method.
    */
   private function getFilePathMethod() {
      $method = new ReflectionMethod(Document::class, 'getTemplateFilePath');
      $method->setAccessible(true);

      return $method;
   }

   public function testWithVendorOnly() {
      $filePath = $this->getFilePathMethod()->invokeArgs(new Document(), [self::VENDOR, 'foo']);
      $this->assertEquals(self::SOURCE_PATH . '/foo.html', $filePath);
   }

   public function testGetChildNode() {
      $doc = new TemplateTag();
      $doc->setContent('<html:template name="foo">bar</html:template>');
      $doc->onParseTime();
      $template = $doc->getChildNode('name', 'foo', TemplateTag::class);
      $this->assertNotNull($template);
      $this->assertEquals('bar', $template->getContent());

      // ensure that a reference is returned instead of a clone or copy
      $children = $doc->getChildren();
      $this->assertEquals(
            spl_object_hash($template),
            spl_object_hash($children[array_keys($children)[0]])
      );
   }

   public function testGetChildNodeWithException() {
      $this->expectException(InvalidArgumentException::class);
      $doc = new Document();
      $doc->onParseTime();
      $doc->getChildNode('foo', 'bar', Document::class);
   }

   public function testGetChildNodeIfExists() {
      $doc = new TemplateTag();
      $doc->setContent('<html:template name="foo">bar</html:template>');
      $doc->onParseTime();
      $template = $doc->getChildNodeIfExists('name', 'foo', TemplateTag::class);
      $this->assertNotNull($template);
      $this->assertEquals('bar', $template->getContent());

      // ensure that a reference is returned instead of a clone or copy
      $children = $doc->getChildren();
      $this->assertEquals(
            spl_object_hash($template),
            spl_object_hash($children[array_keys($children)[0]])
      );
   }

   public function testGetChildNodeIfExistsErrorCase() {
      $doc = new Document();
      $doc->onParseTime();
      $this->assertNull($doc->getChildNodeIfExists('foo', 'bar', Document::class));
   }

   public function testGetChildNodes() {
      $doc = new TemplateTag();
      $doc->setContent('<html:placeholder name="foo" /><html:placeholder name="foo" /><html:placeholder name="foo" />');
      $doc->onParseTime();
      /* @var $placeHolders DomNode[] */
      $placeHolders = $doc->getChildNodes('name', 'foo', PlaceHolderTag::class);
      $this->assertEquals(3, count($placeHolders));
      $this->assertEquals('foo', $placeHolders[0]->getAttribute('name'));

      // ensure that a reference is returned instead of a clone or copy
      $children = $doc->getChildren();
      $keys = array_keys($children);

      for ($i = 0; $i < 3; $i++) {
         $this->assertEquals(
               spl_object_hash($placeHolders[$i]),
               spl_object_hash($children[$keys[$i]])
         );
      }
   }

   public function testGetChildNodesWithException() {
      $this->expectException(InvalidArgumentException::class);
      $doc = new Document();
      $doc->getChildNodes('foo', 'bar', Document::class);
   }

   public function testDocumentControllerParsingTest() {

      $controllerClass = RegistrationController::class;

      // rear cases
      $this->executeControllerTest('<@controller class="' . $controllerClass . '"@>', $controllerClass);
      $this->executeControllerTest('<@controller class="' . $controllerClass . '" @>', $controllerClass);
      $this->executeControllerTest('<@controller class="' . $controllerClass . '"' . "\n" . '@>', $controllerClass);
      $this->executeControllerTest('<@controller class="' . $controllerClass . '"' . "\n" . ' @>', $controllerClass);
      $this->executeControllerTest('<@controller class="' . $controllerClass . '"  ' . "\n" . '  @>', $controllerClass);
      $this->executeControllerTest('<@controller class="' . $controllerClass . '"  ' . "\n\r" . '  @>', $controllerClass);

      // front cases
      $this->executeControllerTest('<@controller' . "\n" . 'class="' . $controllerClass . '" @>', $controllerClass);
      $this->executeControllerTest('<@controller' . " \n" . 'class="' . $controllerClass . '" @>', $controllerClass);
      $this->executeControllerTest('<@controller' . " \n" . '   class="' . $controllerClass . '" @>', $controllerClass);

      // mixed
      $this->executeControllerTest('<@controller' . "\n" . 'class="' . $controllerClass . '"@>', $controllerClass);
      $this->executeControllerTest('<@controller' . "\n" . 'class="' . $controllerClass . '"' . "\n" . '@>', $controllerClass);
      $this->executeControllerTest('<@controller' . " \n" . 'class="' . $controllerClass . '"' . "\n" . '@>', $controllerClass);
      $this->executeControllerTest('<@controller' . " \n" . '   class="' . $controllerClass . '" ' . "\n" . '@>', $controllerClass);

      $this->executeControllerTest('   <@controller' . " \n" . '   class="' . $controllerClass . '" ' . "\n" . '   @>', $controllerClass);
      $this->executeControllerTest('   <@controller' . " \n\r" . '   class="' . $controllerClass . '" ' . "\n\r" . '   @>', $controllerClass);

   }

   protected function executeControllerTest($content, $controllerClass) {

      $method = new ReflectionMethod(Document::class, 'extractDocumentController');
      $method->setAccessible(true);

      $doc = new Document();
      $doc->setContent($content);
      $method->invoke($doc);
      $this->assertEquals($controllerClass, get_class($doc->getDocumentController()));

   }

   public function testTagClosingSignInAttribute() {

      $doc = new TemplateTag();
      $doc->setData('model', [new TestDataModel(), new TestDataModel()]);

      $expressionOne = 'model[0]->getFoo()';
      $expressionTwo = 'model[1]->getBar()';
      $expressionThree = 'model[0]->getBaz()->getBar()';
      $expressionFour = 'model[1]->getBaz()->getBaz()->getFoo()';

      $doc->setContent('<core:addtaglib
   class="APF\core\expression\taglib\ExpressionEvaluationTag"
   prefix="dyn"
   name="expr"
/>
<dyn:expr
   name="one"
   expression="' . $expressionOne . '"
/>

<dyn:expr name="two" expression="' . $expressionTwo . '">
   Bar Baz
</dyn:expr>

<dyn:expr
   name="three"
   expression="' . $expressionThree . '"/>

<dyn:expr name="four" expression="' . $expressionFour . '"/>');

      $doc->onParseTime();

      $expressionNodeOne = $doc->getChildNode('name', 'one', ExpressionEvaluationTag::class);
      $expressionNodeTwo = $doc->getChildNode('name', 'two', ExpressionEvaluationTag::class);
      $expressionNodeThree = $doc->getChildNode('name', 'three', ExpressionEvaluationTag::class);
      $expressionNodeFour = $doc->getChildNode('name', 'four', ExpressionEvaluationTag::class);

      $this->assertEquals($expressionOne, $expressionNodeOne->getAttribute('expression'));
      $this->assertEquals($expressionTwo, $expressionNodeTwo->getAttribute('expression'));
      $this->assertEquals($expressionThree, $expressionNodeThree->getAttribute('expression'));
      $this->assertEquals($expressionFour, $expressionNodeFour->getAttribute('expression'));

      $this->assertEquals('foo', $expressionNodeOne->transform());
      $this->assertEquals('bar', $expressionNodeTwo->transform());
      $this->assertEquals('bar', $expressionNodeThree->transform());
      $this->assertEquals('foo', $expressionNodeFour->transform());

   }

   public function testInvalidTemplateSyntaxWithTagClosingSignInAttribute1() {

      $this->expectException(ParserException::class);

      $doc = new TemplateTag();
      $doc->setContent('<html:placeholder name="tes>t"/');
      $doc->onParseTime();

   }

   public function testInvalidTemplateSyntaxWithTagClosingSignInAttribute2() {

      $this->expectException(ParserException::class);

      $doc = new TemplateTag();
      $doc->setContent('<html:placeholder name="test" /');
      $doc->onParseTime();

   }

   public function testControllerAccessFromDocument() {

      $doc = new Document();
      $doc->setContent('<@controller class="APF\tests\suites\core\pagecontroller\TestDocumentController"@>
<core:addtaglib
   prefix="read-from"
   name="controller-tag"
   class="APF\tests\suites\core\pagecontroller\ReadValueFromControllerTag"
/>
<read-from:controller-tag />');

      $extractDocConMethod = new ReflectionMethod(Document::class, 'extractDocumentController');
      $extractDocConMethod->setAccessible(true);
      $extractDocConMethod->invoke($doc);

      $extractTagLibsMethod = new ReflectionMethod(Document::class, 'extractTagLibTags');
      $extractTagLibsMethod->setAccessible(true);
      $extractTagLibsMethod->invoke($doc);

      $result = $doc->transform();

      $this->assertEquals(TestDocumentController::VALUE, trim($result));

   }

   /**
    * Tests whether the parser ignores an HTML comment such as <em>&lt;!-- foo:bar --&gt;</em> going
    * through the document.
    * <p/>
    * See https://adventure-php-framework.org/tracker/view.php?id=238 for details.
    */
   public function testHtmlCommentWithTagNotation() {

      $doc = new Document();
      $doc->setContent('This is the content of a document with tags and comments:

<!-- app:footer -->

This is text after a comment...

<html:placeholder name="foo" />

This is text after a place holder...
');

      try {
         $this->getParserMethod()->invoke($doc);

         $placeHolder = $doc->getChildNode('name', 'foo', PlaceHolderTag::class);
         $this->assertTrue($placeHolder instanceof PlaceHolderTag);
      } catch (Exception $e) {
         $this->fail('Parsing comments failed. Message: ' . $e->getMessage());
      }

   }

   /**
    * @return ReflectionMethod
    */
   protected function getParserMethod() {
      $method = new ReflectionMethod(Document::class, 'extractTagLibTags');
      $method->setAccessible(true);

      return $method;
   }

   /**
    * Tests parser capabilities with <em>&lt;li&gt;FOO:</em> statements in e.g. HTML lists.
    */
   public function testClosingBracket() {

      $doc = new Document();
      $doc->setContent('<p>
   This is the content of a document with tags and lists:
</p>
<ul>
   <li>Foo: Foo is the first part of the &quot;foo bar&quot; phrase.</li>
   <li>Bar: Bar is the second part of the &quot;foo bar&quot; phrase.</li>
</ul>
<p>
 This is text after a list...
</p>
<html:placeholder name="foo" />
<p>
   This is text after a place holder...
</p>
');

      try {
         $this->getParserMethod()->invoke($doc);

         $placeHolder = $doc->getChildNode('name', 'foo', PlaceHolderTag::class);
         $this->assertTrue($placeHolder instanceof PlaceHolderTag);
      } catch (Exception $e) {
         $this->fail('Parsing lists failed. Message: ' . $e->getMessage());
      }

   }

   /**
    * Tests whether the parser ignores "normal" HTML code with colons (":") in tag attributes.
    * <p/>
    * See https://adventure-php-framework.org/tracker/view.php?id=266 for details.
    */
   public function testColonsInTagAttributes() {

      $doc = new Document();
      $doc->setContent(
            '<p>
   This is static content...
</p>
<p>
   To quit your session, please <a href="/?:action=logout">Logout</a>
</p>
<p>
   This is static content...
</p>'
      );

      try {
         $this->getParserMethod()->invoke($doc);
         $this->assertEmpty($doc->getChildren());
      } catch (Exception $e) {
         $this->fail('Parsing HTML failed. Message: ' . $e->getMessage());
      }
   }

   public function testTransformation() {

      $doc = new Document();
      $doc->setContent('<html:placeholder name="test" />');

      $method = new ReflectionMethod('APF\core\pagecontroller\Document', 'extractTagLibTags');
      $method->setAccessible(true);
      $method->invoke($doc);

      $expected = 'foo';

      /* @var $placeHolder PlaceHolderTag */
      $doc->setPlaceHolder('test', $expected);
      $placeHolder = $doc->getChildNode('name', 'test', 'APF\core\pagecontroller\PlaceHolderTag');
      $placeHolder->setContent($expected);

      $this->assertEquals($expected, $doc->transform());

   }

   /**
    * Test no exception raised with not-existing place holder specified.
    */
   public function testSetPlaceHolder1() {
      try {
         $this->getTemplateWithPlaceHolder()->setPlaceHolder('foo', 'bar');
      } catch (Exception $e) {
         $this->fail($e);
      }
   }

   protected function getTemplateWithPlaceHolder($content = '<html:placeholder name="test"/>') {
      $doc = new TemplateTag();
      $doc->setContent($content);
      $doc->onParseTime();
      $doc->onAfterAppend();

      return $doc;
   }

   /**
    * Test simple existing place holder setting.
    */
   public function testSetPlaceHolder2() {
      $template = $this->getTemplateWithPlaceHolder();
      $expected = 'foo';
      $template->setPlaceHolder('test', $expected);
      $template->transformOnPlace();
      $this->assertEquals($expected, $template->transform());
   }

   /**
    * Test multiple place holders within one document.
    */
   public function testSetPlaceHolder3() {
      $template = $this->getTemplateWithPlaceHolder('<html:placeholder name="test"/><html:placeholder name="test"/>');
      $expected = 'foo';
      $template->setPlaceHolder('test', $expected);
      $template->transformOnPlace();
      $this->assertEquals($expected . $expected, $template->transform());
   }

   /**
    * Test place holder appending.
    */
   public function testSetPlaceHolder4() {
      $template = $this->getTemplateWithPlaceHolder();
      $expected = 'foo';
      $template->setPlaceHolder('test', $expected, true);
      $template->setPlaceHolder('test', $expected, true);
      $template->transformOnPlace();
      $this->assertEquals($expected . $expected, $template->transform());
   }

   /**
    * Test setPlaceHolders() with multiple place holders.
    */
   public function testSetPlaceHolder5() {

      // no place holders set
      $template = $this->getTemplateWithPlaceHolder('${foo}|${bar}|${baz}');
      $this->assertEquals('||', $template->transformTemplate());

      // one place holder set
      $template->setPlaceHolders(['bar' => '2']);
      $this->assertEquals('|2|', $template->transformTemplate());

      // all place holders set
      $template->setPlaceHolders(['foo' => '4', 'bar' => '5', 'baz' => '6']);
      $this->assertEquals('4|5|6', $template->transformTemplate());

      // test mixture of existing vs. non-existing place holders
      $template->clear();
      // TODO clear template will be tricky since within data attributes we cannot distinguish between place holder and other stuff
      // Maybe remember place holder names in a second array???
      $template->setPlaceHolders(['foo' => '4', 'non-existing' => '5', 'baz' => '6']);
      $this->assertEquals('4||6', $template->transformTemplate());
   }

   public function testGetNodeById() {
      $doc = $this->prepareDocumentForGetNodByIdTest();
      $placeHolder = $doc->getNodeById('baz');
      $this->assertNotNull($placeHolder);
      $this->assertEquals('baz', $placeHolder->getAttribute('name'));

      // ensure that a reference is returned instead of a clone or copy
      $children = $doc->getChildNode('name', 'foo', Template::class)
            ->getChildNode('name', 'bar', Template::class)
            ->getChildren();
      $this->assertEquals(
            spl_object_hash($placeHolder),
            spl_object_hash($children[array_keys($children)[0]])
      );
   }

   /**
    * @return TemplateTag Template with appropriate structure for testing method Document::getNodById().
    */
   protected function prepareDocumentForGetNodByIdTest() {
      $doc = new TemplateTag();
      $doc->setContent('
<html:template name="foo">
   <html:template name="bar">
      <html:placeholder name="baz" dom-id="baz" />
   </html:template>
</html:template>');
      $doc->onParseTime();

      return $doc;
   }

   public function testGetNodeByIdErrorCase() {
      $this->expectException(InvalidArgumentException::class);
      $doc = new TemplateTag();
      $doc->getNodeById('baz');
   }

   public function testGetNodeByIdIfExists() {
      $doc = $this->prepareDocumentForGetNodByIdTest();
      $placeHolder = $doc->getNodeByIdIfExists('baz');
      $this->assertNotNull($placeHolder);
      $this->assertEquals('baz', $placeHolder->getAttribute('name'));

      // ensure that a reference is returned instead of a clone or copy
      $children = $doc->getChildNode('name', 'foo', Template::class)
            ->getChildNode('name', 'bar', Template::class)
            ->getChildren();
      $this->assertEquals(
            spl_object_hash($placeHolder),
            spl_object_hash($children[array_keys($children)[0]])
      );
   }

   public function testGetNodeByIdIfExistsErrorCase() {
      $doc = new TemplateTag();
      $this->assertNull($doc->getNodeByIdIfExists('baz'));
   }

   /**
    * Happy case for template expressions.
    */
   public function testExtractExpressionTags1() {

      $doc = new TemplateTag();
      $doc->setContent('${placeHolder}|${dataAttribute[0]}');
      $doc->onParseTime();
      $doc->onAfterAppend();

      $doc->setPlaceHolder('placeHolder', 'foo');
      $doc->setData('dataAttribute', ['bar']);

      $this->assertEquals('foo|bar', $doc->transformTemplate());

   }

   /**
    * Test whether 1st expressions matches and document is *not* overwritten by second.
    */
   public function testExtractExpressionTags2() {

      $property = new ReflectionProperty(Document::class, 'knownExpressions');
      $property->setAccessible(true);

      // inject special conditions that apply for this
      $original = $property->getValue(null);
      $property->setValue(null, [TestTemplateExpressionOne::class, TestTemplateExpressionTwo::class]);

      // setup template
      $doc = new TemplateTag();
      $doc->setContent('${specialExpression1}|${specialExpression2}');
      $doc->onParseTime();
      $doc->onAfterAppend();

      $children = $doc->getChildren();

      $this->assertCount(2, $children);
      $this->assertEquals(TestTemplateExpressionOne::class, $children[array_keys($children)[0]]->getAttribute('expression'));

      // reset to original setup
      $property->setValue(null, $original);

   }

   public function testExtractExpressionTags3() {
      $this->expectException(ParserException::class);
      $doc = new TemplateTag();
      $doc->setContent('${expression');
      $doc->onParseTime();
      $doc->onAfterAppend();
   }

   public function testExtractExpressionTags4() {

      $property = new ReflectionProperty(Document::class, 'knownExpressions');
      $property->setAccessible(true);

      // inject special conditions that apply for this
      $original = $property->getValue(null);
      $property->setValue(null, []);

      // setup template
      $doc = new TemplateTag();
      $doc->setContent('${expression}');
      $doc->onParseTime();

      try {
         $doc->onAfterAppend();
         $this->fail('knownExpressions() should throw a ParserException in case no expression applies!');
      } catch (ParserException $e) {
         // this is expected behavior
      }

      // reset to original setup
      $property->setValue(null, $original);
   }

   public function testAddAttribute() {

      $doc = new Document();

      $name = 'foo';
      $glue = '-';
      $this->assertEquals(null, $doc->getAttribute($name));

      $doc->addAttribute($name, '', $glue);
      $this->assertEquals('', $doc->getAttribute($name));

      $doc->addAttribute($name, 'foo', $glue);
      $this->assertEquals('foo', $doc->getAttribute($name));

      $result = $doc->addAttribute($name, 'foo', $glue);
      $this->assertEquals('foo-foo', $doc->getAttribute($name));

      $this->assertEquals($doc, $result);
   }

   /**
    * ID#300: Test whether parser detects line breaks in tag definitions after tag name as problem!
    */
   public function testLineBreakInTagDefinition() {

      $this->expectException(ParserException::class);

      Document::addTagLib(TemplateTag::class, 'a', 'foo');
      Document::addTagLib(TemplateTag::class, 'b', 'foo');
      Document::addTagLib(TemplateTag::class, 'c', 'foo');

      $tag = new TemplateTag();
      $tag->setContent('<a:foo>
<b:foo
attr1="1"
attr2="2">
<c:foo key="a">a</c:foo>
<c:foo key="b">b</c:foo>
<c:foo key="c">c</c:foo>
</b:foo>
</a:foo>');
      $tag->onParseTime();

   }

   /**
    * ID#147: Tests template expression configuration.
    */
   public function testTemplateExpressionConfiguration() {

      $doc = new ReflectionClass(Document::class);
      $property = $doc->getProperty('knownExpressions');
      $property->setAccessible(true);

      // Initial bootstrap process registers two standard expressions.
      $expressions = $property->getValue();
      $this->assertCount(2, $expressions);

      // Clearing the list should result in zero entries.
      Document::clearTemplateExpressions();
      $this->assertCount(0, $property->getValue());

      // Adding previously registered expressions to not influence other tests.
      foreach ($expressions as $expression) {
         Document::addTemplateExpression($expression);
      }

      $this->assertCount(2, $property->getValue());

   }

   protected function setUp() {
      RootClassLoader::addLoader(new StandardClassLoader(self::VENDOR, self::SOURCE_PATH));
   }

}
