<?php
namespace APF\tools\form\validator;

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
use APF\tools\form\validator\TextFieldValidator;

/**
 * @package APF\tools\form\validator
 * @class NumberScopeValidator
 *
 * Validates a given form control to contain a number within a defined scope.
 * The default min value is 0, the default max value 65535 (maximum value in
 * 16-bit integer). Define the upper/lower limit by using the attributes
 * <em>minvalue</em> and <em>maxvalue</em> in the target control. Use the
 * string 'null' to indicate an infinite upper/lower limit.
 *
 * @author Jan Wiese
 * @version
 * Version 0.1, 02.01.2013<br />
 */
class NumberScopeValidator extends TextFieldValidator {

   private static $MIN_VALUE_ATTRIBUTE_NAME = 'minvalue';

   private static $MAX_VALUE_ATTRIBUTE_NAME = 'maxvalue';

   private static $ONLY_INTEGER_ATTRIBUTE_NAME = 'integer';

   private static $ONLY_INTEGER_ATTRIBUTE_VALUE = 'yes';

   /**
    * @public
    *
    * Re-implements the <em>validate()</em> method for the number scope validator.
    * Supports min and max value definition of the number to validate.
    *
    * @param string $input The number to validate.
    * @return boolean true, in case the control to validate is considered valid, false otherwise.
    *
    * @author Jan Wiese
    * @version
    * Version 0.1, 02.01.2013<br />
    */
   public function validate($input) {

      // check if only integers are accepted
      if ($this->onlyIntegers()) {

         // check for integer
         if (!($input === ((string)(int)$input))) {
            return false;
         }

         // convert input from string to int
         $input = (int)$input;

      } else {

         // check for numeric value (int/float)
         if (!is_numeric($input)) {
            return false;
         }

         // convert input from string to float
         $input = (float)$input;

      }


      // get limiting values
      $minvalue = $this->getMinValue();
      $maxvalue = $this->getMaxValue();

      // check lower limit
      if ($minvalue !== null) {
         if ($input < $minvalue) {
            return false;
         }
      }

      // check upper limit
      if ($maxvalue !== null) {
         if ($input > $maxvalue) {
            return false;
         }
      }

      return true;
   }

   /**
    * @protected
    *
    * Returns the min number value, that must be defined within the target control.
    * Tries to load the min value from the <em>minvalue</em> attribute within the
    * target form control definition. Default value is 0. Infinite lower limit is
    * indicated by string 'null'.
    *
    * @return float The min value.
    *
    * @author Jan Wiese
    * @version
    * Version 0.1, 02.01.2013<br />
    */
   protected function getMinValue() {
      $minValue = $this->control->getAttribute(self::$MIN_VALUE_ATTRIBUTE_NAME);

      if ($minValue === null) {
         $minValue = 0;
      }
      if ($minValue === 'null') {
         return null;
      }

      return (float)$minValue;
   }


   /**
    * @protected
    *
    * Returns the max number value, that must be defined within the target control.
    * Tries to load the max value from the <em>maxvalue</em> attribute within the
    * target form control definition. Default value is 65535. Infinite upper limit is
    * indicated by string 'null'.
    *
    * @return float The max value.
    *
    * @author Jan Wiese
    * @version
    * Version 0.1, 02.01.2013<br />
    */
   protected function getMaxValue() {
      $maxValue = $this->control->getAttribute(self::$MAX_VALUE_ATTRIBUTE_NAME);

      if ($maxValue === null) {
         $maxValue = 65535;
      }

      if ($maxValue === 'null') {
         return null;
      }

      return (float)$maxValue;
   }


   /**
    * @protected
    *
    * Returns if only integers are accepted as target control value.
    * Tries to load from the <em>integers</em> attribute within the target control
    * definition. Default
    *
    * @return bool True if only integers are accepted, false otherwise.
    *
    * @author Jan Wiese
    * @version
    * Version 0.1, 02.01.2013<br />
    */
   protected function onlyIntegers() {
      return $this->control->getAttribute(self::$ONLY_INTEGER_ATTRIBUTE_NAME)
            == self::$ONLY_INTEGER_ATTRIBUTE_VALUE;
   }

}
