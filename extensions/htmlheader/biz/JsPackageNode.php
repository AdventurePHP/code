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
use APF\extensions\htmlheader\biz\PackageNode;
use APF\extensions\htmlheader\biz\JsNode;

/**
 * @package APF\extensions\htmlheader\biz
 * @class JsPackageNode
 *
 * Implements a js node, that loads a js package.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.08.2010<br />
 */
class JsPackageNode extends PackageNode implements JsNode {

   public function __construct($url, $name) {
      parent::__construct($url, $name);
      $this->setAttribute('type', 'text/javascript');
      // set the content, because otherwise ../> tag will destroy layout.
      $this->setContent('');
   }

   protected function getTagName() {
      return 'script';
   }

   protected function getLocationAttributeName() {
      return 'src';
   }

   protected function getTypeIndicator() {
      return 'js';
   }

}
