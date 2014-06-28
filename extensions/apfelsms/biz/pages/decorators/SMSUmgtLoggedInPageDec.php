<?php
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
      $umgtUS = & $this->getServiceObject('APF\modules\usermanagement\biz\UmgtUserSessionStore', APFService::SERVICE_TYPE_SESSION_SINGLETON);

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
    * @param boolean $hiddenOnLogin
    *
    * @since v0.2
    */
   public function setHiddenOnLogin($hiddenOnLogin) {


      $this->hiddenOnLogin = (strtolower((string) $hiddenOnLogin) == 'true');
   }


   /**
    * @return boolean
    * @since v0.2
    */
   public function getHiddenOnLogin() {


      return $this->hiddenOnLogin;
   }

}
