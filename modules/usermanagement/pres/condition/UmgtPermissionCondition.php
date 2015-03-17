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
 * Implements the decision logic whether oor not a user has a particular permission or not.
 *
 * @author Christian Merz
 * @version
 * Version 0.1, 12.03.2015<br />
 */
class UmgtPermissionCondition extends UserDependentContentConditionBase implements UserDependentContentCondition {

   public function matches($conditionKey, UmgtUser $user = null) {

      if ($user === null) {
         return false;
      }

      foreach ($this->getPermissions($user) as $permission) {
         if (in_array($permission->getName(), $this->getOptions())) {
            return true;
         }
      }

      return false;
   }

   public function getConditionIdentifier() {
      return 'permission';
   }

   /**
    * Loads all user permissions and filters duplicate entries.
    *
    * @param UmgtUser $user The current user.
    *
    * @return UmgtPermission[] The list of permissions for the given user.
    * @throws GenericORMapperException In case something went wrong with loading data from the GORM store.
    *
    * @author Christian Merz
    * @version
    * Version 0.1, 13.03.15<br />
    */
   private function getPermissions(UmgtUser $user) {

      $roles = $user->loadRelatedObjects('Role2User');
      $result = array();

      foreach ($roles as $role) {
         $permissions = $role->loadRelatedObjects('Role2Permission');
         foreach ($permissions as $permission) {
            if (!isset($result[$permission->getObjectId()])) {
               $result[$permission->getObjectId()] = $permission;
            }
         }
      }

      return array_merge($result);
   }

}
