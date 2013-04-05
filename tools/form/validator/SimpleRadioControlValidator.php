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
 * @class SimpleRadioControlValidator
 *
 * Implements a validator for radio buttons.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.08.2009<br />
 */
class SimpleRadioControlValidator extends TextFieldValidator {

   /**
    * @public
    *
    * Validates the radio button content.
    *
    * @param string $input The content of the radio control.
    * @return boolean True, in case the radio is valid, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function validate($input) {

      // here, we have to validate the request values instead of the
      // input we are given from the form taglib to get valid results.
      // this has to be done because of dynamic filling of radio button!
      $name = $this->control->getAttribute('name');
      if (!isset($_REQUEST[$name]) || empty($_REQUEST[$name])) {
         return false;
      }
      return true;

   }

}
