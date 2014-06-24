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
use APF\core\pagecontroller\APFObject;
use APF\core\service\APFService;
use APF\modules\usermanagement\biz\UmgtManager;
use APF\modules\usermanagement\biz\UmgtUserSessionStore;
use Exception;

/**
 * @package APF\modules\usermanagement\pres\condition
 * @class UserDependentContentConditionSet
 *
 * Represents a set of conditions that can be used to match the current situation to
 * decide whether a piece of content should be displayed or not.
 * <p/>
 * This component can be configured anywhere within the application:
 * as follows:
 * <pre>
 * // add condition
 * $condSet = &$this->getServiceObject('APF\modules\usermanagement\pres\condition',
 *                   'UserDependentContentConditionSet');
 * $condSet->addCondition(new FooCondition());
 *
 * // initialize with custom condition
 * $condSet = &$this->getServiceObject('APF\modules\usermanagement\pres\condition',
 *                   'UserDependentContentConditionSet');
 * $condSet
 *         ->resetConditionList()
 *         ->addCondition(new BarCondition());
 * </pre>
 * Please note, that it is *NOT* possible to add conditions outside from APF classes or without
 * providing context and language since 1.16!
 * <p/>
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
    * @public
    *
    * Initializes the condition set with the shipped conditions of the user management module.
    * <p/>
    * This is done within the constructor, since this method is called only once due to
    * service manager usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.07.2012<br />
    */
   public function __construct() {
      $this->addCondition(new UmgtLoggedOutCondition());
      $this->addCondition(new UmgtLoggedInCondition());
      $this->addCondition(new UmgtGroupCondition());
      $this->addCondition(new UmgtRoleCondition());
      $this->addCondition(new UmgtNotRoleCondition());
   }

   /**
    * @param UserDependentContentCondition $condition The condition to add.
    *
    * @return UserDependentContentConditionSet This instance for further usage.
    */
   public function &addCondition(UserDependentContentCondition $condition) {
      $this->conditions[] = $condition;

      return $this;
   }

   /**
    * Resets the condition list to be able to setup a custom configuration.
    *
    * @return UserDependentContentConditionSet This instance for further usage.
    */
   public function resetConditionList() {
      $this->conditions = array();

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
    *
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
      return $this->getDIServiceObject('APF\modules\usermanagement\biz', 'UmgtManager');
   }

   /**
    * @param string $conditionKey
    *
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
    * @param string $rawOptions The string that represents multiple condition options.
    *
    * @return array The options list.
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
      return $this->getServiceObject('APF\modules\usermanagement\biz\UmgtUserSessionStore', APFService::SERVICE_TYPE_SESSION_SINGLETON);
   }

}
