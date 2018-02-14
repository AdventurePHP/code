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
namespace APF\tools\form\validator;

use APF\tools\validation\EMailValidator as EMailValidatorImpl;

/**
 * Validates a given form control to contain a syntactically correct email.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.08.2009<br />
 * Version 0.2, 23.08.2014 (ID#138: extracted validation to allow unit testing and easy controller validation)<br />
 */
class EMailValidator extends TextFieldValidator {

   public function validate($input) {
      $validator = new EMailValidatorImpl();

      return $validator->isValid($input);
   }

}
