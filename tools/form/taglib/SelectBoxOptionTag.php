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

/**
 * @package tools::form::taglib
 * @class SelectBoxOptionTag
 *
 * Represents a select option of an APF select field.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 07.01.2007<br />
 * Version 0.2, 12.01.2007<br />
 * Version 0.3, 12.02.2010 (Introduced attribute black and white listing)<br />
 */
class SelectBoxOptionTag extends AbstractFormControl {

   public function __construct() {
      $this->attributeWhiteList[] = 'value';
      $this->attributeWhiteList[] = 'selected';
      $this->attributeWhiteList[] = 'label';
      $this->attributeWhiteList[] = 'disabled';
   }

   /**
    * @protected
    *
    * Overwrites the <em>onParseTime()</em> methode, because here is nothing to do.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function onParseTime() {
   }

   /**
    * @public
    *
    * Returns the HTML code of the option.
    *
    * @return string The HTML source code.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    */
   public function transform() {
      return '<option ' . $this->getSanitizedAttributesAsString($this->__Attributes) . '>'
            . $this->__Content . '</option>';
   }

}
