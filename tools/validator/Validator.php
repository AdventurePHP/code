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

/**
 * @package tools::validator
 * @class Validator
 *
 * Provides (static) methods for data validation purposes.
 *
 * @deprecated Please use the APF form validation instead.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 00.00.2006<br />
 * Version 0.2, 12.07.2007<br />
 * Version 0.3, 27.03.2007 (Removed deprecated methods)<br />
 * Version 0.4, 17.09.2009 (Renamed from "myValidator" to "Validator")<br />
 */
class Validator {

   private function __construct() {
   }

   /**
    * @public
    * @static
    *
    * Validates, whether a text is more than three chars long.
    *
    * @param string $input Data to check against the validation rule.
    * @return boolean true|false
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 12.01.2007<br />
    * Version 0.2, 16.06.2007 (Strings with character count < 3 signs are considered not valid)<br />
    */
   static function validateText($input) {
      return (!empty($input) && strlen($input) >= 3);
   }

   /**
    * @public
    * @static
    *
    * Validates an email address.
    *
    * @param string $input Data to check against the validation rule.
    * @return boolean true|false
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 12.01.2007<br />
    */
   static function validateEMail($input) {
      return preg_match('/^([a-zA-Z0-9\.\_\-]+)@([a-zA-Z0-9\.\-]+\.[A-Za-z][A-Za-z]+)$/', $input);
   }

   /**
    *  @public
    *  @static
    *
    *  Validates a phone number.
    *
    *  @param string $input Data to check against the validation rule.
    *  @return boolean true|false
    *
    *  @author Christian Sch�fer
    *  @version
    *  Version 0.1, 12.01.2007<br />
    */
   static function validateTelefon($input) {
      return preg_match('/^[0-9\-\+\(\)\/ ]{6,}+$/', trim($input));
   }

   /**
    * @public
    * @static
    *
    * Validates a fax number.
    *
    * @param string $input Data to check against the validation rule.
    * @return boolean true|false
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 12.01.2007<br />
    */
   static function validateFax($input) {
      return Validator::validateTelefon($input);
   }

   /**
    * @public
    * @static
    *
    * Validates, if given data is a number.
    *
    * @param string $input Data to check against the validation rule.
    * @return boolean true|false
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2007<br />
    * Version 0.2, 15.08.2008 (Changed due to feature change request)<br />
    */
   static function validateNumber($input) {
      return is_numeric(trim($input));
   }

   /**
    * @public
    * @static
    *
    * Validates Validiert einen Ordner-Namen.<br />
    *
    * @param string $input Data to check against the validation rule.
    * @return boolean true|false
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 12.01.2007<br />
    */
   static function validateFolder($input) {
      return preg_match('/^[a-zA-Z0-9\-\_]+$/', trim($input));
   }

   /**
    * @public
    * @static
    *
    * Validates a given string with the regular expression offered.
    *
    * @param string $input string to test
    * @param string $regExp regular expression
    * @return boolean true|false
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, ??.??.????<br />
    * Version 0.2, 03.02.2006 (Removed typo)<br />
    * Version 0.3, 12.01.2007 (Only boolean values are returned now)<br />
    * Version 0.4, 21.08.2008 (Removed trim()s due to validation errors with blanks)<br />
    */
   static function validateRegExp($input, $regExp) {
      return preg_match($regExp, $input);
   }

   /**
    * @public
    * @static
    *
    * Validates an birthday date.
    *
    * @param string $input Birthday date. Expected format is: dd.mm.yyyy
    * @return boolean true|false
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 10.08.2009<br />
    */
   static function validateBirthday($input) {

      $birthday = explode('.', trim($input));

      // catch invalid strings
      if (count($birthday) !== 3) {
         return false;
      }

      // change order and check date
      return checkdate((int) $birthday['1'], (int) $birthday['0'], (int) $birthday['2']);
   }

}
?>