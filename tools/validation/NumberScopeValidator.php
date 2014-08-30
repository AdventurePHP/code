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
namespace APF\tools\validation;

/**
 * Implements a text length validator with configuration of min/max length and validation mode.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 23.08.2014<br />
 */
class NumberScopeValidator implements Validator {

   /**
    * @var int The minimum number to accept.
    */
   private $minValue;

   /**
    * @var int The maximum number to accept.
    */
   private $maxValue;

   /**
    * @var bool Set to true to make the validator accept only integers, false otherwise.
    */
   private $onlyIntegersAccepted;

   /**
    * @var bool True, if lower end should be included, false otherwise.
    */
   private $includeLowerEnd;

   /**
    * @var bool True, if upper end should be included, false otherwise.
    */
   private $includeUpperEnd;

   /**
    * Let's you initialize the validator with three optional parameters.
    *
    * @param int $minValue The minimum number to accept.
    * @param int $maxValue The maximum number to accept.
    * @param bool $onlyIntegersAccepted True to accept only integers, false otherwise.
    * @param bool $includeLowerEnd True, if lower end should be included, false otherwise.
    * @param bool $includeUpperEnd True, if upper end should be included, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.08.2014<br />
    */
   public function __construct($minValue = null, $maxValue = null, $onlyIntegersAccepted = false, $includeLowerEnd = false, $includeUpperEnd = false) {
      $this->maxValue = $maxValue;
      $this->minValue = $minValue;
      $this->onlyIntegersAccepted = $onlyIntegersAccepted;
      $this->includeLowerEnd = $includeLowerEnd;
      $this->includeUpperEnd = $includeUpperEnd;
   }

   public function isValid($subject) {

      // check if only integers are accepted
      if ($this->onlyIntegersAccepted) {

         // check for integer
         if (!is_int($subject)) {
            return false;
         }

         // convert input from string to int
         $input = (int) $subject;

      } else {

         // check for numeric value (int/float)
         if (!is_numeric($subject)) {
            return false;
         }

         // convert input from string to float
         $input = (float) $subject;

      }

      // check lower limit
      if ($this->minValue !== null) {
         if ($this->includeLowerEnd) {
            if ($input < $this->minValue) {
               return false;
            }
         } else {
            if ($input <= $this->minValue) {
               return false;
            }
         }

      }

      // check upper limit
      if ($this->maxValue !== null) {
         if ($this->includeUpperEnd) {
            if ($input > $this->maxValue) {
               return false;
            }
         } else {
            if ($input >= $this->maxValue) {
               return false;
            }
         }

      }

      return true;
   }

}
