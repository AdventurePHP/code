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

   protected $attributeList = array('id' => null, 'style' => null, 'class' => null, 'onabort' => null,
                                    'onclick' => null, 'ondblclick' => null, 'onmousedown' => null, 'onmouseup' => null,
                                    'onmouseover' => null, 'onmousemove' => null, 'onmouseout' => null,
                                    'onkeypress' => null, 'onkeydown' => null, 'onkeyup' => null, 'tabindex' => null,
                                    'dir' => null, 'accesskey' => null, 'title' => null, 'charset' => null,
                                    'coords' => null, 'href' => null, 'hreflang' => null, 'name' => null, 'rel' => null,
                                    'rev' => null, 'shape' => null, 'target' => null, 'xml:lang' => null, 'onblur' => null);

   public function onParseTime() {
      // Move all vales from parameters which are in the white list into this array
      // and remove them from the attribute array, because they should not be part oft the url.
      foreach ($this->attributeList as $key => $elem) {
         $attr = $this->getAttribute($key, null);
         if ($attr != null) {
            $this->attributeList[$key] = $attr;
            $this->deleteAttribute($attr);
         }
      }

      $this->attributeList['href'] = parent::transform();
      if ($this->attributeList['href'] === null) {
         throw new InvalidArgumentException('[html_taglib_a::onParseTime()] The Attribute "href" is missing. '
                                            . 'Please provide the destination!', E_USER_ERROR);
      }
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
      $qs = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
      $href = strstr($this->attributeList['href'], '#', true);
      if(!$href) {
          $href = $this->attributeList['href'];
      }
      if($qs !== '') {
         if (substr_count($href, $qs) > 0) {
             $this->attributeList['class'] = $this->attributeList['class'] . ' active';
         } 
      }
      elseif(substr($href, -1) === '/') {
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

?>