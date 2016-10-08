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
namespace APF\extensions\apfelsms\biz\pages\decorators\providers;

use APF\core\pagecontroller\APFObject;
use APF\core\service\APFService;
use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\modules\usermanagement\biz\UmgtManager;
use APF\modules\usermanagement\biz\UmgtUserSessionStore;

/**
 * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version v0.1 (08.03.13)
 * @since v0.4
 *
 */
class SMSUmgtVisibilityAccessCtrlProvider extends APFObject implements SMSAccessCtrlProvider {


   /**
    * Caches protection status for each permissionName to gain performance
    *
    * @var bool[] $cache
    */
   protected $cache = [];


   /**
    * If true, access is not protected for no user being logged in.
    *
    * @var bool $anonymousAccess
    */
   protected $anonymousAccess = false;


   /**
    * @param SMSPage $page
    * @param mixed $permissionName In this case, permissionName is the appProxyType name!
    *
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
      $umgtUS = $this->getServiceObject(UmgtUserSessionStore::class, [], APFService::SERVICE_TYPE_SESSION_SINGLETON);

      // load current user
      $user = $umgtUS->getUser($this->getContext());

      // protect against access if no user is logged in and no anonymous access is granted
      if ($user === null) {
         return $this->cache[$permissionName][$pageId] = (!$this->anonymousAccess);
      }

      /** @var $umgtM UmgtManager */
      $umgtM = $this->getDIServiceObject('APF\modules\usermanagement\biz', 'UmgtManager');

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
