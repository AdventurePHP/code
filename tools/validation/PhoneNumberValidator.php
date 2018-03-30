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
namespace APF\tools\validation;

/**
 * Implements a phone number validator that can be configured with custom rules.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 23.08.2014<br />
 */
class PhoneNumberValidator implements Validator {

   const STANDARD = '/^[0-9\-\+\(\)\/ ]{6,}$/';

   const INTERNATIONAL = '/^\+(?:[0-9] ?){6,14}[0-9]$/';
   const INTERNATIONAL_EPP = '/^\+[0-9]{1,3}\.[0-9]{4,14}(?:x.+)?$/';

   /**
    * @var string The rule to check the phone number.
    */
   private $rule;

   /**
    * Initializes the validator. You may provide your custom rule with the optional parameter.
    *
    * @param string $rule The phone number validation rule to use.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.08.2014<br />
    */
   public function __construct(string $rule = self::STANDARD) {
      $this->rule = $rule;
   }

   public function isValid($subject) {
      return preg_match($this->rule, trim($subject)) === 1;
   }

}
