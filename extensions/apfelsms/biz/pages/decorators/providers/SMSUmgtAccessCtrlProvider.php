<?php
namespace APF\extensions\apfelsms\biz\pages\decorators\providers;

use APF\core\pagecontroller\APFObject;
use APF\core\service\APFService;
use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\modules\usermanagement\biz\UmgtManager;
use APF\modules\usermanagement\biz\UmgtUserSessionStore;

/**
 *
 * @package APF\APFelSMS
 * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version v0.1 (19.01.13)
 *          v0.2 (08.03.13) Added support for anonymous access. Added check for non-empty array around foreach.
 * @since v0.4-beta
 *
 */
class SMSUmgtAccessCtrlProvider extends APFObject implements SMSAccessCtrlProvider {

   /**
    * @var bool[] Caches protection status for each permissionName to gain performance
    */
   protected $cache = array();

   /**
    * @var bool If true, access is not protected for no user being logged in.
    * @since v0.2
    */
   protected $anonymousAccess = false;


   /**
    * @param SMSPage $page
    * @param mixed $permissionName
    * @return bool
    */
   public function isAccessProtected(SMSPage $page, $permissionName) {

      // try to return chached protection status
      if (isset($this->cache[$permissionName])) {
         return $this->cache[$permissionName];
      }

      /** @var $umgtUS UmgtUserSessionStore */
      $umgtUS = & $this->getServiceObject('APF\modules\usermanagement\biz\UmgtUserSessionStore', APFService::SERVICE_TYPE_SESSION_SINGLETON);

      // load current user
      $user = $umgtUS->getUser($this->getContext());

      // protect against access if no user is logged in and no anonymous acces is granted
      if ($user === null) {
         return $this->cache[$permissionName] = (!$this->anonymousAccess);
      }

      /** @var $umgtM UmgtManager */
      $umgtM = & $this->getDIServiceObject('APF\modules\usermanagement\biz', 'UmgtManager');

      $permissions = $umgtM->loadUserPermissions($user);

      // search permission
      if (count($permissions) > 0) {
         foreach ($permissions AS $permission) {

            if ($permission->getName() == $permissionName) {
               return $this->cache[$permissionName] = false; // permission found, grant access
            }

         }
      }


      // no permission found, protected against access
      return $this->cache[$permissionName] = true;

   }


   /**
    * @param boolean $anonymousAccess
    */
   public function setAnonymousAccess($anonymousAccess) {
      $this->anonymousAccess = $anonymousAccess;
   }


   /**
    * @return boolean
    */
   public function getAnonymousAccess() {
      return $this->anonymousAccess;
   }

}
