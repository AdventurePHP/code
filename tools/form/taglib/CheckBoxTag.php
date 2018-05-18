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
 * Represents an APF form checkbox.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 13.01.2007<br />
 * Version 0.2, 12.02.2010 (Introduced attribute black and white listing)<br />
 * Version 0.3, 21.04.2016 (ID#298: fixed defect with check state is reset for statically checked boxes on submit)<br />
 */
class CheckBoxTag extends AbstractFormControl {

   public function __construct() {
      $this->attributeWhiteList[] = 'name';
      $this->attributeWhiteList[] = 'accesskey';
      $this->attributeWhiteList[] = 'disabled';
      $this->attributeWhiteList[] = 'tabindex';
      $this->attributeWhiteList[] = 'value';
      $this->attributeWhiteList[] = 'checked';
   }

   public function onAfterAppend() {

      $form = $this->getForm();
      if ($form->isSent()) {
         $name = $this->getAttribute('name');
         if ($this->getRequest()->hasParameter($name)) {
            $this->check();
         } else {
            // ID#236: removed check on whether form is sent or not to avoid issues with the APF 3.0 parser.
            $this->uncheck();
         }
      } else {
         if ($this->getAttribute('checked') === 'checked') {
            $this->check();
         } else {
            $this->uncheck();
         }
      }

   }

   /**
    * Returns the HTML code of the checkbox.
    *
    * @return string The HTML code of the checkbox.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 13.01.2007<br />
    * Version 0.2, 11.02.2007 (Moved presetting and validation to the onAfterAppend() method)<br />
    * Version 0.3, 02.01.2013 (Introduced form control visibility feature)<br />
    */
   public function transform() {
      if ($this->isVisible) {
         return '<input type="checkbox" ' . $this->getSanitizedAttributesAsString($this->attributes) . ' />';
      }

      return '';
   }

   public function reset() {
      $this->uncheck();

      return $this;
   }

}
