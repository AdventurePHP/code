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
namespace APF\tests\suites\core\pagecontroller;

use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\PlaceHolderTag;
use Exception;
use ReflectionMethod;

/**
 * Tests the APF Parser capabilities.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 10.10.2014<br />
 */
class ParserTest extends \PHPUnit_Framework_TestCase {

   /**
    * Tests whether the parser ignores an HTML comment such as <em>&lt;!-- foo:bar --&gt;</em> going
    * through the document.
    * <p/>
    * See http://tracker.adventure-php-framework.org/view.php?id=238 for details.
    */
   public function testHtmlCommentWithTagNotation() {

      $doc = new Document();
      $doc->setContent('This is the content of a document with tags and comments:

<!-- app:footer -->

This is text after a comment...

<html:placeholder name="foo" />

This is text after a place holder...
');

      try {
         $this->getMethod()->invoke($doc);

         $placeHolder = $doc->getChildNode('name', 'foo', PlaceHolderTag::class);
         $this->assertTrue($placeHolder instanceof PlaceHolderTag);
      } catch (Exception $e) {
         $this->fail('Parsing comments failed. Message: ' . $e->getMessage());
      }

   }

   /**
    * @return ReflectionMethod
    */
   protected function getMethod() {
      $method = new ReflectionMethod(Document::class, 'extractTagLibTags');
      $method->setAccessible(true);

      return $method;
   }

   /**
    * Tests parser capabilities with <em>&lt;li&gt;FOO:</em> statements in e.g. HTML lists.
    */
   public function testClosingBracket() {

      $doc = new Document();
      $doc->setContent('<p>
   This is the content of a document with tags and lists:
</p>
<ul>
   <li>Foo: Foo is the first part of the &quot;foo bar&quot; phrase.</li>
   <li>Bar: Bar is the second part of the &quot;foo bar&quot; phrase.</li>
</ul>
<p>
 This is text after a list...
</p>
<html:placeholder name="foo" />
<p>
   This is text after a place holder...
</p>
');

      try {
         $this->getMethod()->invoke($doc);

         $placeHolder = $doc->getChildNode('name', 'foo', PlaceHolderTag::class);
         $this->assertTrue($placeHolder instanceof PlaceHolderTag);
      } catch (Exception $e) {
         $this->fail('Parsing lists failed. Message: ' . $e->getMessage());
      }

   }

   /**
    * Tests whether the parser ignores "normal" HTML code with colons (":") in tag attributes.
    * <p/>
    * See http://tracker.adventure-php-framework.org/view.php?id=266 for details.
    */
   public function testColonsInTagAttributes() {

      $doc = new Document();
      $doc->setContent(
            '<p>
   This is static content...
</p>
<p>
   To quit your session, please <a href="/?:action=logout">Logout</a>
</p>
<p>
   This is static content...
</p>'
      );

      try {
         $this->getMethod()->invoke($doc);
         $this->assertEmpty($doc->getChildren());
      } catch (Exception $e) {
         $this->fail('Parsing HTML failed. Message: ' . $e->getMessage());
      }
   }

   /**
    * @return ReflectionMethod
    */
   protected function getMethod() {
      $method = new ReflectionMethod('APF\core\pagecontroller\Document', 'extractTagLibTags');
      $method->setAccessible(true);

      return $method;
   }

}
