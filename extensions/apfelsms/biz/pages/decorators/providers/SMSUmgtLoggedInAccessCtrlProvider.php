<?php
namespace APF\extensions\apfelsms\biz\pages\decorators\providers;

use APF\core\pagecontroller\APFObject;
use APF\core\service\APFService;
use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\modules\usermanagement\biz\UmgtUserSessionStore;

/**
 *
 * @package APF\extensions\apfelsms
 * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version v0.1 (08.03.13)
 * @since v0.4
 */
class SMSUmgtLoggedInAccessCtrlProvider extends APFObject implements SMSAccessCtrlProvider {

   /**
    * @var boolean If true, page is protected if user is logged in instead of protecting if no user is logged in.
    */
   protected $accessProtectedOnLogin = false;


   /**
    * @param SMSPage $page
    * @param $permissionName
    * @return bool
    */
   public function isAccessProtected(SMSPage $page, $permissionName) {

      /** @var $umgtUS UmgtUserSessionStore */
      $umgtUS = & $this->getServiceObject('APF\modules\usermanagement\biz\UmgtUserSessionStore', APFService::SERVICE_TYPE_SESSION_SINGLETON);

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
      $this->accessProtectedOnLogin = (strtolower((string)$accessProtectedOnLogin) == 'true');
   }


   /**
    * @return boolean
    */
   public function getAccessProtectedOnLogin() {
      return $this->accessProtectedOnLogin;
   }

}
