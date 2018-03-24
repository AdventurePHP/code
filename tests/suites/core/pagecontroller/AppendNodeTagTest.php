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

use APF\core\pagecontroller\PlaceHolder;
use APF\core\pagecontroller\Template;
use APF\core\pagecontroller\TemplateTag;
use PHPUnit\Framework\TestCase;

class AppendNodeTagTest extends TestCase {

   /**
    * Test relocation of DOM nodes from included template into current node.
    */
   public function testDomRelocation() {

      $tag = new TemplateTag();
      $tag->setContent('<core:appendnode namespace="' . __NAMESPACE__ . '\templates" template="include_simple" />');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $children = $tag->getChildren();

      // including append node tag we've got three child nodes
      $this->assertCount(3, $children);

      $keys = array_keys($children);
      $this->assertInstanceOf(Template::class, $children[$keys[1]]);
      $this->assertInstanceOf(PlaceHolder::class, $children[$keys[2]]);

      // test whether the nodes have been re-referenced and not copied
      $appendNodeTag = $children[$keys[0]];
      $appendNodeChildren = $appendNodeTag->getChildren();

      $innerKeys = array_keys($appendNodeChildren);

      $this->assertEquals(
            spl_object_hash($children[$keys[1]]),
            spl_object_hash($appendNodeChildren[$innerKeys[0]])
      );
      $this->assertEquals(
            spl_object_hash($children[$keys[2]]),
            spl_object_hash($appendNodeChildren[$innerKeys[1]])
      );

   }

   /**
    * Test static include w/o other DOM nodes.
    */
   public function testStaticInclude() {

      // test transformation w/o static include
      $tag = new TemplateTag();
      $tag->setContent('<core:appendnode namespace="' . __NAMESPACE__ . '\templates" template="include_static" />');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $this->assertEquals('', $tag->transformTemplate());

      // test transformation w/ static include
      $tag = new TemplateTag();
      $tag->setContent('<core:appendnode includestatic="true" namespace="' . __NAMESPACE__ . '\templates" template="include_static" />');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $expected = 'static text';

      $this->assertEquals($expected, $tag->transformTemplate());

   }

   /**
    * Test DOM node relocation including static content and transformation.
    */
   public function testComplexUseCse() {

      $tag = new TemplateTag();
      $tag->setContent('<core:appendnode includestatic="true" namespace="' . __NAMESPACE__ . '\templates" template="include_complex" />');

      $tag->onParseTime();
      $tag->onAfterAppend();

      $expected = 'static text';

      // fill template with place holder
      /* @var $template TemplateTag */
      $template = $tag->getChildNode('name', 'test', Template::class);
      $template->setPlaceHolder('test', $expected);
      $template->transformOnPlace();

      $tag->setPlaceHolder('test', $expected);

      $actual = $tag->transformTemplate();

      // 6 = 4 static + 1 template place holder + 1 place holder
      $this->assertTrue(substr_count($actual, $expected) === 6);
   }

}
