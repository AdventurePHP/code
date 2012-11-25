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
import('tools::link::taglib', 'LinkLanguageLabelTag');
import('tools::link::taglib', 'LinkGenerationTag');

/**
 * @package tools::link::taglib
 * @class HtmlLinkTag
 *
 * This taglib generates a html link tag based on the LinkGenerationTag taglib.
 *
 * @author Werner Liemberger wpublicmail [at] gmail DOT com
 * @version
 * Version 0.1, 06.08.2011<br />
 * Version 0.2, 22.11.2012 Werner Liemberger: removed a:getstring and ignored href bug<br />
 */
class HtmlLinkTag extends LinkGenerationTag {

   protected $attributeList = array('id' => null, 'style' => null, 'class' => null, 'onabort' => null,
      'onclick' => null, 'ondblclick' => null, 'onmousedown' => null, 'onmouseup' => null,
      'onmouseover' => null, 'onmousemove' => null, 'onmouseout' => null,
      'onkeypress' => null, 'onkeydown' => null, 'onkeyup' => null, 'tabindex' => null,
      'dir' => null, 'accesskey' => null, 'title' => null, 'charset' => null,
      'coords' => null, 'href' => null, 'hreflang' => null, 'name' => null, 'rel' => null,
      'rev' => null, 'shape' => null, 'target' => null, 'xml:lang' => null, 'onblur' => null);

   public function __construct() {
      $this->__TagLibs[] = new TagLib('tools::link::taglib', 'LinkLanguageLabelTag', 'a', 'getstring');
   }

   public function onParseTime() {
      // Move all vales from parameters which are in the white list into this array
      // and remove them from the attribute array, because they should not be part oft the url.
      foreach ($this->attributeList as $key => $elem) {
         $attr = $this->getAttribute($key, null);
         if ($attr != null) {
            $this->attributeList[$key] = $attr;
            if ($key != 'href') {
               $this->deleteAttribute($key);
            }
         }
      }

      $this->attributeList['href'] = parent::transform();
      if ($this->attributeList['href'] === null) {
         throw new InvalidArgumentException('[HtmlLinkTag::onParseTime()] The Attribute "href" is missing. '
               . 'Please provide the destination!', E_USER_ERROR);
      }
      $this->__extractTagLibTags();
   }

   public function transform() {
      // If no Content is set, this taglib tries to set the title as content.
      // If this is also missing it throws an Exception. This exception is needed,
      // because otherwise you will get an invalid html.
      $content = $this->getContent();
      if (empty($content)) {
         $content = $this->attributeList['title'];
      }

      if (empty($content)) {
         throw new InvalidArgumentException('No anchor text available!');
      }

      // if the current link is active, this taglib adds the css class active.
      if (!isset($this->attributeList['href'])) {
         return '';
      }
      if (substr_count(str_replace('&', '&amp;', Registry::retrieve('apf::core', 'CurrentRequestURL')), $this->attributeList['href']) > 0) {
         $this->attributeList['class'] = $this->attributeList['class'] . ' active';
      }

      foreach ($this->attributeList as $key => $elem) {
         if ($elem === null) {
            unset($this->attributeList[$key]);
         }
      }

      return '<a ' . $this->getAttributesAsString($this->attributeList) . '>' . $content . '</a>';
   }

}
