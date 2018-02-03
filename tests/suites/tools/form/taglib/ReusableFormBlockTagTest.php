<?php
namespace APF\tests\suites\tools\form\taglib;

use APF\core\pagecontroller\ImportTemplateTag;
use APF\core\pagecontroller\ParserException;
use APF\tools\form\FormException;
use APF\tools\form\taglib\FormLabelTag;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\taglib\ReusableFormBlockTag;
use APF\tools\form\taglib\TextFieldTag;
use APF\tools\form\taglib\ValidationListenerTag;
use InvalidArgumentException;

/**
 * Tests the capabilities of the re-usable form block tag-
 */
class ReusableFormBlockTagTest extends \PHPUnit_Framework_TestCase {

   /**
    * @throws ParserException
    */
   public function testRequiredAttributeNamespace() {
      $this->expectException(InvalidArgumentException::class);
      (new ReusableFormBlockTag())->onParseTime();
   }

   /**
    * @throws ParserException
    */
   public function testRequiredAttributeTemplate() {
      $this->expectException(InvalidArgumentException::class);
      $tag = new ReusableFormBlockTag();
      $tag->setAttribute('namespace', __NAMESPACE__);
      $tag->onParseTime();
   }

   /**
    * @throws ParserException
    */
   public function testTemplateLoading() {
      $tag = new ReusableFormBlockTag();
      $tag->setAttributes([
            'namespace' => __NAMESPACE__ . '\templates',
            'template' => 'form-block-test-1'
      ]);
      $tag->onParseTime();

      $this->assertEquals('test', $tag->getContent());
   }

   /**
    * @throws ParserException
    */
   public function testPlaceHolderReplacement() {
      $tag = new ReusableFormBlockTag();
      $tag->setAttributes([
            'namespace' => __NAMESPACE__ . '\templates',
            'template' => 'form-block-test-2',
            'block-name' => 'foo', // place holder "name"
            'block-foo-bar' => 'baz', // place holder "foo-bar"
            'other' => 'attribute' // attribute will not be available as place holder
      ]);
      $tag->onParseTime();

      $this->assertEquals('foo:baz:${other}', $tag->getContent());
   }

   /**
    * @throws ParserException
    */
   public function testTagParsing() {

      $fieldName = 'foo';

      $tag = new ReusableFormBlockTag();
      $tag->setAttributes([
            'namespace' => __NAMESPACE__ . '\templates',
            'template' => 'form-block-test-3',
            'block-name' => $fieldName
      ]);
      $tag->onParseTime();

      // test processing of standard tags
      /* @var $textField TextFieldTag */
      $textField = $tag->getChildNode('name', $fieldName, TextFieldTag::class);
      $this->assertInstanceOf(TextFieldTag::class, $textField);
      $this->assertEquals('test', $textField->getValue());

      // test processing of template expressions
      $tag->onAfterAppend();
      $tag->setPlaceHolder('model', 'foo');
      $html = $tag->transform();
      $this->assertEquals('<input type="text" name="foo" value="test" />|foo', $html);

   }

   /**
    * @throws ParserException
    * @throws FormException
    */
   public function testComplexBlockDefinition() {

      $name = 'foo';
      $label = 'Example 1';
      $listener = 'Example Validator 3';

      $tag = new ReusableFormBlockTag();
      $tag->setAttributes([
            'namespace' => __NAMESPACE__ . '\templates',
            'template' => 'form-block-test-4',
            'block-name' => $name,
            'block-label' => $label,
            'block-listener' => $listener
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      // check form block setup
      $this->assertContains('<div class="form-group">', $tag->getContent());

      /* @var $label FormLabelTag */
      $labelTag = $tag->getFormElementByName($name . '-label');
      $this->assertInstanceOf(FormLabelTag::class, $labelTag);
      $this->assertEquals($label, $labelTag->getContent());

      /* @var $label TextFieldTag */
      $fieldTag = $tag->getFormElementByName($name);
      $this->assertInstanceOf(TextFieldTag::class, $fieldTag);
      $this->assertEquals($name, $fieldTag->getAttribute('id'));
      $this->assertEquals('form-control', $fieldTag->getAttribute('class'));

      /* @var $label ValidationListenerTag */
      $listenerTag = $tag->getFormElementByName($name . '-listener');
      $this->assertEquals($name, $listenerTag->getAttribute('control'));
      $this->assertContains('>' . $listener . '<', $listenerTag->getContent());

   }

   /**
    * @throws ParserException
    * @throws FormException
    */
   public function testValidation() {

      $_GET = [];
      $_REQUEST = [
            'send' => 'Save',
            'foo-1' => 'looooooong text',
            'foo-3' => 'looooooong text'
      ];
      $_POST = $_REQUEST;

      $tag = new ImportTemplateTag();
      $tag->setAttributes([
            'namespace' => __NAMESPACE__ . '\templates',
            'template' => 'form-block-test-5'
      ]);
      $tag->onParseTime();
      $tag->onAfterAppend();

      /* @var $form HtmlFormTag */
      $form = $tag->getChildNode('name', 'form', HtmlFormTag::class);

      $this->assertTrue($form->isSent());
      $this->assertFalse($form->isValid());

      $this->assertTrue($form->getFormElementByName('foo-1')->isValid());
      $this->assertFalse($form->getFormElementByName('foo-2')->isValid());
      $this->assertTrue($form->getFormElementByName('foo-3')->isValid());

      $form->transformOnPlace();
      $html = $tag->transform();

      $this->assertNotContains('<span class="help-block">Example Validator 1</span>', $html);
      $this->assertContains('<span class="help-block">Example Validator 2</span>', $html);

   }

}
