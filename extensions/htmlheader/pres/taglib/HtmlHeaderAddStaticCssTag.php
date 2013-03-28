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
use APF\extensions\htmlheader\biz\StaticCssNode;

/**
 * @package extensions::htmlheader::pres::taglib
 * @class HtmlHeaderAddStaticCssTag
 *
 * Taglib for adding static stylesheets to the html header.
 *
 * @example
 * <core:addtaglib namespace="extensions::htmlheader::pres::taglib" class="HtmlHeaderAddStaticCssTag" prefix="htmlheader" name="addstaticcss" />
 * <htmlheader:addstaticcss file="..." />
 * <ul>
 *   <li>file: The source location of the stylesheet</li>
 * </ul>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.08.2010<br />
 */
class HtmlHeaderAddStaticCssTag extends Document {

   public function transform() {
      $header = &$this->getServiceObject('extensions::htmlheader::biz', 'HtmlHeaderManager');
      /* @var $header HtmlHeaderManager */

      $file = $this->getAttribute('file');
      if ($file == null) {
         throw new \InvalidArgumentException('[' . get_class($this) . '::onParseTime()] Please '
                  . 'provide the "file" attribute in order to add a static stylesheet.',
            E_USER_ERROR);
      }
      $node = new StaticCssNode($file);

      $media = $this->getAttribute('media');
      if ($media !== null) {
         $node->setAttribute('media', $media);
      }

      $node->setPriority($this->getAttribute('priority'));
      $header->addNode($node);

      return '';
   }

}
