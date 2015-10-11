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
namespace APF\tools\link\taglib;

use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;
use InvalidArgumentException;

/**
 * This taglib generates a html link tag based on the LinkGenerationTag taglib.
 *
 * @author Werner Liemberger wpublicmail [at] gmail DOT com
 * @version
 * Version 0.1, 06.08.2011<br />
 * Version 0.2, 22.11.2012 Werner Liemberger: add removed a:getstring and ignored href bug<br />
 * Version 0.3, 30.11.2012 Werner Liemberger: add title:getstring and move $this->extractTagLibTags(); in onPareTime() to the beginning<br />
 */
class HtmlLinkTag extends LinkGenerationTag {

   const TITLE_ATTRIBUTE_NAME = 'title';

   protected $attributeWhiteList = [
         'id',
         'style',
         'class',
         'onabort',
         'onclick',
         'ondblclick',
         'onmousedown',
         'onmouseup',
         'onmouseover',
         'onmousemove',
         'onmouseout',
         'onkeypress',
         'onkeydown',
         'onkeyup',
         'onblur',
         'tabindex',
         'dir',
         'accesskey',
         self::TITLE_ATTRIBUTE_NAME,
         'coords',
         self::HREF_ATTRIBUTE_NAME,
         'hreflang',
         'name',
         'rel',
         'rev',
         'shape',
         'target'
   ];

   public function onParseTime() {

      // generate URL using our parent implementation
      $this->setAttribute(self::HREF_ATTRIBUTE_NAME, parent::transform());

      // analyze tags which might add some attributes to this one.
      $this->extractTagLibTags();
   }

   public function transform() {
      // If no Content is set, this taglib tries to set the title as content.
      // If this is also missing it throws an Exception. This exception is needed,
      // because otherwise you will get an invalid html.
      $content = $this->getContent();
      if (empty($content)) {
         $content = $this->getAttribute(self::TITLE_ATTRIBUTE_NAME);
      }

      if (empty($content)) {
         throw new InvalidArgumentException('No anchor text available!');
      }

      $href = $this->getAttribute(self::HREF_ATTRIBUTE_NAME);
      if ($href === null) {
         return '';
      }

      // if the current link is active, this taglib adds the css class active.
      if ($this->isActive()) {
         $this->addAttribute('class', 'active', ' ');
      }

      return '<a ' . $this->getAttributesAsString($this->getAttributes(), $this->attributeWhiteList) . '>' . $content . '</a>';
   }

   /**
    * Checks if link is active (=we are already on the target page) or not.
    *
    * @return boolean
    *
    * @author Werner Liemberger wpublicmail [at] gmail DOT com
    * @version
    * Version 0.1, 30.11.2012<br />
    */
   public function isActive() {

      $currentUrl = Url::fromCurrent(true);
      $targetUrl = Url::fromString($this->getAttribute(self::HREF_ATTRIBUTE_NAME));

      // ID#201: re-map and sort query parameters to allow parameter mixing
      $currentQuery = $currentUrl->getQuery();
      ksort($currentQuery);
      $currentUrl->setQuery($currentQuery);

      $targetQuery = $targetUrl->getQuery();
      ksort($targetQuery);
      $targetUrl->setQuery($targetQuery);

      if (substr_count(LinkGenerator::generateUrl($currentUrl), LinkGenerator::generateUrl($targetUrl)) > 0) {
         return true;
      }

      return false;
   }

   protected function getUrlParameters() {

      // only non-white-list parameters are allowed as URL parameters
      // to not interfere with HTML attributes
      $attributes = [];

      foreach ($this->getAttributes() as $key => $value) {
         if (!in_array($key, $this->attributeWhiteList)) {
            $attributes[$key] = $value;
         }
      }

      return $attributes;
   }

}
