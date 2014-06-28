<?php
namespace APF\modules\usermanagement\pres\condition;

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
use APF\modules\usermanagement\biz\model\UmgtUser;
use APF\modules\usermanagement\pres\condition\UserDependentContentCondition;

/**
 * Implements the decision logic, whether a user is logged out.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2011
 */
class UmgtLoggedOutCondition extends UserDependentContentConditionBase implements UserDependentContentCondition {

   public function matches($conditionKey, UmgtUser $user = null) {
      return $user === null;
   }

   public function getConditionIdentifier() {
      return 'logged-out';
   }

}
