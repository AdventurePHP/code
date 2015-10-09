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
use APF\core\pagecontroller\ParserException;
use APF\core\pagecontroller\PlaceHolderTag;
use APF\core\pagecontroller\TemplateTag;
use APF\modules\usermanagement\pres\documentcontroller\registration\RegistrationController;
use InvalidArgumentException;
use ReflectionMethod;

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

      $method = $this->getMethod();
      $document = new Document();

      $filePath = $method->invokeArgs($document, array(self::VENDOR . '\foo', 'bar'));
      assertEquals(self::SOURCE_PATH . '/foo/bar.html', $filePath);

      $filePath = $method->invokeArgs($document, array(self::VENDOR . '\foo\bar', 'baz'));
      assertEquals(self::SOURCE_PATH . '/foo/bar/baz.html', $filePath);

   }

   /**
    * @return ReflectionMethod The <em>APF\core\pagecontroller\Document::getTemplateFilePath()</em> method.
    */
   private function getMethod() {
      $method = new ReflectionMethod(Document::class, 'getTemplateFilePath');
      $method->setAccessible(true);

      return $method;
   }

   public function testWithVendorOnly() {
      $filePath = $this->getMethod()->invokeArgs(new Document(), array(self::VENDOR, 'foo'));
      assertEquals(self::SOURCE_PATH . '/foo.html', $filePath);
   }

   public function testGetChildNode() {
      $doc = new TemplateTag();
      $doc->setContent('<html:template name="foo">bar</html:template>');
      $doc->onParseTime();
      $template = $doc->getChildNode('name', 'foo', TemplateTag::class);
      $this->assertNotNull($template);
      $this->assertEquals('bar', $template->getContent());
   }

   public function testGetChildNodeWithException() {
      $this->setExpectedException(InvalidArgumentException::class);
      $doc = new Document();
      $doc->onParseTime();
      $doc->getChildNode('foo', 'bar', Document::class);
   }

   public function testGetChildNodes() {
      $doc = new TemplateTag();
      $doc->setContent('<html:placeholder name="foo" /><html:placeholder name="foo" /><html:placeholder name="foo" />');
      $doc->onParseTime();
      $placeHolders = $doc->getChildNodes('name', 'foo', PlaceHolderTag::class);
      $this->assertEquals(3, count($placeHolders));
      $this->assertEquals('foo', $placeHolders[0]->getAttribute('name'));
   }

   public function testGetChildNodesWithException() {
      $this->setExpectedException('\InvalidArgumentException');
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

      $this->setExpectedException(ParserException::class);

      $doc = new TemplateTag();
      $doc->setContent('<html:placeholder name="tes>t"/');
      $doc->onParseTime();

   }

   public function testInvalidTemplateSyntaxWithTagClosingSignInAttribute2() {

      $this->setExpectedException(ParserException::class);

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

   protected function setUp() {
      RootClassLoader::addLoader(new StandardClassLoader(self::VENDOR, self::SOURCE_PATH));
   }

}
