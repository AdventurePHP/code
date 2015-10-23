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
namespace APF\modules\usermanagement\pres\condition;

use APF\modules\genericormapper\data\GenericORMapperException;
use APF\modules\usermanagement\biz\model\UmgtPermission;
use APF\modules\usermanagement\biz\model\UmgtUser;

/**
 * Implements the decision logic, whether a user doesn't have (a) certain permission(s). In case a user's
 * permission is not contained in the option list, the respective content is displayed.
 *
 * @author Christian Merz
 * @version
 * Version 0.1, 16.10.2015<br />
 */
class UmgtNotPermissionCondition extends UserDependentContentConditionBase implements UserDependentContentCondition {

   public function matches($conditionKey, UmgtUser $user = null) {

      if ($user === null) {
         return true;
      }

      foreach ($this->getPermissions($user) as $permission) {
         if (in_array($permission->getName(), $this->getOptions())) {
            return false;
         }
      }

      return true;
   }

   /**
    * Loads all roles and associated permissions of a given user. Filters duplicate entries.
    *
    * @param UmgtUser $user The current user.
    *
    * @return UmgtPermission[] The list of permissions associated to the given user.
    * @throws GenericORMapperException In case of any issues with loading permission.
    *
    * @author Christian Merz
    * @version
    * Version 0.1, 16.10.2015<br />
    */
   private function getPermissions(UmgtUser $user) {
      $RoleList = $user->loadRelatedObjects('Role2User');
      $permissionArray = array();
      foreach ($RoleList as $Role) {
         $PermissionList = $Role->loadRelatedObjects('Role2Permission');
         foreach ($PermissionList as $Permission) {
            if (!array_key_exists($Permission->getObjectId(), $permissionArray))
               $permissionArray[$Permission->getObjectId()] = $Permission;
         }
      }

      return array_merge($permissionArray);
   }

   public function getConditionIdentifier() {
      return 'not-permission';
   }

}
