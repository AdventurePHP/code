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
use APF\extensions\htmlheader\biz\SimpleTitleNode;

/**
 * @package extensions::htmlheader::pres::taglib
 * @class HtmlHeaderAddTitleTag
 *
 * Taglib for adding a title to htmlheader.
 *
 * @example
 * <core:addtaglib class="APF\extensions\htmlheader\pres\taglib\HtmlHeaderAddTitleTag" prefix="htmlheader" name="addtitle" />
 * <htmlheader:addtitle [append="false"]>Testwebpage title</htmlheader:addtitle>
 * Set append to true, if you want to add the given tag-content at the end of
 * the existing title instead of overwriting it.
 *
 * @author Ralf Schubert
 * @version 0.1, 20.09.2009<br>
 * @version 0.2, 27.09.2009<br>
 */
class HtmlHeaderAddTitleTag extends Document {

   public function transform() {
      /* @var $header HtmlHeaderManager */
      $header = $this->getServiceObject('APF\extensions\htmlheader\biz\HtmlHeaderManager');

      $content = $this->getContent();
      if (!empty($content)) {

         if ($this->getAttribute('append') === 'true') {

            $title = $header->getTitle();
            /* @var $title SimpleTitleNode */

            if ($title !== null) {
               $titleContent = $title->getContent() . $content;
            } else {
               $titleContent = $content;
            }
         } else {
            $titleContent = $content;
         }

         $header->addNode(new SimpleTitleNode($titleContent));
      }
      return '';
   }

}
