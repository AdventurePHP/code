<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\tools\form\taglib;

/**
 * Represents a HTML5 search field.
 *
 * Usage:
 * <code>
 * <form:html5-search name="..." />
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.10.2017<br />
 */
class Html5SearchFieldTag extends TextFieldTag {

   public function __construct() {
      parent::__construct();
      $this->attributeWhiteList = array_merge(
            $this->attributeWhiteList, [
                  'autocomplete',
                  'list',
                  'maxlength',
                  'minlength',
                  'pattern',
                  'placeholder',
                  'required',
                  'size'
            ]
      );
   }

   public function transform() {
      if ($this->isVisible) {
         return '<input type="search" ' . $this->getSanitizedAttributesAsString($this->attributes) . ' />';
      }

      return '';
   }

}
