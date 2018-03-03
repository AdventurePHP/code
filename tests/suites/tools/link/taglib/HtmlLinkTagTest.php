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
namespace APF\tests\suites\tools\link\taglib;

use APF\tools\link\taglib\HtmlLinkTag;
use PHPUnit\Framework\TestCase;

/**
 * Tests the HtmlLinkTag functionality regarding the active marker.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 05.08.2014<br />
 */
class HtmlLinkTagTest extends TestCase {

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

   /**
    * ID#246: added unit test to guarantee accurate functionality
    */
   public function testNavigationLinks() {

      // Current URL: http://www.example.com/?p=start
      $query = '/?p=start';
      $_SERVER['REQUEST_URI'] = $query;
      $host = 'www.example.com';
      $_SERVER['HTTP_HOST'] = $host;

      // <li><html:a p="start">Home</html:a></li>
      $tagOne = new HtmlLinkTag();
      $tagOne->setAttributes([
            'p' => 'start'
      ]);
      $homeLinkText = 'Home';
      $tagOne->setContent($homeLinkText);
      $tagOne->onParseTime();

      $this->assertTrue($tagOne->isActive());
      $this->assertEquals(
            '<a href="http://' . $host . '/?p=start" class="active">' . $homeLinkText . '</a>',
            $tagOne->transform()
      );

      // <li><html:a p="impress" para="345">Impress</html:a></li>
      $tagTwo = new HtmlLinkTag();
      $tagTwo->setAttributes([
            'p'    => 'impress',
            'para' => '345'
      ]);
      $impressLinkText = 'Impress';
      $tagTwo->setContent($impressLinkText);
      $tagTwo->onParseTime();

      $this->assertFalse($tagTwo->isActive());
      $this->assertEquals(
            '<a href="http://' . $host . '/?p=impress&amp;para=345">' . $impressLinkText . '</a>',
            $tagTwo->transform()
      );

   }

   public function testLinkTextFallBack() {

      // Current URL: http://www.example.com/?p=start
      $query = '/?p=start';
      $_SERVER['REQUEST_URI'] = $query;
      $host = 'www.example.com';
      $_SERVER['HTTP_HOST'] = $host;

      // <li><html:a p="start" title="Home" /></li>
      $tagOne = new HtmlLinkTag();
      $homeLinkText = 'Home';
      $tagOne->setAttributes([
            'p'     => 'start',
            'title' => $homeLinkText
      ]);
      $tagOne->onParseTime();

      $this->assertTrue($tagOne->isActive());
      $this->assertEquals(
            '<a title="' . $homeLinkText . '" href="http://' . $host . '/?p=start" class="active">' . $homeLinkText . '</a>',
            $tagOne->transform()
      );

   }

}
