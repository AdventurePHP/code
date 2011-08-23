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
import('tools::html::taglib', 'html_taglib_link');

/**
 * @package tools::html::taglib
 * @class html_taglib_a
 *
 * Taglib erzeugt einen html Link basierend auf den übermittelten Parametern und
 * verwendet dazu den html_taglib_link.
 *
 * @author: Werner Liemberger wpublicmail [at] gmail DOT com
 * @version 0.1, 06.08.2011<br />
 */
class html_taglib_a extends html_taglib_link {

   protected $attributeWhiteList = array('id', 'style', 'class', 'onabort',
                                         'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover',
                                         'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup',
                                         'tabindex', 'dir', 'accesskey', 'title', 'charset', 'coords',
                                         'href', 'hreflang', 'name', 'rel', 'rev', 'shape', 'target',
                                         'xml:lang', 'onblur');

   public function onParseTime() {
      /*
      * Alle Attribute die auch in der whitelist sind dort eintragen und dann leeren,
      * damit sie nicht im url eingebaut werden.
      */

      foreach ($this->attributeWhiteList as $elem) {
         $attr = $this->getAttribute($elem, null);
         if ($attr != null) {
            $this->attributeWhiteList[$elem] = $attr;
            $this->setAttribute($attr, null);
         }
      }

      $this->attributeWhiteList['href'] = parent::transform();
      if ($this->attributeWhiteList['href'] === null) {
         throw new InvalidArgumentException('[html_taglib_a::onParseTime()] The Attribute "href" is missing. Please provide the destination!', E_USER_ERROR);
      }
   }

   public function transform() {
      /*
      * Wenn kein Content vorhanden ist, der den anzuklickenden Text darstellt,
      * wird versucht den title zu setzten. Wenn dieser ebenfalls fehlt, wird eine
      * Fehlermeldung erzeugt.
      */
      if ($this->__Content == '') {
         $this->__Content = $this->attributeWhiteList['title'];
      }
      if ($this->__Content == null) {
         throw new Exception('Es ist kein Text vorhanden der angeklickt werden kann.');
      }

      /*
      * Fügt bei vorhandensein des aktuellen Links im Url die CSS Klasse active hinzu.
      */
      if (substr_count($_SERVER['REQUEST_URI'], $this->attributeWhiteList['href']) > 0) {
         $this->setAttribute('class', $this->attributeWhiteList['class'] . ' active');
      }
      return '<a ' . $this->getAttributesAsString($this->attributeWhiteList) . '>' . $this->__Content . '</a>';
   }
}

?>