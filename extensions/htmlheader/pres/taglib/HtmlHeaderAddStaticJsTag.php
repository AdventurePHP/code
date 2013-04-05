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
use APF\extensions\htmlheader\biz\StaticJsNode;

/**
 * @package extensions::htmlheader::pres::taglib
 * @class HtmlHeaderAddStaticJsTag
 *
 * Taglib for adding static java script to the html header.
 *
 * @example
 * <core:addtaglib class="APF\extensions\htmlheader\pres\taglib\HtmlHeaderAddStaticJsTag" prefix="htmlheader" name="addstaticcss" />
 * <htmlheader:addstaticjs file="..." />
 * <ul>
 *   <li>file: The source location of the stylesheet</li>
 * </ul>
 *
 * @author Ralf Schubert
 * @version
 * 0.1, 20.09.2009 <br />
 * 0.2, 27.02.2010 (Added attributes for external file support) <br />
 */
class HtmlHeaderAddStaticJsTag extends Document {

   public function transform() {
      $header = &$this->getServiceObject('APF\extensions\htmlheader\biz\HtmlHeaderManager');
      /* @var $header HtmlHeaderManager */

      $file = $this->getAttribute('file');
      if ($file == null) {
         throw new \InvalidArgumentException('[' . get_class($this) . '::onParseTime()] Please '
                  . 'provide the "file" attribute in order to add a static stylesheet.',
            E_USER_ERROR);
      }

      $node = new StaticJsNode($file);

      if (strtolower($this->getAttribute('appendtobody')) === 'true') {
         $node->setAppendToBody(true);
      }

      $node->setPriority($this->getAttribute('priority'));
      $header->addNode($node);

      return '';
   }

}
