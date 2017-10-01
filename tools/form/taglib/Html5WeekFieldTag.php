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
 * Represents a HTML5 week field.
 *
 * Usage:
 * <code>
 * <form:html5-time name="..." />
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.10.2017<br />
 */
class Html5WeekFieldTag extends TextFieldTag {

   const WEEK_FORMAT_PATTERN = 'Y-\WW';

   public function __construct() {
      parent::__construct();
      $this->attributeWhiteList += [
            'autocomplete',
            'list',
            'readonly',
            'min',
            'max',
            'step',
            'required'
      ];
   }

   /**
    * @param DateTime|string $week The date to initialize the control with (either DateTime instance or string).
    */
   public function setWeek($week) {

      if ($week === null) {
         $this->setAttribute('value', null);
         return;
      }

      if (!($week instanceof DateTime)) {
         // Create DateTime instance directly since createFromFormat() is not interpreting "W" format token correctly.
         // See https://lornajane.net/posts/2011/getting-dates-from-week-numbers-in-php for solution idea.
         $week = new DateTime($week);
      }

      $this->setAttribute('value', $week->format(self::WEEK_FORMAT_PATTERN));
   }

   /**
    * Returns the current value of the date control as DateTime instance.
    *
    * @return DateTime The current date representation.
    */
   public function getWeek() {
      $value = $this->getAttribute('value');

      if ($value === null) {
         return null;
      }

      return new DateTime($value);
   }

   public function getValue() {
      return $this->getWeek();
   }

   public function &setValue($value) {
      $this->setWeek($value);

      return $this;
   }

   public function transform() {
      if ($this->isVisible) {
         return '<input type="week" ' . $this->getSanitizedAttributesAsString($this->attributes) . ' />';
      }

      return '';
   }

}
