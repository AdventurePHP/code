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
namespace APF\extensions\htmlheader\pres\taglib;

use APF\core\pagecontroller\Document;
use APF\extensions\htmlheader\biz\CssImageNode;
use APF\extensions\htmlheader\biz\HtmlHeaderManager;
use InvalidArgumentException;

/**
 * Taglib for adding an image to the html header.
 *
 * @example
 * <core:addtaglib class="APF\extensions\htmlheader\pres\taglib\HtmlHeaderAddCssImageTag" prefix="htmlheader" name="addcssimage" />
 * <htmlheader:addcssimage rel="icon" href="favicon.png" type="image/png" />
 *
 * @author Werner Liemberger
 * @version
 * Version 0.1, 25.8.2011<br />
 */
class HtmlHeaderAddCssImageTag extends Document {

   public function transform() {
      /* @var $header HtmlHeaderManager */
      $header = $this->getServiceObject(HtmlHeaderManager::class);

      $href = $this->getAttribute('href');
      if ($href == null) {
         throw new InvalidArgumentException('[' . get_class($this) . '::onParseTime()] Please provide the "href" '
               . 'attribute in order to add a Css image.', E_USER_ERROR);
      }
      $rel = $this->getAttribute('rel', 'icon');
      $type = $this->getAttribute('type');
      $node = new CssImageNode($href, $rel, $type);

      $node->setPriority($this->getAttribute('priority'));
      $header->addNode($node);

      return '';
   }

}
