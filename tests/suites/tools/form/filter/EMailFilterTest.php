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
use APF\tools\form\taglib\HtmlFormTag;
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

   protected function getForm() {

      $form = new HtmlFormTag();
      $form->setParentObject(new Document());

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
