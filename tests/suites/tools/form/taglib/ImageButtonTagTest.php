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

use APF\tools\form\HtmlForm;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\taglib\ImageButtonTag;
use PHPUnit\Framework\TestCase;

class ImageButtonTagTest extends TestCase {

   const BUTTON_NAME = 'foo';
   const BUTTON_VALUE = 'bar';

   /**
    * @return ImageButtonTag
    */
   protected function getButton() {
      $button = new ImageButtonTag();
      $button->setAttribute('name', self::BUTTON_NAME);
      $button->setAttribute('value', self::BUTTON_VALUE);

      return $button;
   }

   /**
    * @param string $method
    *
    * @return HtmlFormTag
    */
   protected function getForm($method) {

      $form = new HtmlFormTag();
      $form->setAttribute('name', 'test-form');
      $form->setAttribute(HtmlForm::METHOD_ATTRIBUTE_NAME, $method);

      return $form;
   }

   public function testInitialFormLoad() {

      $_GET = [];
      $_POST = [];
      $_REQUEST = [];

      $form = $this->getForm(HtmlForm::METHOD_POST_VALUE_NAME);

      $button = $this->getButton();

      $button->setParent($form);

      $button->onParseTime();
      $button->onAfterAppend();

      $this->assertFalse($button->isSent());

   }

   public function testGetRequestWithPostDefined() {

      $_GET = [];
      $_POST = [];
      $_REQUEST = [];

      $_GET[self::BUTTON_NAME . '_x'] = self::BUTTON_NAME;
      $_GET[self::BUTTON_NAME . '_y'] = self::BUTTON_NAME;

      $form = $this->getForm(HtmlForm::METHOD_POST_VALUE_NAME);

      $button = $this->getButton();

      $button->setParent($form);

      $button->onParseTime();
      $button->onAfterAppend();

      $this->assertFalse($button->isSent());

   }

   public function testPostRequestWithPostDefined() {

      $_GET = [];
      $_POST = [];
      $_REQUEST = [];

      $_POST[self::BUTTON_NAME . '_x'] = self::BUTTON_VALUE;
      $_POST[self::BUTTON_NAME . '_y'] = self::BUTTON_VALUE;

      $form = $this->getForm(HtmlForm::METHOD_POST_VALUE_NAME);

      $button = $this->getButton();

      $button->setParent($form);

      $button->onParseTime();
      $button->onAfterAppend();

      $this->assertTrue($button->isSent());

   }

   public function testPostRequestWithGetDefined() {

      $_GET = [];
      $_POST = [];
      $_REQUEST = [];

      $_POST[self::BUTTON_NAME . '_x'] = self::BUTTON_VALUE;
      $_POST[self::BUTTON_NAME . '_y'] = self::BUTTON_VALUE;

      $form = $this->getForm('get');

      $button = $this->getButton();

      $button->setParent($form);

      $button->onParseTime();
      $button->onAfterAppend();

      $this->assertFalse($button->isSent());

   }

   public function testGetRequestWithGetDefined() {

      $_GET = [];
      $_POST = [];
      $_REQUEST = [];

      $_GET[self::BUTTON_NAME . '_x'] = self::BUTTON_VALUE;
      $_GET[self::BUTTON_NAME . '_y'] = self::BUTTON_VALUE;

      $form = $this->getForm('get');

      $button = $this->getButton();

      $button->setParent($form);

      $button->onParseTime();
      $button->onAfterAppend();

      $this->assertTrue($button->isSent());

   }

}
