<?php
namespace APF\modules\usermanagement\biz;

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
use APF\modules\usermanagement\biz\model\UmgtUser;
use InvalidArgumentException;

/**
 * Stores the user information for each application identifier separately to
 * support multiple applications being executed at one context/application.
 * <p/>
 * In order to use the store within your application, please retrieve the store
 * using the service manager:
 * <pre>
 * $store = &$this->getServiceObject(
 *             'APF\modules\usermanagement\biz\UmgtUserSessionStore',
 *             APFService::SERVICE_TYPE_SESSION_SINGLETON);
 * </pre>
 * Otherwise, the scope of the object is not "session".
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 01.06.2011<br />
 */
class UmgtUserSessionStore extends APFObject {

   /**
    * The application-key-dependent session store.
    *
    * @var array $store
    */
   private $store;

   /**
    * Let's you retrieve the current user by a given application identifier. This key
    * represents the application you want to store your login information.
    *
    * @param string $applicationIdentifier Identifies the application.
    *
    * @return UmgtUser The currently logged-in user.
    * @throws InvalidArgumentException In case the application identifier is not given.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.06.2011<br />
    */
   public function getUser($applicationIdentifier) {
      if (empty($applicationIdentifier)) {
         throw new InvalidArgumentException($this->getExceptionMessage());
      }

      return isset($this->store[$applicationIdentifier]) ? $this->store[$applicationIdentifier] : null;
   }

   /**
    * Let's you store the current user by a given application identifier. This key
    * represents the application you want to store your login information.
    *
    * @param string $applicationIdentifier Identifies the application.
    * @param UmgtUser $user The user to store within the session.
    *
    * @throws InvalidArgumentException In case the application identifier is not given.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.06.2011<br />
    */
   public function setUser($applicationIdentifier, UmgtUser $user) {
      if (empty($applicationIdentifier)) {
         throw new InvalidArgumentException($this->getExceptionMessage());
      }
      $this->store[$applicationIdentifier] = $user;
   }

   /**
    * Indicates, whether the user is logged in for the current application.
    *
    * @param string $applicationIdentifier Identifies the application.
    *
    * @return boolean True in case the user is logged in, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.06.2011<br />
    */
   public function isLoggedIn($applicationIdentifier) {
      return $this->getUser($applicationIdentifier) !== null;
   }

   /**
    * Indicates, whether the user is logged out for the current application.
    *
    * @param string $applicationIdentifier Identifies the application.
    *
    * @return boolean True in case the user is logged out, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.06.2011<br />
    */
   public function isLoggedOut($applicationIdentifier) {
      return !$this->isLoggedIn($applicationIdentifier);
   }

   /**
    * Logs out the user for the current application.
    *
    * @param string $applicationIdentifier Identifies the application.
    *
    * @throws InvalidArgumentException In case the application identifier is not given.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.06.2011<br />
    */
   public function logout($applicationIdentifier) {
      if (empty($applicationIdentifier)) {
         throw new InvalidArgumentException($this->getExceptionMessage());
      }
      unset($this->store[$applicationIdentifier]);
   }

   private function getExceptionMessage() {
      return 'Application identifier must not be null or empty! Please check you application setup.';
   }

}
