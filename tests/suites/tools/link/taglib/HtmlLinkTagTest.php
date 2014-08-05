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
namespace APF\tests\suites\tools\link\taglib;

use APF\tools\link\taglib\HtmlLinkTag;

/**
 * Tests the HtmlLinkTag functionality regarding the active marker.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 05.08.2014<br />
 */
class HtmlLinkTagTest extends \PHPUnit_Framework_TestCase {

   public function testIsActiveSingleParameter() {

      $_SERVER['REQUEST_URI'] = '/my-page/?foo=bar';
      $_SERVER['HTTP_HOST'] = 'localhost';

      $tag = new HtmlLinkTag();
      $tag->setAttribute('foo', 'bar');
      $tag->setContent('Link text');
      $tag->onParseTime();

      $this->assertTrue($tag->isActive());
   }

   public function testIsActiveMultipleParameters() {

      $_SERVER['REQUEST_URI'] = '/my-page/?foo=bar&view=baz';
      $_SERVER['HTTP_HOST'] = 'localhost';

      $tag = new HtmlLinkTag();
      $tag->setAttribute('foo', 'bar');
      $tag->setAttribute('view', 'baz');
      $tag->setContent('Link text');
      $tag->onParseTime();

      $this->assertTrue($tag->isActive());
   }

   public function testIsActiveMultipleParametersMixedUp() {

      $_SERVER['REQUEST_URI'] = '/my-page/?foo=bar&view=baz';
      $_SERVER['HTTP_HOST'] = 'localhost';

      $tag = new HtmlLinkTag();
      $tag->setAttribute('view', 'baz');
      $tag->setAttribute('foo', 'bar');
      $tag->setContent('Link text');
      $tag->onParseTime();

      $this->assertTrue($tag->isActive());
   }

}
