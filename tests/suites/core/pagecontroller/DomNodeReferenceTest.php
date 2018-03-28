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
use APF\core\pagecontroller\DomNode;
use APF\core\pagecontroller\PlaceHolder;
use APF\core\pagecontroller\PlaceHolderTag;
use APF\core\pagecontroller\Template;
use APF\core\pagecontroller\TemplateTag;
use APF\core\pagecontroller\XmlParser;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * Tests all DomNode methods working w/ references to proof referencing node properties (especially children)
 * works with the current PHP version.
 */
class DomNodeReferenceTest extends TestCase {

   /**
    * Tests whether manipulation of the parent node is reflected in the
    * respective instance (i.e. used to re-locate DOM nodes).
    */
   public function testParentReference() {

      $content1 = 'parent';
      $content2 = 'foo';

      $node = new Document();

      $parent = new Document();
      $parent->setContent($content1);

      $node->setParent($parent);

      $this->assertEquals($content1, $node->getParent()->getContent());

      // try changing content on the reference (applying and returning objects in PHP should be "by reference")
      $parent->setContent($content2);

      $this->assertEquals($content2, $node->getParent()->getContent());
   }

   /**
    * Tests whether working on the list of children is reflected by the DOM node instance
    * (i.e. used to add children on the fly).
    */
   public function testChildrenReference() {

      $node = new TemplateTag();
      $node->setContent('${foo}${bar}');
      $node->onParseTime();
      $node->onAfterAppend();

      // obtain a "real" reference on the list of children
      $children = &$node->getChildren();

      $this->assertNotEmpty($children);
      $this->assertCount(2, $children);
      $this->assertContainsOnlyInstancesOf(PlaceHolder::class, $children);

      // append an additional node
      $placeHolder = new PlaceHolderTag();
      $placeHolder->setObjectId(XmlParser::generateUniqID());
      $placeHolder->setAttribute('name', 'baz');
      $children[$placeHolder->getObjectId()] = $placeHolder;

      // check whether the object instance has a new child added
      $children = $node->getChildren();
      $this->assertNotEmpty($children);
      $this->assertCount(3, $children);
      $this->assertContainsOnlyInstancesOf(PlaceHolder::class, $children);
   }

   /**
    * Tests whether manipulating a single child is reflected by the DOM node instance
    * (i.e. used to add children on the fly).
    */
   public function testGetChildNodeReference() {

      $node = new TemplateTag();
      $node->setContent('${foo}');
      $node->onParseTime();
      $node->onAfterAppend();

      $node
            ->getChildNode('name', 'foo', PlaceHolder::class)
            ->setAttribute('foo', 'bar');

      $property = new ReflectionProperty(TemplateTag::class, 'children');
      $property->setAccessible(true);

      /* @var $children DomNode[] */
      $children = $property->getValue($node);
      $key = array_keys($children)[0];
      $this->assertEquals('foo', $children[$key]->getAttribute('name'));
      $this->assertEquals('bar', $children[$key]->getAttribute('foo'));
   }

   /**
    * Tests whether manipulating a subset of children is reflected by the DOM node instance
    * (i.e. used to add children on the fly).
    */
   public function testGetChildNodes() {

      $node = new TemplateTag();
      $node->setContent('<html:placeholder name="foo" /><html:template name="bar" /><html:placeholder name="foo" />');
      $node->onParseTime();
      $node->onAfterAppend();

      // manipulating a sub set selected by getChildNodes()
      /* @var $placeHolders DomNode[] */
      $placeHolders = $node->getChildNodes('name', 'foo', PlaceHolder::class);
      $placeHolders[0]->setAttribute('foo', 'baz1');
      $placeHolders[1]->setAttribute('foo', 'baz2');

      $children = $node->getChildren();
      $keys = array_keys($children);

      $this->assertCount(3, $children);
      $this->assertInstanceOf(PlaceHolder::class, $children[$keys[0]]);
      $this->assertInstanceOf(Template::class, $children[$keys[1]]);
      $this->assertInstanceOf(PlaceHolder::class, $children[$keys[2]]);

      $this->assertEquals('baz1', $children[$keys[0]]->getAttribute('foo'));
      $this->assertEquals('foo', $children[$keys[0]]->getAttribute('name'));
      $this->assertEquals('baz2', $children[$keys[2]]->getAttribute('foo'));

      $this->assertEquals(spl_object_hash($placeHolders[0]), spl_object_hash($children[$keys[0]]));
      $this->assertEquals(spl_object_hash($placeHolders[1]), spl_object_hash($children[$keys[2]]));
   }

   /**
    * Tests whether manipulating a specific DOM node reflected by the DOM node instance
    * (i.e. used to add children on the fly).
    */
   public function testGetNodeById() {

      $node = new TemplateTag();
      $node->setContent('<html:template name="foo"><html:placeholder name="foo" dom-id="bar" /></html:template>');
      $node->onParseTime();
      $node->onAfterAppend();

      $placeHolder = $node->getNodeById('bar');

      $this->assertNotEmpty($placeHolder);
      $this->assertInstanceOf(PlaceHolder::class, $placeHolder);
      $this->assertEmpty($placeHolder->getContent());

      $placeHolder->setContent('foo');

      $this->assertEquals(
            'foo',
            $node
                  ->getChildNode('name', 'foo', Template::class)
                  ->getChildNode('name', 'foo', PlaceHolder::class)
                  ->getContent()
      );

   }

   /**
    * Negative test that getAttributes() does not return a reference but an array copy
    * due to missing & sign in method declaration.
    */
   public function testAttributesManipulation() {

      $node = new Document();
      $node->setAttributes(['foo' => 'bar', 'bar' => 'baz']);

      $node->getAttributes()['baz'] = 'foo';
      $this->assertCount(2, $node->getAttributes());

      $attributes = $node->getAttributes();
      $attributes['baz'] = 'foo';
      $this->assertCount(2, $node->getAttributes());

      // only directly operating on the object reflects changes!
      $node->setAttribute('baz', 'foo');
      $this->assertCount(3, $node->getAttributes());

   }

}
