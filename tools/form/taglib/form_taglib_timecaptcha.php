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
import('core::session', 'SessionManager');

/**
 * @package tools::form::taglib
 * @class form_taglib_timecaptcha
 *
 * Implements, in combination with TimeCaptchaValidator, a non-visual captcha
 * which tries to identify bots by the time they need to fill in the data in a
 * form.
 *
 * @author Ralf Schubert
 * @version
 * Version 0.1, 14.03.2010<br />
 */
class form_taglib_timecaptcha extends form_control {

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
    * Overwrites the parent's method, because there is nothing to do here.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 14.03.2010<br />
    */
   public function onAfterAppend() {
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

      $sessMgr = new SessionManager('tools::form::taglib::form_taglib_timecaptcha');

      // Delete every stored time in session, which is older than 40 minutes, in order to clean the session.
      $sessionStore = $sessMgr->getEntryDataKeys();
      foreach ($sessionStore as $sessKey) {
         if ($sessMgr->loadSessionData($sessKey) <= (time() - 2400)) {
            $sessMgr->deleteSessionData($sessKey);
         }
      }

      // save the new time in session.
      $sessMgr->saveSessionData('form_' . $this->__ParentObject->getAttribute('name'), time());

      return '';
   }

}
