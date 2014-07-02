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

use APF\modules\usermanagement\biz\model\UmgtRole;
use APF\modules\usermanagement\biz\model\UmgtUser;

/**
 * @package APF\modules\usermanagement\pres\condition
 * @class UmgtGroupCondition
 *
 * Implements the decision logic, whether a user's role is part of given options array.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.06.2011
 */
class UmgtRoleCondition extends UserDependentContentConditionBase implements UserDependentContentCondition {

   public function matches($conditionKey, UmgtUser $user = null) {

      if ($user === null) {
         return false;
      }

      foreach ($this->getRoles($user) as $role) {
         if (in_array($role->getDisplayName(), $this->getOptions())) {
            return true;
         }
      }
      return false;
   }

   public function getConditionIdentifier() {
      return 'role';
   }

   /**
    * @param UmgtUser $user
    * @return UmgtRole[]
    */
   private function getRoles(UmgtUser $user) {
      return $user->loadRelatedObjects('Role2User');
   }

}
