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
namespace APF\tools\form\taglib;

/**
 * Implements the APF wrapper taglib for the HTML form reset button. Renders
 * various additional attributes, so you can specify css and style attributes
 * as desired.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 22.06.2009<br />
 */
class ResetButtonTag extends AbstractFormControl {

   public function __construct() {
      $this->attributeWhiteList[] = 'name';
      $this->attributeWhiteList[] = 'accesskey';
      $this->attributeWhiteList[] = 'disabled';
      $this->attributeWhiteList[] = 'tabindex';
      $this->attributeWhiteList[] = 'value';
   }

   /**
    * Generates the HTML code of the reset button.
    *
    * @return string The HTML representation of the reset button.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.06.2009<br />
    * Version 0.2, 02.01.2013 (Introduced form control visibility feature)<br />
    */
   public function transform() {
      if ($this->isVisible) {
         return '<input type="reset" ' . $this->getSanitizedAttributesAsString($this->attributes) . ' />';
      }

      return '';
   }

   public function reset() {
      // nothing to do as buttons contain no user input
   }

}
