<?php
namespace APF\modules\usermanagement\biz\login;

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
use APF\tools\cookie\CookieManager;

/**
 * @package modules::usermanagement::biz::login
 * @class UmgtAutoLoginAction
 */
class UmgtAutoLoginAction extends AbstractFrontcontrollerAction {

   /**
    * @const Defines the cookie namespace of the auto login cookie.
    */
   const AUTO_LOGIN_COOKIE_NAMESPACE = 'umgt::biz';

   /**
    * @const Defines the cookie name for the user's permanent auth token.
    */
   const AUTO_LOGIN_COOKIE_NAME = 'AuthToken';

   public function run() {

      /* @var $sessionStore UmgtUserSessionStore */
      $sessionStore = &$this->getServiceObject('modules::usermanagement::biz', 'UmgtUserSessionStore', APFService::SERVICE_TYPE_SESSION_SINGLETON);

      $appIdent = $this->getContext();
      $user = $sessionStore->getUser($appIdent);

      // try to log-in user from cookie
      if ($user === null) {

         $cM = new CookieManager(self::AUTO_LOGIN_COOKIE_NAMESPACE);
         $authToken = $cM->readCookie(self::AUTO_LOGIN_COOKIE_NAME);

         if ($authToken !== null) {

            /* @var $umgt UmgtManager */
            $umgt = &$this->getDIServiceObject('modules::usermanagement::biz', 'UmgtManager');
            $savedToken = $umgt->loadAuthTokenByTokenString($authToken);

            if ($savedToken !== null) {

               $user = $umgt->loadUserByAuthToken($savedToken);

               if ($user !== null) {
                  $sessionStore->setUser($appIdent, $user);
                  $cookieLifeTime = $umgt->getAutoLoginCookieLifeTime();
                  $cM->updateCookie(self::AUTO_LOGIN_COOKIE_NAME, $savedToken->getToken(), time() + $cookieLifeTime);
               }
            }
         }
      }
   }

}
