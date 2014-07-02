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

/**
 * Represents a http header meta tag.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 16.08.2010<br />
 */
class HttpMetaNode extends HtmlNode implements MetaNode {

   public function __construct($name, $content) {
      $this->setAttribute('http-equiv', $name);
      $this->setAttribute('content', $content);
   }

   protected function getTagName() {
      return 'meta';
   }

   public function getChecksum() {
      return md5($this->getAttribute('name') . $this->getAttribute('content'));
   }

}
