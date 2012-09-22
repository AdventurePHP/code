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
require_once(__DIR__ . '/../../external/google/recaptchalib.php');

/**
 * @package modules::recaptcha::pres::validator
 * @class ReCaptchaValidator
 *
 * Validates a reCaptcha field.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 22.09.2012<br />
 */
class ReCaptchaValidator extends TextFieldValidator {

   /**
    * @public
    *
    * Method, that is called to validate the element.
    *
    * @param string $input The input to validate.
    * @return boolean True, in case the control is valid, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.08.2009<br />
    */
   public function validate($input) {

      /* @var $control ReCaptchaTag */
      $control = $this->__Control;
      $control->getPrivateKey();

      /* @var $resp ReCaptchaResponse */
      $resp = recaptcha_check_answer(
         $control->getPrivateKey(),
         $_SERVER['REMOTE_ADDR'],
         $_REQUEST[ReCaptchaTag::RE_CAPTCHA_CHALLENGE_FIELD_IDENTIFIER],
         $_REQUEST[ReCaptchaTag::RE_CAPTCHA_CHALLENGE_ANSWER_IDENTIFIER]
      );

      if ($resp->is_valid) {
         return true;
      } else {
         // inject error message key to be able to display a user hint
         $control->setErrorMessageKey($resp->error);
         return false;
      }
   }

   /**
    * @public
    *
    * Notifies the form control to be invalid for the reCaptcha field.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.00.2012<br />
    */
   public function notify() {
      // used to invalidate the form in common APF style
      $this->__Control->markAsInvalid();

      // necessary to display the registered listeners
      $this->notifyValidationListeners($this->__Control);
   }

}
