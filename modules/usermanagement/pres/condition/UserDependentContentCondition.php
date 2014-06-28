<?php
namespace APF\modules\usermanagement\pres\condition;

/**
 * Defines the structure of a user dependent content condition for the user
 * mangement module.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2011
 */
use APF\modules\usermanagement\biz\model\UmgtUser;

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
interface UserDependentContentCondition {

   /**
    * @param string $conditionKey The name of the condition.
    * @param UmgtUser $user The current user.
    *
    * @return boolean true in case the condition matches, false otherwise.
    */
   public function matches($conditionKey, UmgtUser $user = null);

   /**
    * @return string The condition key.
    */
   public function getConditionIdentifier();

   /**
    * @param array $options A set of options that are defined for the dicision (e.g. groups).
    */
   public function setOptions(array $options);
}
