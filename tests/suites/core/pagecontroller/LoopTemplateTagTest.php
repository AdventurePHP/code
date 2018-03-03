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
use APF\core\pagecontroller\LoopTemplateTag;
use APF\core\pagecontroller\ParserException;
use PHPUnit\Framework\TestCase;

/**
 * Tests capabilities of the &lt;loop:template /&gt; tag.
 */
class LoopTemplateTagTest extends TestCase {

   /**
    * @param string $contentMapping
    * @param string $content
    * @return LoopTemplateTag
    * @throws ParserException
    */
   protected function getLoopTemplate($contentMapping, $content) {

      $template = new LoopTemplateTag();
      $template->setAttribute(LoopTemplateTag::CONTENT_MAPPING_ATTRIBUTE, $contentMapping);
      $template->setContent($content);

      $doc = new Document();
      $template->setParentObject($doc);

      $template->onParseTime();
      $template->onAfterAppend();

      return $template;
   }

   /**
    * Tests whether &lt;loop:template /&gt; has the same behaviour as a "normal" template.
    * @throws ParserException
    */
   public function testEmptyOutput() {

      $content = 'This is dummy content';

      $template = $this->getLoopTemplate('foo', $content);
      $this->assertEmpty($template->transform());

      // wrong type of content mapping
      $template = $this->getLoopTemplate('foo', $content);

      $template->getParentObject()->setData('foo', new TestDataModel());
      $template->transformOnPlace();
      $this->assertEmpty($template->transform());

      // calling transformOnPlace() initiates display
      $template = $this->getLoopTemplate('foo', $content);
      $template->getParentObject()->setData('foo', [1, 2, 3]);

      $template->transformOnPlace();
      $this->assertEquals($content . $content . $content, $template->transform());
   }

   /**
    * Tests behaviour in case of empty content and content mapping.
    * @throws ParserException
    */
   public function testEmptyContentMapping() {

      $template = $this->getLoopTemplate('foo', '');

      // empty content and content mapping
      $template->transformOnPlace();
      $this->assertEmpty($template->transform());

      // empty content but w/ content mapping
      $template->getParentObject()->setData('foo', [1, 2, 3]);
      $template->transformOnPlace();
      $this->assertEmpty($template->transform());
   }

   /**
    * Tests output generation for simple content including static place holder.
    * @throws ParserException
    */
   public function testLoopWithSimpleDataAttribute() {

      $template = $this->getLoopTemplate('foo', '<p>${staticPlaceHolder}|${content[\'number\']}|${content[\'title\']}</p>');
      $template->getParentObject()->setData(
            'foo',
            [
                  [
                        'number' => 1,
                        'title' => 'One'
                  ],
                  [
                        'number' => 2,
                        'title' => 'Two'
                  ],
                  [
                        'number' => 3,
                        'title' => 'Three'
                  ]
            ]
      );
      $template->setPlaceHolder('staticPlaceHolder', 'foo');

      $template->transformOnPlace();

      $this->assertEquals(
            '<p>foo|1|One</p>'
            . '<p>foo|2|Two</p>'
            . '<p>foo|3|Three</p>',
            $template->transform()
      );
   }


   /**
    * Tests output generation for complex content.
    * @throws ParserException
    */
   public function testLoopWithComplexDataAttribute() {

      $template = $this->getLoopTemplate('foo', '<p>${content->getFoo()}|${content->getBar()}</p>');
      $template->getParentObject()->setData(
            'foo',
            [
                  new TestDataModel(),
                  new TestDataModel(),
                  new TestDataModel()
            ]
      );
      $template->setPlaceHolder('staticPlaceHolder', 'foo');

      $template->transformOnPlace();

      $this->assertEquals(
            '<p>foo|bar</p>'
            . '<p>foo|bar</p>'
            . '<p>foo|bar</p>',
            $template->transform()
      );
   }

   /**
    * Tests complex content mapping with access to data attribute of grandparent object.
    * @throws ParserException
    */
   public function testLoopWithAccessToParentNode() {

      $template = new LoopTemplateTag();
      $template->setAttribute(
            LoopTemplateTag::CONTENT_MAPPING_ATTRIBUTE,
            'this->getParentObject()->getData(\'foo\')'
      );
      $template->setContent('<p>${content->getFoo()}|${content->getBar()}</p>');

      $grandParent = new Document();
      $grandParent->setData(
            'foo',
            [
                  new TestDataModel(),
                  new TestDataModel(),
                  new TestDataModel()
            ]
      );

      $parent = new Document();
      $parent->setParentObject($grandParent);

      $template->setParentObject($parent);

      $template->onParseTime();
      $template->onAfterAppend();

      $template->transformOnPlace();

      $this->assertEquals(
            '<p>foo|bar</p>'
            . '<p>foo|bar</p>'
            . '<p>foo|bar</p>',
            $template->transform()
      );
   }

   /**
    * ID#302: test direct output using attribute <em>transform-on-place=true</em>.
    * @throws ParserException
    */
   public function testDirectOutputWithAccessToParentNode() {

      $expected = 'content';

      $template = new LoopTemplateTag();
      $template->setAttribute(
            LoopTemplateTag::CONTENT_MAPPING_ATTRIBUTE,
            'this->getParentObject()->getData(\'foo\')'
      );
      $template->setAttribute('transform-on-place', 'true');
      $template->setContent($expected . '|${content[0]}');

      $grandParent = new Document();
      $grandParent->setData('foo', [['bar']]);

      $parent = new Document();
      $parent->setParentObject($grandParent);

      $template->setParentObject($parent);

      $template->onParseTime();
      $template->onAfterAppend();

      $this->assertEquals($expected . '|bar', $template->transform());
   }

}
