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
 * Represents a APF text area.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 13.01.2007<br />
 */
class TextAreaTag extends AbstractFormControl {

   public function __construct() {
      $this->attributeWhiteList[] = 'name';
      $this->attributeWhiteList[] = 'cols';
      $this->attributeWhiteList[] = 'rows';
      $this->attributeWhiteList[] = 'tabindex';
      $this->attributeWhiteList[] = 'disabled';
      $this->attributeWhiteList[] = 'readonly';
   }

   /**
    * Returns the HTML source code of the text area.
    *
    * @return string HTML code of the text area.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 05.01.2007<br />
    * Version 0.2, 11.02.2007 (Presetting and validation moved to onAfterAppend())<br />
    * Version 0.3, 02.01.2013 (Introduced form control visibility feature)<br />
    */
   public function transform() {
      if ($this->isVisible) {
         return '<textarea ' . $this->getSanitizedAttributesAsString($this->attributes) . '>'
         . $this->content . '</textarea>';
      }

      return '';
   }

   /**
    * Re-implements the retrieving of values for text area, because
    * the text area contains it's value in the content, not in an
    * attribute.
    *
    * @return string The current value or content of the control.
    *
    * @since 1.14
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 26.07.2011<br />
    */
   public function getValue() {
      return $this->content;
   }

   /**
    * Re-implements the setting of values for text area, because
    * the text area contains it's value in the content, not in an
    * attribute.
    *
    * @param string $value
    *
    * @return $this This instance for further usage.
    *
    * @since 1.14
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 26.07.2011<br />
    */
   public function setValue($value) {
      $this->content = $value;

      return $this;
   }

   /**
    * Let's check if the form:area was filled with content.
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
      return $this->getContent() == null ? false : true;
   }

   public function reset() {
      $this->content = null;
   }

   /**
    * Implements the presetting method for the text area.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 13.01.2007<br />
    */
   protected function presetValue() {
      $value = $this->getRequest()->getParameter($this->getAttribute('name'));
      if ($value !== null) {
         $this->content = $value;
      }
   }

}
