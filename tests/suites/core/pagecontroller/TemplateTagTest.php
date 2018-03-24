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

use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\TemplateTag;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TemplateTagTest extends TestCase {

   protected function setUp() {
      // setup POST super global for form testing...
      if (!isset($_POST)) {
         $_POST = [];
      }
   }

   public function testGetForm1() {
      $template = new TemplateTag();
      $template->setContent('<html:form name="testFormName"><form:button name="testButtonName" /></html:form>');
      $template->onParseTime();
      $template->onAfterAppend();

      $this->assertNotNull($template->getForm('testFormName'));
   }

   public function testGetForm2() {
      $this->expectException(InvalidArgumentException::class);
      $template = new TemplateTag();

      $doc = new Document();
      $template->setParent($doc);

      $template->getForm('testFormName');
   }

   public function testGetTemplate1() {
      $template = new TemplateTag();
      $template->setContent('<html:template name="testTemplateName">foo</html:template>');
      $template->onParseTime();
      $template->onAfterAppend();

      $this->assertNotNull($template->getTemplate('testTemplateName'));
   }

   public function testGetTemplate2() {
      $this->expectException(InvalidArgumentException::class);

      $template = new TemplateTag();

      $doc = new Document();
      $template->setParent($doc);

      $template->getTemplate('testTemplateName');
   }

}
