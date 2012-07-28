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
 * @class EMailValidator
 *
 * Validates a given form control to contain a syntactically correct email.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.08.2009<br />
 */
class EMailValidator extends TextFieldValidator {

   public function validate($input) {
      if (!empty($input) && preg_match('/^([a-zA-Z0-9\.\_\-]+)@([a-zA-Z0-9\.\-]+\.[A-Za-z][A-Za-z]+)$/', $input)) {
         return true;
      }
      return false;
   }

}
