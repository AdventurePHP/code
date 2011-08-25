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
import('tools::link::taglib', 'html_taglib_link');

/**
 * @package tools::link::taglib
 * @class html_taglib_a
 *
 * This taglib generates a html link tag based on the html_taglib_link taglib.
 *
 * @author: Werner Liemberger wpublicmail [at] gmail DOT com
 * @version
 * Version 0.1, 06.08.2011<br />
 */
class html_taglib_a extends html_taglib_link {

   protected $attributeWhiteList = array('id', 'style', 'class', 'onabort',
                                         'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover',
                                         'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup',
                                         'tabindex', 'dir', 'accesskey', 'title', 'charset', 'coords',
                                         'href', 'hreflang', 'name', 'rel', 'rev', 'shape', 'target',
                                         'xml:lang', 'onblur');

   public function onParseTime() {
      // Move all vales from parameters which are in the white list into this array
      // and remove them from the attribute array, because they should not be part oft the url.
      foreach ($this->attributeWhiteList as $elem) {
         $attr = $this->getAttribute($elem, null);
         if ($attr != null) {
            $this->attributeWhiteList[$elem] = $attr;
            $this->deleteAttribute($attr);
         }
      }

      $this->attributeWhiteList['href'] = parent::transform();
      if ($this->attributeWhiteList['href'] === null) {
         throw new InvalidArgumentException('[html_taglib_a::onParseTime()] The Attribute "href" is missing. Please provide the destination!', E_USER_ERROR);
      }
   }

   public function transform() {
      // If no Content is set, this taglib tries to set the title as content.
      // If this is also missing it throws an Exception. This exception is needed,
      // because otherwise you will get an invalid html.
      $content = $this->getContent();
      if (empty($content)) {
         $content = $this->attributeWhiteList['title'];
      }

      if (empty($content)) {
         throw new InvalidArgumentException('No anchor text available!');
      }

      // if the current link is active, this taglib adds the css class active.
      if (substr_count($_SERVER['REQUEST_URI'], $this->attributeWhiteList['href']) > 0) {
         $this->setAttribute('class', $this->attributeWhiteList['class'] . ' active');
      }
      return '<a ' . $this->getAttributesAsString($this->attributeWhiteList) . '>' . $content . '</a>';
   }
}

?>