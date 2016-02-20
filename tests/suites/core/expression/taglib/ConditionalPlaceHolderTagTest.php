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
      $tag->setContent(['moreLabel' => $model->getMoreLabel(), 'moreLink' => $model->getMoreLink()]);

      $this->assertEquals(
            '<a href="' . $model->getMoreLink() . '">' . $model->getMoreLabel() . '</a>',
            $tag->transform()
      );

   }

   public function testViewModelContent() {
      $tag = $this->getPlaceHolder('<a href="${content->getMoreLink()}">${content->getMoreLabel()}</a>', []);

      $model = new LinkModel();
      $tag->setContent($model);

      $this->assertEquals(
            '<a href="' . $model->getMoreLink() . '">' . $model->getMoreLabel() . '</a>',
            $tag->transform()
      );
   }

}
