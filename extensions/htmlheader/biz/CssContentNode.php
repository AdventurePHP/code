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
 * @package APF\extensions\htmlheader\biz
 * @class CssContentNode
 *
 * Represents a css node with explicit content.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 18.08.2010<br />
 */
class CssContentNode extends HtmlNode implements CssNode {

   public function __construct($content, $media = null) {
      $this->setContent($content);
      $this->setAttribute('type', 'text/css');

      // default to screen css file
      if ($media === null) {
         $media = 'screen';
      }

      $this->setAttribute('media', $media);
   }

   protected function getTagName() {
      return 'style';
   }

   public function getChecksum() {
      return md5($this->getContent());
   }

}
