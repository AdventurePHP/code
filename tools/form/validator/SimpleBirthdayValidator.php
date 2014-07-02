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
 * Validates a given form control to contain a syntactically correct
 * birthday date. Schema: dd.MM.YY
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.08.2009<br />
 */
class SimpleBirthdayValidator extends TextFieldValidator {

   public function validate($input) {

      $birthday = explode('.', trim($input));

      // catch invalid strings
      if (count($birthday) !== 3) {
         return false;
      }

      // change order and check date
      return checkdate((int) $birthday['1'], (int) $birthday['0'], (int) $birthday['2']);

   }

}
