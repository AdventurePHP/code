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
namespace APF\modules\usermanagement\pres\condition;

use APF\modules\usermanagement\biz\model\UmgtRole;
use APF\modules\usermanagement\biz\model\UmgtUser;

/**
 * Implements common functionality ti retrieve all user roles.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 19.06.2015<br />
 */
class UmgtRoleConditionBase extends UserDependentContentConditionBase {

   /**
    * Returns all roles the user is either assigned directly (User <-> Role) or
    * indirectly (User <-> Group <-> Role).
    *
    * @param UmgtUser $user
    *
    * @return UmgtRole[]
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.06.2015<br />
    */
   protected function getRoles(UmgtUser $user) {

      $roles = [];

      // map roles int associative array to ensure unique result set
      foreach ($user->loadRelatedObjects('Role2User') as $role) {
         $roles[$role->getObjectId()] = $role;
      }

      // add roles assigned via group to allow easy role/permission assignment
      foreach ($user->loadRelatedObjects('Group2User') as $group) {
         foreach ($group->loadRelatedObjects('Role2Group') as $groupRole) {
            if (!isset($roles[$groupRole->getObjectId()])) {
               $roles[$groupRole->getObjectId()] = $groupRole;
            }
         }
      }

      return array_values($roles);
   }

}
