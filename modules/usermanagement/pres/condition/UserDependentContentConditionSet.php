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
import('modules::genericormapper::data', 'GenericDomainObject');
import('modules::usermanagement::pres::condition', 'UserDependentContentCondition');

import('modules::usermanagement::pres::condition', 'UmgtLoggedOutCondition');
import('modules::usermanagement::pres::condition', 'UmgtLoggedInCondition');
import('modules::usermanagement::pres::condition', 'UmgtGroupCondition');
import('modules::usermanagement::pres::condition', 'UmgtRoleCondition');
import('modules::usermanagement::pres::condition', 'UmgtNotRoleCondition');

/**
 * @package modules::usermanagement::pres::condition
 * @class UserDependentContentConditionSet
 *
 * Represents a set of conditions that can be used to match the current situation to
 * decide whether a piece of content should be displayed or not.
 * <p/>
 * This component can be configured anywhere within the application:
 * as follows:
 * <pre>
 * // within classes derived from APFObject
 * $condSet = &$this->getServiceObject('modules::usermanagement::pres::condition',
 *                   'UserDependentContentConditionSet');
 * $condSet->addCondition(new FooCondition());
 *
 * // outside classes (this is possible, since the
 * // condition set needs no context and language)
 * $condSet = &ServiceManager::getServiceObject('modules::usermanagement::pres::condition',
 *                   'UserDependentContentConditionSet', null, null);
 * $condSet->addCondition(new BarCondition());
 * </pre>
 * By default, the set is used as request-singleton container through the service manager.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2011
 */
class UserDependentContentConditionSet extends APFObject {

   /**
    * @var array The registered conditions
    */
   private $conditions = array();

   /**
    * @param UserDependentContentCondition $condition
    * @return UserDependentContentConditionSet
    */
   public function &addCondition(UserDependentContentCondition $condition) {
      $this->conditions[] = $condition;
      return $this;
   }

   /**
    * @public
    *
    * Executes the conditions and evaluates the results.
    *
    * @param string $applicationIdentifier The identifier of the current application concerning the login state.
    * @param string $conditionKey The current condition.
    * @param string $options The options to apply to the conditions.
    * @return bool True, in case one of the condition matches, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2011
    */
   public function conditionMatches($applicationIdentifier, $conditionKey, $options) {

      $user = $this->getUserSessionStore()->getUser($applicationIdentifier);

      if ($user !== null) {
         // inject data component to be able to retrieve further parameters (e.g. related objects)
         $user->setDataComponent($this->getUmgtManager()->getORMapper());
      }

      try {
         $condition = $this->getCondition($conditionKey);

         // apply options from the current call to avoid interference
         $parsedOptions = $this->getOptions($options);
         $condition->setOptions($parsedOptions);

         return $condition === null ? false : $condition->matches($conditionKey, $user);
      } catch (Exception $e) {
         return false;
      }
   }

   /**
    * @return UmgtManager
    */
   private function &getUmgtManager() {
      return $this->getAndInitServiceObject('modules::usermanagement::biz', 'UmgtManager', 'Default');
   }

   /**
    * @param string $conditionKey
    * @return UserDependentContentCondition
    * @throws Exception In case no condition can be found for the given condition key.
    */
   private function getCondition($conditionKey) {

      foreach ($this->conditions as $condition) {
         /* @var $condition UserDependentContentCondition */
         if ($conditionKey === $condition->getConditionIdentifier()) {
            return $condition;
         }
      }

      throw new Exception('No condition found for key "' . $conditionKey . '".');
   }

   /**
    * @param string $rawOptions
    * @return array The options
    */
   private function getOptions($rawOptions) {
      $options = array();
      foreach (explode(',', $rawOptions) as $option) {
         $options[] = trim($option);
      }
      return $options;
   }

   /**
    * @return UmgtUserSessionStore The session store of the umgt module.
    */
   private function &getUserSessionStore() {
      return $this->getServiceObject('modules::usermanagement::biz', 'UmgtUserSessionStore', APFObject::SERVICE_TYPE_SESSIONSINGLETON);
   }

}

// Initialize the condition set here, because the APF inclusion mechanism ensures
// that the code is only executed only once. Further, this is possible, due to the
// fact, that the UserDependentContentConditionSet is neither context nor language
// dependent.
$condSet = &ServiceManager::getServiceObject('modules::usermanagement::pres::condition', 'UserDependentContentConditionSet', null, null);
/* @var $condSet UserDependentContentConditionSet */
$condSet->addCondition(new UmgtLoggedOutCondition());
$condSet->addCondition(new UmgtLoggedInCondition());
$condSet->addCondition(new UmgtGroupCondition());
$condSet->addCondition(new UmgtRoleCondition());
$condSet->addCondition(new UmgtNotRoleCondition());
?>