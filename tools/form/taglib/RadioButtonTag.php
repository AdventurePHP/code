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

/**
 * @package APF\tools\form\taglib
 * @class RadioButtonTag
 *
 * Represents a APF radio button.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 13.01.2007<br />
 * Version 0.2, 12.02.2010 (Introduced attribute black and white listing)<br />
 */
class RadioButtonTag extends AbstractFormControl {

   public function __construct() {
      $this->attributeWhiteList[] = 'name';
      $this->attributeWhiteList[] = 'value';
      $this->attributeWhiteList[] = 'tabindex';
      $this->attributeWhiteList[] = 'accesskey';
      $this->attributeWhiteList[] = 'checked';
      $this->attributeWhiteList[] = 'disabled';
      $this->attributeWhiteList[] = 'readonly';
   }

   /**
    * @public
    *
    * Returns the HTML code of the radio button.
    *
    * @return string The HTML code of the radio button
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 13.01.2007<br />
    * Version 0.2, 11.02.2007 (Moved presetting and validation to onAfterAppend())<br />
    * Version 0.3, 02.01.2013 (Introduced form control visibility feature)<br />
    */
   public function transform() {
      if ($this->isVisible) {
         return '<input type="radio" ' . $this->getSanitizedAttributesAsString($this->attributes) . ' />';
      }
      return '';
   }

   /**
    * @protected
    *
    * Re-implements the presetValue() method for the radio button.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 13.01.2007<br />
    * Version 0.2, 13.01.2009 (Bug-fix: now the 'checked' attribute is deleted, that in case of a manually defined 'ckecked' the button could be unchecked)<br />
    * Version 0.3, 05.09.2009 (Added check for attributes "name" and "value" beeing present)<br />
    */
   protected function presetValue() {

      $name = $this->getAttribute('name');
      $value = $this->getAttribute('value');

      // Check for name and value beeing present. Otherwise
      // presetting will fail!
      if ($name === null || $value === null) {
         $formName = $this->getParentObject()->getAttribute('name');
         throw new FormException('[RadioButtonTag::presetValue()] Attribute "name" and or "value" is '
               . 'missing for &lt;form:radio /&gt; definition within form "' . $formName . '". '
               . 'Please check your tag definition!', E_USER_ERROR);
      }

      if (isset($_REQUEST[$name])) {

         // pre-check, whether the value is contained in the request or the
         // value is "on" for tag definitions without a value attribute.
         if ($_REQUEST[$name] == $value || $_REQUEST[$name] == 'on') {
            $this->check();
         } else {
            $this->uncheck();
         }

      }

   }

}
