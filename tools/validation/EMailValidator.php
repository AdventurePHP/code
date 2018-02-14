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
 * Implements an e-mail validator that can be configured with custom rules.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 23.08.2014<br />
 */
class EMailValidator implements Validator {

   /**
    * @const string Simple e-mail validation rule.
    */
   const RULE_SIMPLE = '/^([a-zA-Z0-9\.\_\-]+)@([a-zA-Z0-9\.\-]+\.[A-Za-z][A-Za-z]+)$/';

   /**
    * @const string Complex validation rule more strictly following RFC 822.
    */
   const RULE_COMPLEX = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';

   /**
    * @var string The validation rule to use.
    */
   private $rule;

   /**
    * Initializes the validator. You may provide your custom rule with the optional parameter.
    *
    * @param string $rule The e-mail validation rule to use.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.08.2014<br />
    */
   public function __construct($rule = self::RULE_SIMPLE) {
      $this->rule = $rule;
   }

   public function isValid($subject) {
      if (!empty($subject) && preg_match($this->rule, $subject)) {
         return true;
      }

      return false;
   }

}
