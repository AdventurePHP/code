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
import('tools::media::taglib', 'ui_mediastream');

/**
 * @package extensions::news::pres::taglib
 * @class news_taglib_media
 *
 * Implements the image displaying tablib. Based on the *:mediastream taglib.
 *
 * @author Ralf Schubert
 */
class news_taglib_media extends ui_mediastream {

   /**
    * @public
    *
    * Overwrites the parent's onParseTime().
    *
    * @author Ralf Schubert
    */
   public function onParseTime() {

   }

   /**
    * @public
    *
    * Overwrites the parent's onAfterAppend().
    * Ralf Schubert
    */
   public function onAfterAppend() {

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

      // setup the desired attributes
      $this->setAttribute('namespace', 'modules::usermanagement::pres::icons');

      // execute the parent's onParseTime()
      parent::onParseTime();

      // generate the image tag
      $imgsrc = parent::transform();
      
      $Label = $this->getAttribute('label');
      $Cfg = $this->getConfiguration('extensions::news', 'labels');
      $Lang = $Cfg->getSection($this->getLanguage());
      
      $Title = $Lang->getValue($Label);
      $this->setAttribute('title', $Title);
      $this->setAttribute('alt', $Title);
      
      $Width = $this->getAttribute('width', '20px');
      $Height = $this->getAttribute('height', '20px');
      
      $attributes = $this->getAttributesAsString($this->getAttributes(), array('alt', 'title'));
      return '<img src="' . $imgsrc . '" ' . $attributes . ' style="width: ' . $Width . '; height: ' . $Height . '; border-width: 0px;" />';
   }

}

?>