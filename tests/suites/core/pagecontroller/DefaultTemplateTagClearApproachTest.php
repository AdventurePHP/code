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

use APF\core\pagecontroller\TemplateTag;
use PHPUnit\Framework\TestCase;

/**
 * Tests the template clear mechanism available within the TemplateTag.
 */
class DefaultTemplateTagClearApproachTest extends TestCase {

   /**
    * Test w/o place holder should not fail.
    */
   public function testTemplateWithoutPlaceHolders() {
      $template = new TemplateTag();
      $template->clear();
   }

   public function testPlaceHolders() {
      $template = new TemplateTag();
      $template->setContent('${foo}|${bar}');
      $template->onParseTime();
      $template->onAfterAppend();

      $template->setPlaceHolders(['foo' => '1', 'bar' => '2']);

      $this->assertEquals('1|2', $template->transformTemplate());

      $template->clear();

      $this->assertEquals('|', $template->transformTemplate());
   }

   public function testConditionalPlaceHolders() {
      $template = new TemplateTag();
      $template->setContent('<cond:placeholder name="foo">${content}</cond:placeholder>|'
            . '<cond:placeholder name="bar">${content}</cond:placeholder>');
      $template->onParseTime();
      $template->onAfterAppend();

      $template->setPlaceHolders(['foo' => '1', 'bar' => '2']);

      $this->assertEquals('1|2', $template->transformTemplate());

      $template->clear();

      $this->assertEquals('|', $template->transformTemplate());
   }

}
