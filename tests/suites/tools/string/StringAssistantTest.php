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
namespace APF\tests\suites\tools\string;

use APF\tools\string\StringAssistant;
use PHPUnit\Framework\TestCase;

class StringAssistantTest extends TestCase {

   public function testEscapeSpecialCharacters() {
      $this->assertEquals(
            '6%$!?&gt;&lt;test&quot;&#039;',
            StringAssistant::escapeSpecialCharacters('6%$!?><test"\'')
      );
   }

   public function testEncodeCharactersToHTML() {
      $this->assertEquals(
            '&#54;&#37;&#36;&#33;&#63;&#62;&#60;&#116;&#101;&#115;&#116;&#34;&#39;',
            StringAssistant::encodeCharactersToHTML('6%$!?><test"\'')
      );
   }

   public function testGenerateCaptchaString() {
      $actual = StringAssistant::generateCaptchaString(5);
      $this->assertNotEmpty($actual);
      $this->assertEquals(5, strlen($actual));
   }

}
