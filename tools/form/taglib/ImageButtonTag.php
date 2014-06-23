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
use APF\tools\form\FormException;
use APF\tools\form\taglib\ButtonTag;

/**
 * @package APF\tools\form\taglib
 * @class ImageButtonTag
 *
 * Represents an image button.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.09.2009<br />
 * Version 0.2, 16.10.2009 (Made subclass of "normal" button to enable validation/filtering!)<br />
 * Version 0.3, 12.02.2010 (Introduced attribute black and white listing)<br />
 */
class ImageButtonTag extends ButtonTag {

   public function __construct() {
      parent::__construct();
      $this->attributeWhiteList[] = 'src';
      $this->attributeWhiteList[] = 'alt';
   }

   /**
    * @public
    * @since 1.17
    *
    * Re-implements the onParseTime() method of the ButtonTag to respect the difference
    * between buttons and normal buttons (name + _x/_y in request).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2013 (Bug 1261: re-implementation due to different browser behaviour for image buttons)<br />
    */
   public function onParseTime() {

      $buttonName = $this->getAttribute('name');
      if ($buttonName === null) {
         $formName = $this->getParentObject()->getAttribute('name');
         throw new FormException('[ImageButtonTag::onAfterAppend()] Missing required attribute '
               . '"name" in &lt;form:imagebutton /&gt; tag in form "' . $formName . '". '
               . 'Please check your form definition!', E_USER_ERROR);
      }

      // check name attribute in request to indicate, that the
      // form was sent. Mark button as sent, too. Due to potential
      // XSS issues, we distinguish between GET and POST requests
      $method = strtolower($this->getParentObject()->getAttribute(HtmlFormTag::$METHOD_ATTRIBUTE_NAME));
      if ($method == HtmlFormTag::$METHOD_POST_VALUE_NAME) {
         if (isset($_POST[$buttonName . '_x']) && isset($_POST[$buttonName . '_y'])) {
            $this->controlIsSent = true;
         }
      } else {
         if (isset($_GET[$buttonName . '_x']) && isset($_GET[$buttonName . '_y'])) {
            $this->controlIsSent = true;
         }
      }
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
    * Version 0.2, 02.01.2013 (Introduced form control visibility feature)<br />
    */
   public function transform() {
      if ($this->isVisible) {
         return '<input type="image" '
               . $this->getSanitizedAttributesAsString($this->attributes)
               . ' />';
      }
      return '';
   }

}
