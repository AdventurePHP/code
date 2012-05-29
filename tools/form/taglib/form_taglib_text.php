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
 * @class form_taglib_text
 *
 * Represents a APF text field.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 05.01.2007<br />
 * Version 0.2, 12.01.2007<br />
 * Version 0.3, 12.02.2010 (Introduced attribute black and white listing)<br />
 */
class form_taglib_text extends form_control {

   public function __construct() {
      $this->attributeWhiteList[] = 'name';
      $this->attributeWhiteList[] = 'value';
      $this->attributeWhiteList[] = 'tabindex';
      $this->attributeWhiteList[] = 'disabled';
      $this->attributeWhiteList[] = 'readonly';
      $this->attributeWhiteList[] = 'size';
      $this->attributeWhiteList[] = 'maxlength';
   }

   /**
    * @public
    *
    * Returns the HTML source code of the text field.
    *
    * @return string HTML code of the text field
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 05.01.2007<br />
    * Version 0.2, 11.02.2007 (Moved presetting and validation to onAfterAppend())<br />
    * Version 0.3, 13.02.2010 (Introduced attribute white listing)<br />
    */
   public function transform() {
      return '<input type="text" ' . $this->getSanitizedAttributesAsString($this->__Attributes) . ' />';
   }
   
   /**
    * @public
    *
    * Let's check if the form:text was filled with content.
    *
    * @return bool True in case the control is filled, false otherwise.
    *
    * @since 1.15
    *
    * @author dave
    * @version
    * Version 0.1, 20.09.2011<br />
    */
   public function isFilled() {
      return $this->getValue() == null ? false : true;
   }

}
