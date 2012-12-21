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
import('tools::media::taglib', 'MediaInclusionTag');

/**
 * @package modules::usermanagement::pres::taglib
 * @class UmgtMediaInclusionTag
 *
 * Implements the image displaying taglib. Based on the <*:mediastream/> taglib.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.12.2008<br />
 */
class UmgtMediaInclusionTag extends MediaInclusionTag {

   public function __construct() {
      $this->addTagLib(new TagLib('modules::usermanagement::pres::taglib', 'UmgtMediaInclusionLanguageLabelTag', 'media', 'getstring'));
   }

   /**
    * @public
    *
    * Overwrites the parent's onParseTime().
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
    */
   public function onParseTime() {
      $this->extractTagLibTags();
   }

   /**
    * @public
    *
    * Overwrites the parent's onAfterAppend().
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
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
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
    */
   public function transform() {

      // setup the desired attributes
      $this->setAttribute('namespace', 'modules::usermanagement::pres::icons');

      // execute the parent's onParseTime()
      parent::onParseTime();

      // resolve missing alt attribute
      $alt = $this->getAttribute('alt');
      if ($alt === null) {
         $this->setAttribute('alt', $this->getAttribute('title'));
      }

      // generate the image tag
      $imgSrc = parent::transform();
      $attributes = $this->getAttributesAsString($this->getAttributes(), array('alt', 'title'));
      return '<img src="' . $imgSrc . '" ' . $attributes . ' class="icon" />';
   }

}