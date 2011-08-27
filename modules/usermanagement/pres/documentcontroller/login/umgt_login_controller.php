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
import('core::logging', 'Logger');
import('tools::link', 'LinkGenerator');
import('tools::http', 'HeaderManager');

/**
 * @package modules::usermanagement::pres::documentcontroller::login
 * @class umgt_login_controller
 *
 * Manages the login form and authenticates the user concerning the
 * authentication method configured.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2011
 */
class umgt_login_controller extends base_controller {

   private static $EMAIL_AND_PASSWORD_LOGIN = 'email';
   private static $USERNAME_AND_PASSWORD_LOGIN = 'username';

   public function transformContent() {

      $sessionStore = &$this->getServiceObject('modules::usermanagement::biz', 'UmgtUserSessionStore', APFService::SERVICE_TYPE_SESSION_SINGLETON);
      /* @var $sessionStore UmgtUserSessionStore */
      $appIdent = $this->getApplicationIdentifier();
      $user = $sessionStore->getUser($appIdent);

      if ($user === null) {
         $form = &$this->getForm('login');

         // generate url ourselves to not include the logout action instruction
         $form->setAction(LinkGenerator::generateUrl(Url::fromCurrent()));

         if ($form->isSent() && $form->isValid()) {

            $username = $form->getFormElementByName('username')->getAttribute('value');
            $password = $form->getFormElementByName('password')->getAttribute('value');

            try {
               $user = $this->loadUser($username, $password);

               if ($user === null) {
                  $form->getFormElementByName('username')->markAsInvalid();
                  $form->getFormElementByName('username')->appendCssClass(AbstractFormValidator::$DEFAULT_MARKER_CLASS);
                  $form->getFormElementByName('password')->markAsInvalid();
                  $form->getFormElementByName('password')->appendCssClass(AbstractFormValidator::$DEFAULT_MARKER_CLASS);

                  $form->setPlaceHolder('login-error', $this->getTemplate('login-error')->transformTemplate());

                  $form->transformOnPlace();
               } else {
                  // store user
                  $sessionStore->setUser($appIdent, $user);

                  // redirect to target page
                  $urlProvider = &$this->getDIServiceObject('modules::usermanagement::biz', 'LoginRedirectUrlProvider');
                  /* @var $urlProvider UmgtRedirectUrlProvider */
                  HeaderManager::forward(LinkGenerator::generateUrl(Url::fromString($urlProvider->getRedirectUrl())));
                  exit(0);
               }
            } catch (Exception $e) {
               $this->getTemplate('system-error')->transformOnPlace();
               $l = &Singleton::getInstance('Logger');
               /* @var $l Logger */
               $l->logEntry('login', 'Login is not possible due to ' . $e, 'ERROR');
            }
         } elseif ($form->isSent() && !$form->isValid()) {
            $form->setPlaceHolder('login-error', $this->getTemplate('login-error')->transformTemplate());
            $form->transformOnPlace();
         } else {
            $form->transformOnPlace();
         }
      } else {
         $this->getTemplate('login-ok')->transformOnPlace();
      }
   }

   /**
    * @private
    *
    * Allows you to athenticate with email and password as well as with username and password.
    * The decision is done by an optional configuration that defines the login type.
    *
    * @param string $username The given user name.
    * @param string $password The given password.
    * @return GenericDomainObject The logged-in user or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2011<br />
    */
   private function loadUser($username, $password) {
      $umgt = &$this->getDIServiceObject('modules::usermanagement::biz', 'UmgtManager');
      /* @var $umgt UmgtManager */

      try {
         $config = $this->getConfiguration('modules::usermanagement::pres', 'login');
         $section = $config->getSection(UmgtManager::CONFIG_SECTION_NAME);
         $loginType = $section == null ? self::$USERNAME_AND_PASSWORD_LOGIN : $section->getValue('login.type');
      } catch (ConfigurationException $e) {
         $loginType = self::$USERNAME_AND_PASSWORD_LOGIN;
      }

      switch ($loginType) {
         case self::$EMAIL_AND_PASSWORD_LOGIN:
            return $umgt->loadUserByEMailAndPassword($username, $password);
            break;
         default:
            return $umgt->loadUserByUsernameAndPassword($username, $password);
            break;
      }
   }

   private function getApplicationIdentifier() {
      return $this->getAttribute('app-ident', $this->getContext());
   }

}

?>