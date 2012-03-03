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
import('extensions::htmlheader::biz', 'HtmlNode');

/**
 * @abstract
 * @package extensions::htmlheader::biz
 * @class PackageNode
 *
 * Provides basic functionality for css and js package nodes
 *
 * @author Ralf Schubert
 * @version
 * Version 0.1, ...<br />
 * Version 0.2, 20.08.2010<br />
 */
abstract class PackageNode extends HtmlNode {

   public function __construct($url, $name, $rewriting = null) {
      $this->setAttribute($this->getLocationAttributeName(), $this->__buildPackageLink(
         $url,
         $name,
         $rewriting
      ));
   }

   protected abstract function getLocationAttributeName();

   protected abstract function getTypeIndicator();

   public function getChecksum() {
      return md5($this->getAttribute($this->getLocationAttributeName()));
   }

   protected function __buildPackageLink($url, $name) {

      // Generate url if not given
      $url = ($url === null) ? Url::fromCurrent(true) : Url::fromString($url);
      return LinkGenerator::generateActionUrl($url, 'extensions::htmlheader', 'JsCss', array(
         'package' => $name . '.' . $this->getTypeIndicator()
      ));
   }

}

?>