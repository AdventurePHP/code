<?php
namespace APF\extensions\htmlheader\pres\taglib;

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
use APF\core\pagecontroller\Document;
use APF\extensions\htmlheader\biz\CssContentNode;
use APF\extensions\htmlheader\biz\HtmlHeaderManager;

/**
 * @package extensions::htmlheader::pres::taglib
 * @class HtmlHeaderAddCssContentTag
 *
 * Taglib for adding stylesheets content to the html header.
 *
 * @example
 * <core:addtaglib namespace="extensions::htmlheader::pres::taglib" class="HtmlHeaderAddCssContentTag" prefix="htmlheader" name="addcsscontent" />
 * <htmlheader:addcsscontent>
 *   ... css code ...
 * </htmlheader:addcsscontent>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.08.2010<br />
 */
class HtmlHeaderAddCssContentTag extends Document {

   public function transform() {
      /* @var $header HtmlHeaderManager */
      $header = &$this->getServiceObject('APF\extensions\htmlheader\biz\HtmlHeaderManager');

      $node = new CssContentNode($this->getContent());
      $node->setPriority($this->getAttribute('priority'));
      $header->addNode($node);

      return '';
   }

}
