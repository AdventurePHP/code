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

use DateTime;

/**
 * Represents a HTML5 date field.
 *
 * Usage:
 * <code>
 * <form:html5-date name="..." />
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.10.2017<br />
 */
class Html5DateFieldTag extends TextFieldTag {

   const DATE_FORMAT_PATTERN = 'Y-m-d';

   public function __construct() {
      parent::__construct();
      $this->attributeWhiteList += [
            'autocomplete',
            'list',
            'readonly',
            'step',
            'min',
            'max',
            'required'
      ];
   }

   /**
    * @param DateTime|string $date The date to initialize the control with (either DateTime instance or string).
    */
   public function setDate($date) {

      if ($date === null) {
         $this->setAttribute('value', null);
         return;
      }

      if (!($date instanceof DateTime)) {
         $date = DateTime::createFromFormat(self::DATE_FORMAT_PATTERN, $date);
      }

      $this->setAttribute('value', $date->format(self::DATE_FORMAT_PATTERN));

   }

   /**
    * Returns the current value of the date control as DateTime instance.
    *
    * @return DateTime The current date representation.
    */
   public function getDate() {
      $value = $this->getAttribute('value');

      if ($value === null) {
         return null;
      }

      return new DateTime($value);
   }

   public function getValue() {
      return $this->getDate();
   }

   public function &setValue($value) {
      $this->setDate($value);

      return $this;
   }

   public function transform() {
      if ($this->isVisible) {
         return '<input type="date" ' . $this->getSanitizedAttributesAsString($this->attributes) . ' />';
      }

      return '';
   }

}
