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
 * Implements a simple multi select field validator, that expects the value
 * of the select field not to be empty.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.09.2009<br />
 */
class MultiSelectFieldValidator extends SelectFieldValidator {

   /**
    * Implements the validation method for multi select fields.
    *
    * @param string $input The input of the multi select field.
    *
    * @return boolean True, in case the control is valid, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.09.2009<br />
    */
   public function validate($input) {

      $controlName = $this->control->getAttribute('name');
      if (self::getRequest()->getParameter($controlName) === null) {
         return false;
      }

      return true;

   }

}
