<?php
namespace APF\extensions\htmlheader\biz;

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
use APF\extensions\htmlheader\biz\HtmlNode;
use APF\extensions\htmlheader\biz\CssNode;

/**
 * @package APF\extensions\htmlheader\biz
 * @class CssImageNode
 *
 * Represents a node for css image.
 *
 * @author Werner Liemberger
 * @version
 * Version 0.1, 25.8.2011<br />
 */
class CssImageNode extends HtmlNode implements CssNode {

   public function __construct($href, $rel, $type) {
      $this->setAttribute('href', $href);
      $this->setAttribute('rel', $rel);
      $this->setAttribute('type', $type);
   }

   protected function getTagName() {
      return 'link';
   }

   public function getChecksum() {
      return md5($this->getAttribute('href'));
   }

}
