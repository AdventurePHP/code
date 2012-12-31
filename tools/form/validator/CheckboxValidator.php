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
 * @class CheckboxValidator
 *
 * Validates checkboxes, which need to be checked by the user.
 *
 * @author Ralf Schubert
 * @version
 * Version 0.1, 13.03.2010<br />
 */
class CheckboxValidator extends TextFieldValidator {

   /**
    * @public
    *
    * Validates the checkbox content.
    *
    * @param string $input The content of the checkbox.
    * @return boolean True, in case the checkbox is valid, false otherwise.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 13.03.2010<br />
    */
   public function validate($input) {
      $name = $this->control->getAttribute('name');
      if (!isset($_REQUEST[$name])) {
         return false;
      }
      return true;

   }

}
