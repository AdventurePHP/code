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

use APF\core\expression\taglib\ConditionalPlaceHolderTag;
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\DomNode;
use APF\core\pagecontroller\TemplateTag;
use APF\tests\suites\core\expression\LinkModel;

class ConditionalPlaceHolderTagTest extends \PHPUnit_Framework_TestCase {

   public function testSimplePlaceHolder() {

      // inject place holder data
      $tag = $this->getPlaceHolder('<h3>${content}</h3>', []);

      $text = 'test headline';
      $tag->setContent($text);

      $this->assertEquals('<h3>' . $text . '</h3>', $tag->transform());
   }

   protected function getPlaceHolder($content, array $attributes) {
      $tag = new ConditionalPlaceHolderTag();
      $tag->setAttributes($attributes);
      $tag->setContent($content);
      $tag->setParentObject(new Document());
      $tag->onParseTime();
      $tag->onAfterAppend();

      return $tag;
   }

   public function testPlaceHolderWithLengthCondition() {

      $tag = $this->getPlaceHolder('<h3>${content}</h3>', ['condition' => 'longerThan(10)']);

      // inject place holder data
      $text = 'test headline';
      $tag->setContent($text);

      $this->assertEquals('<h3>' . $text . '</h3>', $tag->transform());

      $tag = $this->getPlaceHolder('<h3>${content}</h3>', ['condition' => 'longerThan(10)']);

      // inject place holder data
      $text = 'headline';
      $tag->setContent($text);

      $this->assertEquals('', $tag->transform());
   }

   public function testEmptyOutputForMissingContent() {
      $tag = $this->getPlaceHolder('<h3>${content}</h3>', []);
      $this->assertEmpty($tag->transform());
   }

   public function testArrayContent() {

      $tag = $this->getPlaceHolder('<a href="${content[\'moreLink\']}">${content[\'moreLabel\']}</a>', []);

      $model = new LinkModel();
      $tag->setContent(['moreLabel' => $model->getLabel(), 'moreLink' => $model->getUrl()]);

      $this->assertEquals(
            '<a href="' . $model->getUrl() . '">' . $model->getLabel() . '</a>',
            $tag->transform()
      );

   }

   public function testViewModelContent() {
      $tag = $this->getPlaceHolder('<a href="${content->getUrl()}">${content->getLabel()}</a>', []);

      $model = new LinkModel();
      $tag->setContent($model);

      $this->assertEquals(
            '<a href="' . $model->getUrl() . '">' . $model->getLabel() . '</a>',
            $tag->transform()
      );
   }

   public function testComplexExample() {

      $doc = $this->getDocument();
      $doc->setPlaceHolder('name', '');

      $actual = $doc->transform();
      $this->assertContains('<p>No entry available.</p>', $actual);
      $this->assertNotContains('<p>Name: APF</p>', $actual);

      $doc = $this->getDocument();
      $doc->setPlaceHolder('name', 'APF');

      $actual = $doc->transform();
      $this->assertNotContains('<p>No entry available.</p>', $actual);
      $this->assertContains('<p>Name: APF</p>', $actual);

   }

   /**
    * @return DomNode
    */
   protected function getDocument() {

      $doc = new TemplateTag();
      $doc->setContent('<cond:placeholder name="name" condition="empty()">
         <p>No entry available.</p>
      </cond:placeholder>
      <cond:placeholder name="name" condition="notEmpty()">
         <p>Name: ${content}</p>
      </cond:placeholder>');
      $doc->onParseTime();
      $doc->onAfterAppend();

      $doc->transformOnPlace();

      return $doc;
   }

}
