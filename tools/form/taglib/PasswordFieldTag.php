<?php
namespace APF\tools\form\taglib;

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
use APF\tools\form\taglib\TextFieldTag;

/**
 * @package APF\tools\form\taglib
 * @class PasswordFieldTag
 *
 * Represents a APF password field.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 12.01.2007<br />
 * Version 0.2, 12.01.2007<br />
 * Version 0.3, 12.02.2010 (Introduced attribute black and white listing)<br />
 */
class PasswordFieldTag extends TextFieldTag {

   public function __construct() {
      parent::__construct();
   }

   /**
    * @public
    *
    * Returns the HTML source code of the text field.
    *
    * @return string HTML code of the password field.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 12.01.2007<br />
    * Version 0.2, 11.02.2007 (Moved presetting and validation to onAfterAppend())<br />
    * Version 0.3, 02.01.2013 (Introduced form control visibility feature)<br />
    */
   public function transform() {
      if ($this->isVisible) {
         return '<input type="password" ' . $this->getSanitizedAttributesAsString($this->attributes) . ' />';
      }
      return '';
   }

}
