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

use APF\tools\form\taglib\HtmlFormTag;
use PHPUnit\Framework\TestCase;

class DependentFormControlsTest extends TestCase {

   /**
    * Tests AbstractFormControl::getDependentFields()
    */
   public function testHideDependentFormControls() {

      $form = new HtmlFormTag();
      $form->setContent(
            '<form:label name="label1" for="text-field">Label 1</form:label>'
            . '<form:label name="label2" for="text-field">Label 2</form:label>'
            . '<form:text name="text-field" dependent-controls="label1|label2"/>'
      );
      $form->onParseTime();
      $form->onAfterAppend();

      // assume form controls are visible by default
      $this->assertTrue($form->getFormElementByName('text-field')->isVisible());
      $this->assertTrue($form->getFormElementByName('label1')->isVisible());
      $this->assertTrue($form->getFormElementByName('label2')->isVisible());

      // check whether hiding works for all dependent controls
      $text = $form->getFormElementByName('text-field');
      $text->hide();

      $this->assertFalse($form->getFormElementByName('text-field')->isVisible());
      $this->assertFalse($form->getFormElementByName('label1')->isVisible());
      $this->assertFalse($form->getFormElementByName('label2')->isVisible());

      $this->assertEquals('<form method="post" action=""></form>', $form->transformForm());

      // check whether controls can be displayed again
      $text->show();

      $this->assertTrue($form->getFormElementByName('text-field')->isVisible());
      $this->assertTrue($form->getFormElementByName('label1')->isVisible());
      $this->assertTrue($form->getFormElementByName('label2')->isVisible());

      // check whether specific visibility setting is overwritten by hiding text field
      $form->getFormElementByName('label1')->hide();

      $this->assertTrue($form->getFormElementByName('text-field')->isVisible());
      $this->assertFalse($form->getFormElementByName('label1')->isVisible());
      $this->assertTrue($form->getFormElementByName('label2')->isVisible());

      $text->hide();

      $this->assertFalse($form->getFormElementByName('text-field')->isVisible());
      $this->assertFalse($form->getFormElementByName('label1')->isVisible());
      $this->assertFalse($form->getFormElementByName('label2')->isVisible());

   }

}
