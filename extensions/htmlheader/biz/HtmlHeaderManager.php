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
namespace APF\extensions\htmlheader\biz;

use APF\core\pagecontroller\APFObject;

/**
 * Container for htmlheader objects.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>, Christian Achatz
 * @version
 * 0.1, 20.09.2009<br />
 * 0.2, 27.09.2009<br />
 * 0.3, 20.03.2010 Added package support<br />
 * 0.4, 20.08.2010 (Enhancement with further node types)<br />
 * 0.5, 15.05.2013 (Added namespace to getNodesByType [Tobias Lückel|Megger])<br />
 */
class HtmlHeaderManager extends APFObject {

   /**
    * The list of header nodes.
    *
    * @var HeaderNode[] $nodes
    */
   protected $nodes = array();

   /**
    * Let's you add a header node to the container.
    *
    * @param HeaderNode $node The node to add.
    */
   public function addNode(HeaderNode $node) {
      if ($this->isUnique($node)) {
         $this->nodes[] = $node;
      }
   }

   /**
    * Compares already saved nodes with new node, in order to find duplicates.
    * Returns true if duplicate was found.
    *
    * @param HeaderNode $node New node
    *
    * @return bool Returns true if duplicate was found.
    */
   protected function isUnique(HeaderNode $node) {
      $checksum = $node->getChecksum();

      foreach ($this->nodes as $node) {
         if ($node->getChecksum() === $checksum) {
            return false;
         }
      }

      return true;
   }

   /**
    * @param string $type The (interface) type of node to include in the list.
    *
    * @return HeaderNode[] The list of header nodes that is described by the given type.
    */
   protected function getNodesByType($type) {
      $nodes = array();

      foreach ($this->nodes as $node) {
         if ($node instanceof $type) {
            $nodes[] = $node;
         }
      }

      return $nodes;
   }

   /**
    * @return HtmlNode The title or null.
    */
   public function getTitle() {
      $titles = $this->getNodesByType('APF\extensions\htmlheader\biz\TitleNode');
      if (count($titles) > 0) {
         return $titles[count($titles) - 1]; // always return the last title, to allow override!
      }

      return null;
   }

   /**
    * @return CanonicalNode The canonical node or null.
    */
   public function getCanonical() {
      $canonical = $this->getNodesByType('APF\extensions\htmlheader\biz\CanonicalNode');
      if (count($canonical) > 0) {
         return $canonical[count($canonical) - 1]; // always return the last title, to allow override!
      }

      return null;
   }

   /**
    * @return CssNode[] The list of javascript nodes.
    */
   public function getStylesheetNodes() {
      return $this->getNodesByType('APF\extensions\htmlheader\biz\CssNode');
   }

   /**
    * @return JsNode[] The list of javascript nodes.
    */
   public function getJavascriptNodes() {
      return $this->getNodesByType('APF\extensions\htmlheader\biz\JsNode');
   }

   /**
    * @return MetaNode[] The meta nodes (HttpMetaNode, SimpleMetaNode).
    */
   public function getMetaNodes() {
      return $this->getNodesByType('APF\extensions\htmlheader\biz\MetaNode');
   }

   /**
    * @return BaseNode[] The base nodes of the current header.
    */
   public function getBaseNodes() {
      return $this->getNodesByType('APF\extensions\htmlheader\biz\BaseNode');
   }

   /**
    * Returns the complete list of header nodes. Unsorted!
    *
    * @return HeaderNode[] The list of header nodes.
    */
   public function getNodes() {
      return $this->nodes;
   }

}
