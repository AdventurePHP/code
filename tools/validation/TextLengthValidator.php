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
namespace APF\tools\validation;

/**
 * Implements a text length validator with configuration of min/max length and validation mode.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 23.08.2014<br />
 */
class TextLengthValidator implements Validator {

   /**
    * @const string Validates the input as is (e.g. w/ leading and trailing blanks).
    */
   const MODE_LAX = 'lax';

   /**
    * @const string Validates the input w/o leading and trailing blanks.
    */
   const MODE_STRICT = 'strict';

   /**
    * @var int Min length of the string.
    */
   private $minLength;

   /**
    * @var int Max length of the string.
    */
   private $maxLength;

   /**
    * @var string The validation mode.
    */
   private $mode = self::MODE_LAX;

   /**
    * Initialized/configures the validator.
    *
    * @param int $minLength Min length of the string.
    * @param int $maxLength Max length of the string.
    * @param string $mode The validation mode (MODE_LAX, MODE_STRICT). Default is MODE_LAX.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.08.2014<br />
    */
   public function __construct(int $minLength, int $maxLength, $mode = self::MODE_LAX) {
      $this->minLength = $minLength;
      $this->maxLength = $maxLength;
      $this->mode = $mode;
   }

   public function isValid($subject) {

      // mode strict needs trim() on $input
      if ($this->mode === self::MODE_STRICT) {
         $subject = trim($subject);
      }

      // the max length being null, the text may contain an infinite number of characters
      if ($this->maxLength === 0) {
         if (strlen($subject) >= $this->minLength) {
            return true;
         }
      } else {
         if (strlen($subject) >= $this->minLength && strlen($subject) <= $this->maxLength) {
            return true;
         }
      }

      return false;
   }

}
