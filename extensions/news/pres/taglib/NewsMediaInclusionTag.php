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
namespace APF\extensions\news\pres\taglib;

use APF\tools\media\taglib\MediaInclusionTag;

/**
 * @package APF\extensions\news\pres\taglib
 * @class NewsMediaInclusionTag
 *
 * Implements the image displaying tablib. Based on the *:mediastream taglib.
 *
 * @author Ralf Schubert
 */
class NewsMediaInclusionTag extends MediaInclusionTag {

   /**
    * @public
    *
    * Overwrites the parent's onParseTime().
    *
    * @author Ralf Schubert
    */
   public function onParseTime() {
      // setup the desired attribute to have more convenience
      $this->setAttribute('namespace', 'APF\modules\usermanagement\pres\icons');
      parent::onParseTime();
   }

   /**
    * @public
    *
    * Returns the image tag, that includes the image resource (front controller action). The
    * tag definition also contains size and border instruction.
    *
    * @return string The image tag.
    *
    * @author Ralf Schubert
    */
   public function transform() {

      // generate the image tag
      $imgSrc = parent::transform();

      $label = $this->getAttribute('label');
      $cfg = $this->getConfiguration('APF\extensions\news', 'labels.ini');
      $lang = $cfg->getSection($this->getLanguage());

      $title = $lang->getValue($label);
      $this->setAttribute('title', $title);
      $this->setAttribute('alt', $title);

      $width = $this->getAttribute('width', '20px');
      $height = $this->getAttribute('height', '20px');

      $attributes = $this->getAttributesAsString($this->getAttributes(), array('alt', 'title'));
      return '<img src="' . $imgSrc . '" ' . $attributes . ' style="width: ' . $width . '; height: ' . $height . '; border-width: 0px;" />';
   }

}
