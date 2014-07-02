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
namespace APF\tools\form\validator;

/**
 * @package APF\tools\form\validator
 * @class SimpleSelectControlValidator
 *
 * Implements a validator for select fields.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.08.2009<br />
 */
class SimpleSelectControlValidator extends SelectFieldValidator {

   /**
    * @public
    *
    * Validates the select field content.
    *
    * @param string $input The content of the select control.
    * @return boolean True, in case the control is valid, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function validate($input) {

      // here, we have to validate the request values instead of the
      // input we are given from the form taglib to get valid results.
      // this has to be done because of dynamic filling of select fields!
      $name = $this->control->getAttribute('name');
      if (!isset($_REQUEST[$name]) || empty($_REQUEST[$name])) {
         return false;
      }
      return true;

   }

}
