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
import('tools::form::taglib', 'form_taglib_button');

/**
 * @package tools::form::taglib
 * @class form_taglib_imagebutton
 *
 * Represents an image button.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.09.2009<br />
 * Version 0.2, 16.10.2009 (Made subclass of "normal" button to enable validation/filtering!)<br />
 * Version 0.3, 12.02.2010 (Introduced attribute black and white listing)<br />
 */
class form_taglib_imagebutton extends form_taglib_button {

   public function __construct() {
      parent::__construct();
      $this->attributeWhiteList[] = 'src';
      $this->attributeWhiteList[] = 'alt';
   }

   /**
    * @public
    *
    * Generates the HTML code of the image button.
    *
    * @return string Image button html.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2009<br />
    */
   public function transform() {
      return '<input type="image" '
      . $this->getSanitizedAttributesAsString($this->__Attributes)
      . ' />';
   }

}
?>