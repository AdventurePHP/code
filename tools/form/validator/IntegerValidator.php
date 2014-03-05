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
 * @class IntegerValidator
 *
 * Validates a given form control to contain an integer.
 *
 * @author Ralf Schubert
 * @version
 * Version 0.1, 01.11.2010<br />
 */
class IntegerValidator extends TextFieldValidator {

   public function validate($input) {
      $input = trim($input);
      if ($input === (string)(int)$input) {
         return true;
      }
      return false;
   }

}
