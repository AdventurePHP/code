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
namespace APF\modules\recaptcha\pres\validator;

use APF\modules\recaptcha\pres\taglib\ReCaptchaTag;
use APF\tools\form\validator\TextFieldValidator;
use ReCaptcha\ReCaptcha;

/**
 * Validates a reCaptcha field.
 * <p/>
 * Using APF's ReCaptcha wrapper requires download and inclusion of Google's
 * <em>recaptcha</em> available under https://github.com/google/recaptcha.
 * <p/>
 * Please include <em>recaptcha</em> library into your project and add to
 * auto loading.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 22.09.2012<br />
 * Version 0.2, 06.11.2013 (Removed inclusion of external recaptachalib library due to license issues described in ID#80)<br />
 * Version 0.3, 30.10.2015 (ID#94: updated to ReCaptcha 2 lib)<br />
 */
class ReCaptchaValidator extends TextFieldValidator {

   public function validate($input) {

      /* @var $control ReCaptchaTag */
      $control = $this->control;

      $request = $this->getRequest();

      $reCaptcha = new ReCaptcha($control->getPrivateKey());
      $response = $reCaptcha->verify(
            $request->getParameter(ReCaptchaTag::RE_CAPTCHA_RESPONSE_IDENTIFIER),
            $request->getRemoteAddress()
      );

      if ($response->isSuccess()) {
         return true;
      } else {
         // inject error message key to be able to display a user hint
         $control->setErrorMessageKeys($response->getErrorCodes());

         return false;
      }
   }

   /**
    * Notifies the form control to be invalid for the reCaptcha field.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.00.2012<br />
    */
   public function notify() {
      // used to invalidate the form in common APF style
      $this->control->markAsInvalid();

      // necessary to display the registered listeners
      $this->notifyValidationListeners($this->control);
   }

}
