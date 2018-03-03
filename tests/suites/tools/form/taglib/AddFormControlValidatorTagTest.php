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

use APF\core\pagecontroller\ParserException;
use APF\tools\form\FormException;
use APF\tools\form\taglib\AddFormControlValidatorTag;
use APF\tools\form\taglib\ButtonTag;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\taglib\TextFieldTag;
use APF\tools\form\validator\TextLengthValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

/**
 * Test validator construction and attachment to form controls.
 */
class AddFormControlValidatorTagTest extends TestCase {

   /**
    * Ensure form validator is constructed correctly (applying context and language) as well as
    * injected form control to validate and the button to trigger the validation.
    * @throws FormException
    * @throws ParserException
    * @throws ReflectionException
    */
   public function testFormValidatorSetup() {

      $controlName = 'test';
      $buttonName = 'send';

      $context = 'context';
      $language = 'test-lang';

      /* @var $form HtmlFormTag|MockObject */
      $form = $this->getMockBuilder(HtmlFormTag::class)
            ->setMethods(['getFormElementByName'])
            ->getMock();

      // Mock control to set expectations
      $control = new TextFieldTag();
      $control->setAttribute('name', $controlName);
      $control->setParentObject($form);
      $control->onParseTime();
      $control->onAfterAppend();

      // Mock button to allow granular control of test case
      $button = new ButtonTag();
      $button->setAttribute('name', $buttonName);
      $button->setAttribute('value', 'GO');
      $button->setParentObject($form);
      $button->onParseTime();
      $button->onAfterAppend();


      $form->method('getFormElementByName')
            ->will($this->returnValueMap([
                  [$controlName, $control],
                  [$buttonName, $button]
            ]));

      /* @var $addValidatorTag AddFormControlValidatorTag|MockObject */
      $addValidatorTag = $this->getMockBuilder(AddFormControlValidatorTag::class)
            ->setMethods(['getForm'])
            ->getMock();

      $addValidatorTag->method('getForm')
            ->willReturn($form);

      $addValidatorTag->setAttributes([
            'control' => $controlName,
            'button' => $buttonName,
            'class' => TextLengthValidator::class
      ]);

      $addValidatorTag->setContext($context);
      $addValidatorTag->setLanguage($language);

      $addValidatorTag->onParseTime();

      // Form validators are attached at onAfterAppend() to ensure form is completely built
      // up before creating (complex) validators.
      $this->assertEmpty($control->getValidators());

      $addValidatorTag->onAfterAppend();

      // Ensure validator has been attached and is of above expected type
      $this->assertNotEmpty($control->getValidators());
      $this->assertCount(1, $control->getValidators());

      $validator = $control->getValidators()[0];
      $this->assertInstanceOf(TextLengthValidator::class, $validator);

      // Ensure validator has access to the button to trigger validation
      $buttonProperty = new ReflectionProperty(TextLengthValidator::class, 'button');
      $buttonProperty->setAccessible(true);
      $this->assertEquals($button, $buttonProperty->getValue($validator));

      // Ensure validator has access to the control to validate
      $controlProperty = new ReflectionProperty(TextLengthValidator::class, 'control');
      $controlProperty->setAccessible(true);
      $this->assertEquals($control, $controlProperty->getValue($validator));

      // Ensure the validator has been created correctly
      $this->assertEquals($context, $validator->getContext());
      $this->assertEquals($language, $validator->getLanguage());

   }
}
