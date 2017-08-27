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
use APF\tools\form\filter\EMailFilter;
use APF\tools\form\taglib\ButtonTag;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\taglib\TextFieldTag;
use APF\tools\form\validator\EMailValidator;

class EMailFilterTest extends \PHPUnit_Framework_TestCase {

   public function testEMailFilter() {

      $email = '"§$te%&/st)(@example,"§$.com';
      $expected = 'test@example.com';

      $form = $this->getForm();

      $_POST['send'] = 'send';
      $_REQUEST['email'] = $email;

      $form->onParseTime();
      $form->onAfterAppend();

      $this->assertEquals($expected, $form->getFormElementByName('email')->getValue());
   }

   /**
    * Test whether e-mail filter prevents XSS attacks.
    */
   public function testXssInjection() {

      $filter = new EMailFilter(new TextFieldTag(), new ButtonTag());

      $this->assertEquals(
            'scriptalertThiscouldbeanXSSattackscript',
            $filter->filter('<script>alert("This could be an XSS attack!");</script>')
      );

   }

   protected function getForm() {

      $form = new HtmlFormTag();
      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttributes(['name' => 'filter-test']);
      $form->setContent('<form:text name="email" />
<form:button name="send" value="send" />
<form:addfilter
   class="' . EMailFilter::class . '"
   control="email"
   button="send"
/>
<form:addvalidator
   class="' . EMailValidator::class . '"
   control="email"
   button="send"
/>');

      return $form;
   }

}
