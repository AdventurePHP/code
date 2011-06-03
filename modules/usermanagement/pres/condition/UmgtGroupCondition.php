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
import('modules::usermanagement::pres::condition', 'UserDependentContentCondition');

/**
 * @package modules::usermanagement::pres::condition
 * @class UmgtGroupCondition
 *
 * Implements the decision logic, whether a user is part of the groups
 * given in the options array.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2011
 */
class UmgtGroupCondition extends UserDependentContentConditionBase implements UserDependentContentCondition {

   public function matches($conditionKey, GenericDomainObject $user = null) {

      if ($user === null) {
         return false;
      }

      foreach ($this->getGroups($user) as $group) {
         /* @var $group GenericDomainObject */
         if (in_array($group->getProperty('DisplayName'), $this->getOptions())) {
            return true;
         }
      }
      return false;
   }

   public function getConditionIdentifier() {
      return 'group';
   }

   private function getGroups(GenericDomainObject $user) {
      return $user->loadRelatedObjects('Group2User');
   }

}

?>