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
import('extensions::htmlheader::biz', 'CssContentNode');

/**
 * @package extensions::htmlheader::pres::taglib
 * @class htmlheader_taglib_addcsscontent
 *
 * Taglib for adding stylesheets content to the html header.
 *
 * @example
 * <core:addtaglib namespace="extensions::htmlheader::pres::taglib" class="htmlheader_taglib_addcsscontent" prefix="htmlheader" name="addcsscontent" />
 * <htmlheader:addcsscontent>
 *   ... css code ...
 * </htmlheader:addcsscontent>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.08.2010<br />
 */
class htmlheader_taglib_addcsscontent extends Document {

   public function transform() {
      $header = &$this->getServiceObject('extensions::htmlheader::biz', 'HtmlHeaderManager');
      /* @var $header HtmlHeaderManager */

      $node = new CssContentNode($this->getContent());
      $node->setPriority($this->getAttribute('priority'));
      $header->addNode($node);

      return '';
   }

}
