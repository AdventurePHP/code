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
namespace APF\modules\usermanagement\pres\documentcontroller\login;

use APF\core\configuration\ConfigurationException;
use APF\core\http\Cookie;
use APF\core\logging\LogEntry;
use APF\core\logging\Logger;
use APF\core\pagecontroller\BaseDocumentController;
use APF\core\service\APFService;
use APF\core\singleton\Singleton;
use APF\modules\usermanagement\biz\login\UmgtAutoLoginAction;
use APF\modules\usermanagement\biz\login\UmgtRedirectUrlProvider;
use APF\modules\usermanagement\biz\model\UmgtAuthToken;
use APF\modules\usermanagement\biz\model\UmgtUser;
use APF\modules\usermanagement\biz\UmgtManager;
use APF\modules\usermanagement\biz\UmgtUserSessionStore;
use APF\tools\form\validator\AbstractFormValidator;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;
use Exception;

/**
 * Manages the login form and authenticates the user concerning the
 * authentication method configured.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2011
 */
class LoginController extends BaseDocumentController {

   private static $EMAIL_AND_PASSWORD_LOGIN = 'email';
   private static $USERNAME_AND_PASSWORD_LOGIN = 'username';

   public function transformContent() {

      /* @var $sessionStore UmgtUserSessionStore */
      $sessionStore = &$this->getServiceObject(UmgtUserSessionStore::class, [],
            APFService::SERVICE_TYPE_SESSION_SINGLETON);

      $appIdent = $this->getContext();
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

                  // create auto-login cookie
                  $rememberMe = $form->getFormElementByName('remember-me');
                  if ($rememberMe->isChecked()) {
                     $this->createAutoLogin($user);
                  }

                  // redirect to target page
                  $urlProvider = &$this->getDIServiceObject('APF\modules\usermanagement\biz', 'LoginRedirectUrlProvider');
                  /* @var $urlProvider UmgtRedirectUrlProvider */
                  $this->getResponse()->forward(LinkGenerator::generateUrl(Url::fromString($urlProvider->getRedirectUrl())));
               }
            } catch (Exception $e) {
               $this->getTemplate('system-error')->transformOnPlace();

               $l = &Singleton::getInstance(Logger::class);
               /* @var $l Logger */
               $l->logEntry('login', 'Login is not possible due to ' . $e, LogEntry::SEVERITY_ERROR);
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
    * Allows you to authenticate with email and password as well as with username and password.
    * The decision is done by an optional configuration that defines the login type.
    *
    * @param string $username The given user name.
    * @param string $password The given password.
    *
    * @return UmgtUser The logged-in user or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2011<br />
    */
   private function loadUser($username, $password) {
      /* @var $umgt UmgtManager */
      $umgt = &$this->getDIServiceObject('APF\modules\usermanagement\biz', 'UmgtManager');

      try {
         $config = $this->getConfiguration('APF\modules\usermanagement\pres', 'login.ini');
         $loginType = $config->getSection(UmgtManager::CONFIG_SECTION_NAME)
               ->getValue('login.type', self::$USERNAME_AND_PASSWORD_LOGIN);
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

   public function createAutoLogin($user) {
      /* @var $umgt UmgtManager */
      $umgt = &$this->getDIServiceObject('APF\modules\usermanagement\biz', 'UmgtManager');

      $cookieLifeTime = $umgt->getAutoLoginCookieLifeTime();

      $cookie = new Cookie(UmgtAutoLoginAction::AUTO_LOGIN_COOKIE_NAME, time() + $cookieLifeTime);

      $token = md5(rand(100000, 999999));

      $this->getResponse()->setCookie($cookie->setValue($token));

      $authToken = new UmgtAuthToken();
      $authToken->setToken($token);
      $umgt->saveAuthToken($user, $authToken);
   }

}
