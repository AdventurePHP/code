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

use APF\tools\form\FormException;
use APF\tools\form\HtmlForm;

/**
 * Represents an APF form button.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 05.01.2007<br />
 * Version 0.2, 14.04.2007 (Added the onAfterAppend() method)<br />
 * Version 0.3, 12.02.2010 (Introduced attribute black and white listing)<br />
 */
class ButtonTag extends AbstractFormControl {

   public function __construct() {
      $this->attributeWhiteList[] = 'name';
      $this->attributeWhiteList[] = 'accesskey';
      $this->attributeWhiteList[] = 'disabled';
      $this->attributeWhiteList[] = 'tabindex';
      $this->attributeWhiteList[] = 'value';
   }

   /**
    * Indicates, if the form was sent.
    *
    * @since 0.2
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 14.04.2007<br />
    */
   public function onParseTime() {

      $buttonName = $this->getAttribute('name');
      $form = & $this->getForm();
      if ($buttonName === null) {
         $formName = $form->getAttribute('name');
         throw new FormException('[ButtonTag::onAfterAppend()] Missing required attribute '
               . '"name" in &lt;form:button /&gt; tag in form "' . $formName . '". '
               . 'Please check your form definition!', E_USER_ERROR);
      }

      // check name attribute in request to indicate, that the
      // form was sent. Mark button as sent, too. Due to potential
      // XSS issues, we distinguish between GET and POST requests
      $method = strtolower($form->getAttribute(HtmlForm::METHOD_ATTRIBUTE_NAME));
      if ($method == HtmlForm::METHOD_POST_VALUE_NAME) {
         if (isset($_POST[$buttonName])) {
            $this->controlIsSent = true;
         }
      } else {
         if (isset($_GET[$buttonName])) {
            $this->controlIsSent = true;
         }
      }

      // parse button:getstring tags
      $this->extractTagLibTags();
   }

   /**
    * Returns the HTML code of the button.
    *
    * @return string The HTML code of the button.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 05.01.2007<br />
    */
   public function transform() {
      if ($this->isVisible) {
         return '<input type="submit" ' . $this->getSanitizedAttributesAsString($this->getAttributes()) . ' />';
      }

      return '';
   }

   public function reset() {
      // nothing to do as buttons contain no user input.
   }

}