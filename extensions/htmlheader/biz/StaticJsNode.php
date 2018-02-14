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
namespace APF\extensions\htmlheader\biz;

/**
 * Represents a node for static js inclusion.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 16.08.2010<br />
 */
class StaticJsNode extends HtmlNode implements JsNode {

   public function __construct($file) {
      $this->setAttribute('src', $file);
      $this->setAttribute('type', 'text/javascript');
      $this->setContent('');
   }

   protected function getTagName() {
      return 'script';
   }

   public function getChecksum() {
      return md5($this->getAttribute('src'));
   }

}
