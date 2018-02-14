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
namespace APF\extensions\apfelsms\biz\pages\decorators\providers;

use APF\core\pagecontroller\APFObject;
use APF\core\service\APFService;
use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\modules\usermanagement\biz\UmgtUserSessionStore;

/**
 * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version v0.1 (08.03.13)
 * @since v0.4
 */
class SMSUmgtLoggedInAccessCtrlProvider extends APFObject implements SMSAccessCtrlProvider {


   /**
    * If true, page is protected if user is logged in instead of protecting if no user is logged in.
    *
    * @var boolean $accessProtectedOnLogin
    */
   protected $accessProtectedOnLogin = false;


   /**
    * @param SMSPage $page
    * @param $permissionName
    *
    * @return bool
    */
   public function isAccessProtected(SMSPage $page, $permissionName) {

      /** @var $umgtUS UmgtUserSessionStore */
      $umgtUS = $this->getServiceObject(UmgtUserSessionStore::class, [], APFService::SERVICE_TYPE_SESSION_SINGLETON);

      // load current user
      $user = $umgtUS->getUser($this->getContext());


      // user IS NOT logged in
      if ($user === null) {
         return (!$this->accessProtectedOnLogin);
      }

      // user IS logged in
      return $this->accessProtectedOnLogin;
   }


   /**
    * @param boolean $accessProtectedOnLogin
    */
   public function setAccessProtectedOnLogin($accessProtectedOnLogin) {


      $this->accessProtectedOnLogin = (strtolower((string) $accessProtectedOnLogin) == 'true');
   }


   /**
    * @return boolean
    */
   public function getAccessProtectedOnLogin() {


      return $this->accessProtectedOnLogin;
   }

}
