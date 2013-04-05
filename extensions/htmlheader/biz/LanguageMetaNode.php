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
use APF\extensions\htmlheader\biz\MetaNode;

/**
 * @package APF\extensions\htmlheader\biz
 * @class LanguageMetaNode
 *
 * Implements a language indication meta node.
 *
 * @author Werner (welworx)
 * @version
 * Version 0.1, 05.12.2010<br />
 */
class LanguageMetaNode extends HtmlNode implements MetaNode {

   public function __construct($name, $lang, $content) {
      $this->setAttribute('name', $name);
      $this->setAttribute('lang', $lang);
      $this->setAttribute('content', $content);
   }

   protected function getTagName() {
      return 'meta';
   }

   public function getChecksum() {
      return md5($this->getAttribute('name') . $this->getAttribute('lang') . $this->getAttribute('content'));
   }

}
