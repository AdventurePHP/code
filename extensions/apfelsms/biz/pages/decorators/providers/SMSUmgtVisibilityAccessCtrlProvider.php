<?php
namespace APF\extensions\apfelsms\biz\pages\decorators\providers;

/**
 *
 * @package APFelSMS
 * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version v0.1 (08.03.13)
 * @since v0.4
 *
 */
class SMSUmgtVisibilityAccessCtrlProvider extends APFObject implements SMSAccessCtrlProvider {

   /**
    * @var bool[] Caches protection status for each permissionName to gain performance
    */
   protected $cache = array();

   /**
    * @var bool If true, access is not protected for no user being logged in.
    */
   protected $anonymousAccess = false;


   /**
    * @param SMSPage $page
    * @param mixed $permissionName In this case, permissionName is the appProxyType name!
    * @return bool
    */
   public function isAccessProtected(SMSPage $page, $permissionName) {

      /*
      * !!! $permissionName is used for visibilityDefinitionType !!!
      */

      $pageId = $page->getId();

      // try to return chached protection status
      if (isset($this->cache[$permissionName][$pageId])) {
         return $this->cache[$permissionName][$pageId];
      }

      /** @var $umgtUS UmgtUserSessionStore */
      $umgtUS = &$this->getServiceObject('modules::usermanagement::biz', 'UmgtUserSessionStore', APFService::SERVICE_TYPE_SESSION_SINGLETON);

      // load current user
      $user = $umgtUS->getUser($this->getContext());

      // protect against access if no user is logged in and no anonymous acces is granted
      if ($user === null) {
         return $this->cache[$permissionName][$pageId] = (!$this->anonymousAccess);
      }

      /** @var $umgtM UmgtManager */
      $umgtM = &$this->getDIServiceObject('APF\modules\usermanagement\biz', 'UmgtManager');

      // load visibilities from users and groups
      $groups = $umgtM->loadGroupsWithUser($user);
      $visibilityType = $umgtM->loadVisibilityDefinitionTypeByName($permissionName);
      if ($visibilityType === null) {
         return $this->cache[$permissionName][$pageId] = true; // visibility type is not existent
      }
      
      $visibilities = $umgtM->loadVisibilityDefinitionsByUser($user, $visibilityType);
      if (count($groups) > 0) {
         foreach ($groups AS $group) {
            $visibilities = array_merge(
               $visibilities,
               $umgtM->loadVisibilityDefinitionsByGroup($group, $visibilityType)
            );
         }
      }


      // search visibility definitions
      if (count($visibilities) > 0) {
         foreach ($visibilities AS $visibility) {

            if ($visibility->getAppObjectId() == $pageId &&
               ((bool) $visibility->getReadPermission())
            ) {
               return $this->cache[$permissionName][$pageId] = false; // visibility definition with read permission found, grant access
            }
         }
      }

      // no permission found, protected against access
      return $this->cache[$permissionName][$pageId] = true;


   }


   /**
    * @param boolean $anonymousAccess
    */
   public function setAnonymousAccess($anonymousAccess) {
      $this->anonymousAccess = (strtolower((string) $anonymousAccess) == 'true');
   }


   /**
    * @return boolean
    */
   public function getAnonymousAccess() {
      return $this->anonymousAccess;
   }

}
