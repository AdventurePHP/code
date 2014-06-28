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

/**
 * Validates a given form control to contain a string with a defined length.
 * The default value is three, to configure the length, please specify the
 * <em>minlength</em> and <em>maxlength</em> attribute within the target form
 * control definition.
 * <p/>
 * Applying attribute <em>mode</em> with value <em>strict</em> the content is
 * trim'd before validation.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.08.2009<br />
 * Version 0.2, 04.07.2010 (Added mode "strict")<br />
 */
class TextLengthValidator extends TextFieldValidator {

   private static $MIN_LENGTH_ATTRIBUTE_NAME = 'minlength';
   private static $MAX_LENGTH_ATTRIBUTE_NAME = 'maxlength';
   private static $MODE_ATTRIBUTE_NAME = 'mode';

   /**
    * Re-implements the <em>validate()</em> method for the text length validator.
    * Supports min and max length definition of the text to validate.
    *
    * @param string $input The text to validate.
    *
    * @return boolean true, in case the control to validate is considered valid, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.02.2010<br />
    */
   public function validate($input) {

      $minlength = $this->getMinLength();
      $maxlength = $this->getMaxLength();
      $mode = $this->getMode();

      // mode strict needs trim() on $input
      if ($mode === 'strict') {
         $input = trim($input);
      }

      // the max length being null, the text may contain an infinite number of characters
      if ($maxlength === 0) {
         if (!empty($input) && strlen($input) >= $minlength) {
            return true;
         }
      } else {
         if (!empty($input) && strlen($input) >= $minlength && strlen($input) <= $maxlength) {
            return true;
         }
      }

      return false;
   }

   /**
    * Returns the min length of the text, that must be contained within
    * the target control. Tries to load the min length from the
    * <em>minlength</em> attribute within the target form control definition.
    *
    * @return int The min length of the text.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   private function getMinLength() {
      $minLength = $this->control->getAttribute(self::$MIN_LENGTH_ATTRIBUTE_NAME);
      if ($minLength === null) {
         $minLength = 3;
      }

      return (int) $minLength;
   }

   /**
    * Returns the max length of the text, that must be contained within
    * the target control. Tries to load the max length from the
    * <em>maxlength</em> attribute within the target form control definition.
    *
    * @return int The max length of the text.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.02.2010<br />
    */
   private function getMaxLength() {
      // max length being null is ok, because we consider it to indicate infinite length!
      $maxLength = $this->control->getAttribute(self::$MAX_LENGTH_ATTRIBUTE_NAME);

      return (int) $maxLength;
   }

   /**
    * Returns the mode of validating, which should be used on the target control.
    * Tries to load the mode from the <em>mode</em> attribute within the target form control definition.
    *
    * @return string The mode name.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 04.07.2010<br />
    */
   private function getMode() {
      return $this->control->getAttribute(self::$MODE_ATTRIBUTE_NAME);
   }

}
