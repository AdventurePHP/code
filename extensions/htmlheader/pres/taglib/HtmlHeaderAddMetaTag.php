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
use APF\extensions\htmlheader\biz\HtmlHeaderManager;
use APF\extensions\htmlheader\biz\SimpleMetaNode;

/**
 * @package extensions::htmlheader::pres::taglib
 * @class HtmlHeaderAddMetaTag
 *
 * Taglib for adding meta nodes to htmlheader.
 *
 * @example
 * <core:addtaglib namespace="extensions::htmlheader::pres::taglib" class="HtmlHeaderAddMetaTag" prefix="htmlheader" name="addmeta" />
 * <htmlheader:addmeta name="" content="" />
 *
 * @author Ralf Schubert
 * @version 0.1, 11.10.2011<br>
 */
class HtmlHeaderAddMetaTag extends Document {

   public function transform() {
      /* @var $header HtmlHeaderManager */
      $header = $this->getServiceObject('extensions::htmlheader::biz', 'HtmlHeaderManager');

      $header->addNode(
         new SimpleMetaNode(
            $this->getAttribute('name'),
            $this->getAttribute('content')
         )
      );
      return '';
   }

}
