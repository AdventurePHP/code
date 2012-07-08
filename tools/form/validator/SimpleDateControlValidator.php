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
import('tools::form::validator', 'TextFieldValidator');

/**
 * @package tools::form::validator
 * @class SimpleDateControlValidator
 *
 * Implements a simple date control validator. It expects the selected date to
 * be greater than today.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.08.2009<br />
 */
class SimpleDateControlValidator extends TextFieldValidator {

   /**
    * @private
    *
    * Validates the date contained in the date control. Checks, whether the
    * date is greater than or equal to the current date.
    *
    * @param string $input The content of the form control (YYYY-MM-DD).
    * @return boolean True, in case the control is valid, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    * Version 0.2, 07.07.2012 (Switched to DateTime API and introduced handling of empty dates)<br />
    */
   public function validate($input) {

      if ($input === null) {
         return false;
      }

      $date = DateTime::createFromFormat('Y-m-d', $input);
      $today = new DateTime();
      return $today < $date;
   }

   /**
    * @public
    *
    * Notifies the form control to be invalid.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function notify() {

      $this->__Control->markAsInvalid();

      // due to the fact, that we introduced a surrounding span
      // validation marking can be done as usual with "normal"
      // text fields
      $this->markControl($this->__Control);

      $this->notifyValidationListeners($this->__Control);
   }

}
