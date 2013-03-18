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
 * @package extensions::htmlheader::biz
 * @Class DynamicCssNode
 *
 * Css file node for HtmlHeaderManagers data.
 *
 * @author Ralf Schubert
 * @version
 * 0.1, 20.09.2009 <br />
 * 0.2, 27.02.2010 (Added external file support) <br />
 */
class DynamicCssNode extends HtmlNode implements CssNode {

   /**
    * Receives information and configures node.
    * @param string $url Optional url.
    * @param string $namespace Namespace of file
    * @param string $filename Name of file (without .css)
    * @param bool $rewriting Optional. Create rewriting Url.
    * @param bool $fcaction Optional. Create link for FC-Action.
    */
   public function __construct($url, $namespace, $filename, $rewriting = null, $fcaction = true) {
      $this->setAttribute('href', $this->buildFrontcontrollerLink(
         $url,
         $namespace,
         $filename,
         $rewriting,
         $fcaction,
         'css'
      ));
      $this->setAttribute('rel', 'stylesheet');
      $this->setAttribute('type', 'text/css');
   }

   public function getChecksum() {
      return md5($this->getAttribute('href'));
   }

   protected function getTagName() {
      return 'link';
   }

}
