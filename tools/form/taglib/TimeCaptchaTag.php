<?php
namespace APF\tools\form\taglib;

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
use APF\core\session\SessionManager;

/**
 * @package APF\tools\form\taglib
 * @class TimeCaptchaTag
 *
 * Implements, in combination with TimeCaptchaValidator, a non-visual captcha
 * which tries to identify bots by the time they need to fill in the data in a
 * form.
 *
 * @author Ralf Schubert
 * @version
 * Version 0.1, 14.03.2010<br />
 */
class TimeCaptchaTag extends AbstractFormControl {

   /**
    * @public
    *
    * Overwrites the parent's method, because there is nothing to do here.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 14.03.2010<br />
    */
   public function onParseTime() {
   }

   /**
    * @public
    *
    * Saves an unix_timestamp in session, which can be used from TimeCaptchaValidator.
    *
    * @return string Empty string, because we need no html.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 14.03.2010<br />
    */
   public function transform() {

      $session = new SessionManager('tools::form::taglib::TimeCaptchaTag');

      // Delete every stored time in session, which is older than 40 minutes, in order to clean the session.
      $sessionStore = $session->getEntryDataKeys();
      foreach ($sessionStore as $key) {
         if ($session->loadSessionData($key) <= (time() - 2400)) {
            $session->deleteSessionData($key);
         }
      }

      // save the new time in session.
      $session->saveSessionData('form_' . $this->getParentObject()->getAttribute('name'), time());

      return '';
   }

}
