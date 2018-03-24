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

use APF\tools\form\taglib\SelectBoxGroupTag;
use PHPUnit\Framework\TestCase;

class SelectBoxGroupTagTest extends TestCase {

   /**
    * Tests whether an option group tag returns the correct selected options.
    */
   public function testSelectedOption() {

      $tag = new SelectBoxGroupTag();
      $tag->setContent('<group:option value="1">One</group:option>
<group:option value="2" selected="selected">Two</group:option>
<group:option value="3">Three</group:option>');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $option = $tag->getSelectedOption();
      $this->assertNotNull($option);

      $this->assertEquals('2', $option->getValue());

      // ensure method returns references
      $children = $tag->getChildren();
      $keys = array_keys($children);

      $this->assertEquals(
            spl_object_hash($children[$keys[1]]),
            spl_object_hash($option)
      );
   }

   /**
    * Tests whether multiple selected options are returned correctly.
    */
   public function testSelectedOptions() {

      $tag = new SelectBoxGroupTag();
      $tag->setContent('<group:option value="1">One</group:option>
<group:option value="2" selected="selected">Two</group:option>
<group:option value="3" selected="selected">Three</group:option>');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $options = $tag->getSelectedOptions();
      $this->assertNotEmpty($options);

      $this->assertEquals('2', $options[0]->getValue());
      $this->assertEquals('3', $options[1]->getValue());

      // ensure method returns references
      $children = $tag->getChildren();
      $keys = array_keys($children);

      $this->assertEquals(
            spl_object_hash($children[$keys[1]]),
            spl_object_hash($options[0])
      );
      $this->assertEquals(
            spl_object_hash($children[$keys[2]]),
            spl_object_hash($options[1])
      );
   }

   /**
    * Tests selecting entries via API (e.g from within controllers).
    */
   public function testSetOption2Selected() {

      $tag = new SelectBoxGroupTag();
      $tag->setContent('<group:option value="1">One</group:option>
<group:option value="2">Two</group:option>
<group:option value="3">Three</group:option>');
      $tag->onParseTime();
      $tag->onAfterAppend();

      $this->assertNull($tag->getSelectedOption());
      $this->assertEmpty($tag->getSelectedOptions());

      $tag->setOption2Selected('One');
      $this->assertNotNull($tag->getSelectedOption());
      $this->assertCount(1, $tag->getSelectedOptions());

      $tag->setOption2Selected('3');
      $this->assertCount(2, $tag->getSelectedOptions());

   }

}
