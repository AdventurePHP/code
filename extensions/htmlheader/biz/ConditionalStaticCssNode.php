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

import('extensions::htmlheader::biz', 'CssNode');
import('extensions::htmlheader::biz', 'StaticCssNode');

/**
 * @package extensions::htmlheader::biz
 * @class ConditionalStaticCssNode
 *
 * Implements a conditional static css node.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.08.2010<br />
 */
class ConditionalStaticCssNode extends StaticCssNode implements CssNode {

   private $condition;

   public function __construct($file, $condition) {
      parent::__construct($file);
      $this->condition = $condition;
   }

   public function  transform() {
      return '<!--[if ' . $this->condition . ']>' . parent::transform() . '<![endif]-->';
   }

}

?>