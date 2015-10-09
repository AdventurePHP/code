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
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\core\service\APFService;
use APF\modules\usermanagement\biz\UmgtUserSessionStore;

/**
 * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version v0.1 (19.01.13)
 *          v0.2 (08.03.13) Added hiddenOnLogin option. Renamed from SMSUmgtLoggedInOrHiddenPageDec to SMSUmgtLoggedInPageDec.
 * @since v0.4-beta
 *
 */
class SMSUmgtLoggedInPageDec extends SMSAbstractPageDec {


   /**
    * If true, page is hidden if user is logged in instead of hiding if no user is logged in.
    *
    * @var boolean $hiddenOnLogin
    *
    * @since v0.2
    */
   protected $hiddenOnLogin = false;


   /**
    * @return boolean
    */
   public function isHidden() {

      /** @var $umgtUS UmgtUserSessionStore */
      $umgtUS = &$this->getServiceObject(UmgtUserSessionStore::class, [], APFService::SERVICE_TYPE_SESSION_SINGLETON);

      // load current user
      $user = $umgtUS->getUser($this->getContext());

      // user IS NOT logged in
      if ($user === null) {
         return (!$this->hiddenOnLogin);
      }

      // user IS logged in
      return $this->hiddenOnLogin;

   }

   /**
    * @return boolean
    * @since v0.2
    */
   public function getHiddenOnLogin() {


      return $this->hiddenOnLogin;
   }

   /**
    * @param boolean $hiddenOnLogin
    *
    * @since v0.2
    */
   public function setHiddenOnLogin($hiddenOnLogin) {
      $this->hiddenOnLogin = (strtolower((string) $hiddenOnLogin) == 'true');
   }

}
