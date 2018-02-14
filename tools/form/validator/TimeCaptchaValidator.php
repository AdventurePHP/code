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

use APF\tools\form\taglib\TimeCaptchaTag;

/**
 * Implements, in combination with TimeCaptchaTag, a non-visual captcha
 * which tries to identify bots by the time they need to fill in the data in a
 * form.
 * The time (in seconds) before the tag becomes valid can be specified in the
 * target &lt;form:timecaptcha/&gt; element with the attribute <em>seconds</em>.
 * Default is 2 seconds.
 *
 * @author Ralf Schubert
 * @version
 * Version 0.1, 14.03.2010<br />
 */
class TimeCaptchaValidator extends TextFieldValidator {

   /**
    * The default time in seconds before form gets valid.
    *
    * @var int $defaultSeconds
    */
   protected $defaultSeconds = 2;

   /**
    * Re-implements the <em>validate()</em> method for the TimeCaptchaValidator
    *
    * @param string $input Empty string, because we have no html-definition
    *
    * @return boolean true, in case the control to validate is considered valid, false otherwise.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 14.03.2010<br />
    */
   public function validate($input) {
      $seconds = $this->control->getAttribute('seconds');
      if ($seconds === null) {
         $seconds = $this->defaultSeconds;
      }

      $session = $this->getRequest()->getSession(TimeCaptchaTag::SESSION_NAMESPACE);
      $form = $this->control->getForm();
      $storedTime = intval($session->load('form_' . $form->getAttribute('name')));
      $session->delete('form_' . $form->getAttribute('name'));
      unset($session);

      // If there is no stored time in session control is not valid.
      if ($storedTime === null) {
         return false;
      }
      // Form was sent too fast.
      if (($seconds + $storedTime) >= time()) {
         return false;
      }

      return true;

   }

}
