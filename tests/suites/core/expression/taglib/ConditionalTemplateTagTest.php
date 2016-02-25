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
namespace APF\tests\suites\core\expression\taglib;

use APF\core\pagecontroller\TemplateTag;

class ConditionalTemplateTagTest extends \PHPUnit_Framework_TestCase {

   public function testHideEntry() {
      $doc = $this->getTemplate();
      $doc->setData('teaser', new TeaserModel(false, false, false));

      $actual = $doc->transform();
      $this->assertEmpty($actual);
   }

   /**
    * @return TemplateTag
    */
   private function getTemplate() {

      $doc = new TemplateTag();
      $doc->setContent('<cond:template content-mapping="teaser" expression="content->displayIt()" condition="true()">
   <h2 class="...">${content->getHeadline()}</h2>
   <cond:template content-mapping="content" expression="content->getSubHeadline()" condition="notEmpty()">
      <h3 class="...">${content->getSubHeadline()}</h3>
   </cond:template>
   <p>${content->getText()}</p>
   <cond:template content-mapping="content->getMoreLink()" expression="content" condition="notEmpty()">
      <a href="${content->getUrl()}">${content->getLabel()}</a>
   </cond:template>
</cond:template>');
      $doc->onParseTime();
      $doc->onAfterAppend();

      $doc->transformOnPlace();

      return $doc;
   }

   public function testDisplayEntry() {
      $doc = $this->getTemplate();
      $model = new TeaserModel(true, false, false);
      $doc->setData('teaser', $model);

      $actual = $doc->transform();
      $this->assertContains('<h2 class="...">' . $model->getHeadline() . '</h2>', $actual);
      $this->assertContains('<p>' . $model->getText() . '</p>', $actual);
      $this->assertNotContains('<h3 class="..."', $actual);
      $this->assertNotContains('<a href="', $actual);
   }

   public function testDisplayEntryWithHeadline() {
      $doc = $this->getTemplate();
      $model = new TeaserModel(true, true, false);
      $doc->setData('teaser', $model);

      $actual = $doc->transform();

      echo $actual;

      $this->assertContains('<h2 class="...">' . $model->getHeadline() . '</h2>', $actual);
      $this->assertContains('<p>' . $model->getText() . '</p>', $actual);
      $this->assertContains('<h3 class="...">' . $model->getSubHeadline() . '</h3>', $actual);
      $this->assertNotContains('<a href="', $actual);
   }

   public function testDisplayEntryWithEverything() {
      $doc = $this->getTemplate();
      $model = new TeaserModel(true, true, true);
      $doc->setData('teaser', $model);

      $actual = $doc->transform();

      $this->assertContains('<h2 class="...">' . $model->getHeadline() . '</h2>', $actual);
      $this->assertContains('<p>' . $model->getText() . '</p>', $actual);
      $this->assertContains('<h3 class="...">' . $model->getSubHeadline() . '</h3>', $actual);
      $this->assertContains('<a href="' . $model->getMoreLink()->getUrl() . '">' . $model->getMoreLink()->getLabel() . '</a>', $actual);
   }

}
