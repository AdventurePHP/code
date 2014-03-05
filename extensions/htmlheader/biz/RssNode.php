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
use APF\extensions\htmlheader\biz\HeaderNode;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * @package APF\extensions\htmlheader\biz
 * @class RssNode
 *
 * Represents an RSS reference within the HTML header area.
 *
 * @author Coach83, Christian Achatz
 * @version
 * Version 0.1, 25.04.2013 <br />
 */
class RssNode extends HtmlNode implements HeaderNode {

   public function __construct($file) {
      $this->setAttribute('href', $file);
      $this->setAttribute('rel', 'alternate');
      $this->setAttribute('type', 'application/rss+xml');
   }

   protected function getTagName() {
      return 'link';
   }

   public function getChecksum() {
      return md5($this->getAttribute('href'));
   }

}
