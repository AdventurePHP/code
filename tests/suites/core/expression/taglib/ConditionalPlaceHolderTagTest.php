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
namespace APF\tests\suites\core\expression\taglib;

use APF\core\expression\taglib\ConditionalPlaceHolderTag;
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\DomNode;
use APF\core\pagecontroller\ParserException;
use APF\core\pagecontroller\TemplateTag;
use APF\tests\suites\core\expression\LinkModel;
use PHPUnit\Framework\TestCase;

class ConditionalPlaceHolderTagTest extends TestCase {

   /**
    * @throws ParserException
    */
   public function testSimplePlaceHolder() {

      $name = 'foo';
      $text = 'test headline';

      // inject place holder data
      $tag = $this->getPlaceHolder('<h3>${content}</h3>', ['name' => $name]);

      $parent = new Document();
      $parent->setPlaceHolder($name, $text);
      $tag->setParentObject($parent);

      $this->assertEquals('<h3>' . $text . '</h3>', $tag->transform());
   }

   /**
    * @param string $content
    * @param array $attributes
    * @return ConditionalPlaceHolderTag
    * @throws ParserException
    */
   protected function getPlaceHolder($content, array $attributes) {
      $tag = new ConditionalPlaceHolderTag();
      $tag->setAttributes($attributes);
      $tag->setContent($content);
      $tag->onParseTime();
      $tag->onAfterAppend();

      return $tag;
   }

   /**
    * @throws ParserException
    */
   public function testPlaceHolderWithLengthCondition() {

      $name = 'foo';
      $text = 'test headline';

      // inject place holder data
      $tag = $this->getPlaceHolder('<h3>${content}</h3>', ['condition' => 'longerThan(10)', 'name' => $name]);

      $parent = new Document();
      $parent->setPlaceHolder($name, $text);
      $tag->setParentObject($parent);

      $this->assertEquals('<h3>' . $text . '</h3>', $tag->transform());

      $tag = $this->getPlaceHolder('<h3>${content}</h3>', ['condition' => 'longerThan(10)', 'name' => $name]);

      // inject place holder data
      $text = 'headline';
      $parent = new Document();
      $parent->setPlaceHolder($name, $text);
      $tag->setParentObject($parent);

      $this->assertEquals('', $tag->transform());
   }

   /**
    * @throws ParserException
    */
   public function testEmptyOutputForMissingContent() {
      $tag = $this->getPlaceHolder('<h3>${content}</h3>', []);
      $doc = new Document();
      $tag->setParentObject($doc);
      $this->assertEmpty($tag->transform());
   }

   /**
    * @throws ParserException
    */
   public function testArrayContent() {

      $name = 'foo';
      $tag = $this->getPlaceHolder(
      /** @lang text */
            '<a href="${content[\'moreLink\']}">${content[\'moreLabel\']}</a>',
            ['name' => $name]
      );

      $parent = new Document();
      $model = new LinkModel();
      $parent->setPlaceHolder($name, ['moreLabel' => $model->getLabel(), 'moreLink' => $model->getUrl()]);
      $tag->setParentObject($parent);

      $this->assertEquals(
            '<a href="' . $model->getUrl() . '">' . $model->getLabel() . '</a>',
            $tag->transform()
      );

   }

   /**
    * @throws ParserException
    */
   public function testViewModelContent() {

      $name = 'foo';
      $tag = $this->getPlaceHolder(
      /** @lang text */
            '<a href="${content->getUrl()}">${content->getLabel()}</a>',
            ['name' => $name]
      );

      $parent = new Document();
      $model = new LinkModel();
      $parent->setPlaceHolder($name, $model);
      $tag->setParentObject($parent);

      $this->assertEquals(
            '<a href="' . $model->getUrl() . '">' . $model->getLabel() . '</a>',
            $tag->transform()
      );
   }

   /**
    * @throws ParserException
    */
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
    * @throws ParserException
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

   /**
    * ID#301: allow passing regular expression conditions including double quotes
    * @throws ParserException
    */
   public function testRegExpCondition() {
      $name = 'foo';
      $text = '"foo"';

      // inject place holder data
      $tag = $this->getPlaceHolder('${content}', ['condition' => 'regExp(#^&quot;f([o]{2})&quot;$#)', 'name' => $name]);

      $parent = new Document();
      $parent->setPlaceHolder($name, $text);
      $tag->setParentObject($parent);

      $this->assertEquals($text, $tag->transform());
   }

}
