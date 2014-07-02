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
namespace APF\modules\usermanagement\biz;

use APF\core\configuration\Configuration;
use APF\core\configuration\ConfigurationException;
use APF\core\pagecontroller\APFObject;
use APF\modules\genericormapper\data\GenericCriterionObject;
use APF\modules\genericormapper\data\GenericORRelationMapper;
use APF\modules\usermanagement\biz\model\UmgtApplication;
use APF\modules\usermanagement\biz\model\UmgtAuthToken;
use APF\modules\usermanagement\biz\model\UmgtGroup;
use APF\modules\usermanagement\biz\model\UmgtPermission;
use APF\modules\usermanagement\biz\model\UmgtRole;
use APF\modules\usermanagement\biz\model\UmgtUser;
use APF\modules\usermanagement\biz\model\UmgtVisibilityDefinition;
use APF\modules\usermanagement\biz\model\UmgtVisibilityDefinitionType;
use APF\modules\usermanagement\biz\provider\PasswordHashProvider;
use APF\modules\usermanagement\biz\provider\UserFieldEncryptionProvider;

/**
 * @package APF\modules\usermanagement\biz
 * @class UmgtManager
 *
 * Business component of the user management module. In standard case the component uses a crypt
 * based provider to create password hashes. But you can add other providers to ensure compatibility
 * with older versions. Hashes will then get updated to your configured default provider on-the-fly.
 * If you desire to use another one, implement the PasswordHashProvider interface
 * and add it to the umgt's configuration file. For details on the implementation, please consult the manual!
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.04.2008<br />
 * Version 0.2, 23.06.2008 (Mapper is now loaded by an internal method that uses the GenericORMapperFactory)<br />
 * Version 0.3, 31.01.2009 (Introduced the possibility to switch the hash algo)<br />
 */
class UmgtManager extends APFObject {

   /**
    * @const string The name of the umgt manager's main config section.
    */
   const CONFIG_SECTION_NAME = 'Default';

   /**
    * @const int Cookie life-time in seconds.
    */
   const AUTO_LOGIN_COOKIE_LIFETIME = 2592000;

   /**
    * @var int Indicates the id of the current application/project.
    */
   private $applicationId = 1;

   /**
    * Stores the providers, that hashes the user's password.
    *
    * @var PasswordHashProvider[] The password hash providers.
    */
   protected $passwordHashProviders = array();

   /**
    * @var array Marks all password hash providers used within this instance. This marker is
    * used as reminder for session wake-up, too.
    */
   protected $passwordHashProviderList = array();

   /**
    * @var GenericORRelationMapper The current instance of the generic o/r mapper.
    */
   protected $orm;

   /**
    * @public
    *
    * DI initialization method to setup and re-initialize (on session restore!) the password hash providers.
    *
    * @author Christian Achatz, Ralf Schubert
    * @version
    * Version 0.1, 26.08.2011<br />
    */
   public function setup() {

      // we need to import the password hash providers on each request once, due to incomplete object
      // bug, when saving in session.
      if (count($this->passwordHashProviderList) === 0) {

         $passwordHashProvider = $this->getConfigurationSection()->getSection('PasswordHashProvider');
         if ($passwordHashProvider !== null) {
            $providerSectionNames = $passwordHashProvider->getSectionNames();

            // single provider given (and fallback for old configurations)
            if (count($providerSectionNames) === 0) {
               $passHashClass = $passwordHashProvider->getValue('Class');
               if ($passHashClass !== null) {
                  $this->passwordHashProviderList[] = array($passHashClass);
               }
            } else { // multiple providers given
               foreach ($providerSectionNames as $subSection) {
                  $passHashClass = $passwordHashProvider->getSection($subSection)->getValue('Class');
                  if ($passHashClass !== null) {
                     $this->passwordHashProviderList[] = array($passHashClass);
                  }
               }
            }

         }

         if (count($this->passwordHashProviderList) === 0) {
            // fallback to default provider
            $this->passwordHashProviderList[] = array('APF\modules\usermanagement\biz\provider\crypt\CryptHardcodedSaltPasswordHashProvider');
         }

      }

      // initialize the password hash providers or re-initialize because it
      // might contain incomplete objects
      if (count($this->passwordHashProviders) === 0) {
         $this->passwordHashProviders = array();
         foreach ($this->passwordHashProviderList as $provider) {
            $passwordHashProviderObject = $this->getServiceObject($provider[0]);
            $this->passwordHashProviders[] = $passwordHashProviderObject;
            unset($passwordHashProviderObject);
         }
      }

   }

   /**
    * @param int $applicationId The current application id.
    */
   public function setApplicationId($applicationId) {
      $this->applicationId = $applicationId;
   }

   /**
    * @return int The current application id.
    */
   public function getApplicationId() {
      return $this->applicationId;
   }

   /**
    * @return Configuration The configuration section used to initialize this service.
    */
   protected function getConfigurationSection() {
      return $this->getConfiguration('APF\modules\usermanagement\biz', 'umgtconfig.ini')->getSection(self::CONFIG_SECTION_NAME);
   }

   /**
    * @public
    *
    * When serialising in session, password hash providers need to be imported
    * to avoid incomplete object bug. Thus, "isInitialized" is not serialized.
    *
    * @return array The list of object properties to serialize.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 14.07.2011 <br />
    */
   public function __sleep() {
      return array(
            'language',
            'context',
            'serviceType',
            'applicationId',
            'passwordHashProviderList'
      );
   }

   /**
    * @protected
    *
    * Implements the comparing of stored hash with given password,
    * supporting fallback hash-providers and on-the-fly updating
    * of hashes in database to new providers.
    *
    * @param string $password the password to hash
    * @param UmgtUser $user current user.
    *
    * @return bool Returns true if password matches.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 21.06.2011 <br />
    */
   public function comparePasswordHash($password, UmgtUser &$user) {
      // check if current default hash provider matches
      $defaultHashedPassword = $this->createPasswordHash($password, $user);
      if ($user->getPassword() === $defaultHashedPassword) {
         return true;
      }

      // if there is no fallback provider, password didn't match
      if (count($this->passwordHashProviders) === 1) {
         return false;
      }

      // check each fallback, but skip default provider
      $firstSkipped = false;
      foreach ($this->passwordHashProviders as $passwordHashProvider) {
         if (!$firstSkipped) {
            $firstSkipped = true;
            continue;
         }
         $hashedPassword = $passwordHashProvider->createPasswordHash($password, $this->getDynamicSalt($user));
         if ($user->getPassword() === $hashedPassword) {
            // if fallback matched, first update hash in database to new provider (on-the-fly updating to new provider)
            $user->setPassword($password);
            $this->saveUser($user);

            return true;
         }
      }

      // no fallback matched, this user cannot be authenticated after all second tries. :(
      return false;
   }

   /**
    * @protected
    *
    * Implements the central dynamic salt method. If you desire to use another
    * dynamic salt, extend the UmgtManager and re-implement this method! Be sure,
    * to keep all other methods untouched.
    *
    * @param UmgtUser $user Current user
    *
    * @return string The dynamic salt
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 05.04.2011<br />
    */
   public function getDynamicSalt(UmgtUser &$user) {

      $dynamicSalt = $user->getDynamicSalt();
      $dynamicSalt = ($dynamicSalt === null) ? '' : trim($dynamicSalt);

      if ($dynamicSalt === '') {
         $dynamicSalt = md5(rand(10000, 99999));
         $user->setDynamicSalt($dynamicSalt);
      }

      return $dynamicSalt;

   }

   /**
    * @public
    *
    * Hashes the password for the given user with the first configured
    * hash provider, which represents the current default provider.
    * If you desire to use another hash algo, implement a PasswordHashProvider
    * and add it to the UmgtManager.
    *
    * @param string $password The password to hash
    * @param UmgtUser $user The current user.
    *
    * @return string The desired hash of the given password.
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 21.06.2011<br />
    */
   public function createPasswordHash($password, UmgtUser &$user) {
      return $this->passwordHashProviders[0]->createPasswordHash($password, $this->getDynamicSalt($user));
   }

   /**
    * @protected
    *
    * Returns an initialized Application object.
    *
    * @return UmgtApplication Current application domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    */
   protected function getCurrentApplication() {
      $app = new UmgtApplication();
      $app->setObjectId($this->applicationId);

      return $app;
   }

   /**
    * @public
    *
    * Returns the configured instance of the generic o/r mapper the user management
    * business component is currently using.
    * <p/>
    * This can be used to directly query the user management database in cases, the
    * UmgtManager is missing a special feature.
    *
    * @return GenericORRelationMapper Instance of the generic or relation mapper.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2008<br />
    * Version 0.2, 16.03.2010 (Bugfix 299: moved the service type to the GORM factory call)<br />
    * Version 0.1, 25.08.2011 (Switched to DI configuration)<br />
    */
   public function &getORMapper() {
      return $this->orm;
   }

   /**
    * @public
    *
    * Let's you inject the o/r mapper instance to use (used for DI configuration).
    *
    * @param GenericORRelationMapper $orm The o/r mapper instance to use.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 25.08.2011<br />
    */
   public function setORMapper(GenericORRelationMapper &$orm) {
      $this->orm = & $orm;
   }

   /**
    * @public
    *
    * Saves a user object within the current application.
    *
    * @param UmgtUser $user current user.
    *
    * @return int The id of the user.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    * Version 0.2, 23.06.2009 (Introduced a generic possibility to create the display name.)<br />
    * Version 0.3, 20.09.2009 (Bugfix for bug 202. Password was hased twice on update.)<br />
    * Version 0.4, 27.09.2009 (Bugfix for bug related to 202. Password for new user was not hashed.)<br />
    */
   public function saveUser(UmgtUser &$user) {

      $orm = & $this->getORMapper();

      // check, whether user is an existing user, and yes, resolve the
      // password conflict, described under http://forum.adventure-php-framework.org/viewtopic.php?f=8&t=202
      $userId = $user->getObjectId();
      $password = $user->getPassword();
      if ($userId !== null && $password !== null) {

         $storedUser = $orm->loadObjectByID('User', $userId);
         /* @var $storedUser UmgtUser */

         // In case, the stored password is different to the current one,
         // hash the password. In all other cases, the password would be
         // hashed twice!
         if ($storedUser->getPassword() != $password) {
            $user->setPassword($this->createPasswordHash($password, $user));
         } else {
            $user->deletePassword();
         }

      } else {
         // only create password for not empty strings!
         if (!empty($password)) {
            $user->setPassword($this->createPasswordHash($password, $user));
         }
      }

      // set display name
      $user->setDisplayName($this->getDisplayName($user));

      // save the user and return it's id
      $app = $this->getCurrentApplication();
      $user->addRelatedObject('Application2User', $app);

      return $orm->saveObject($user);

   }

   /**
    * @public
    *
    * Saves an application object.
    *
    * @param UmgtApplication $app The application object to save.
    *
    * @return int The id of the application.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.07.2010<br />
    */
   public function saveApplication(UmgtApplication &$app) {
      return $this->getORMapper()->saveObject($app);
   }

   /**
    * @public
    *
    * Saves a group object within the current application.
    *
    * @param UmgtGroup $group current group.
    *
    * @return int The id of the group.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    */
   public function saveGroup(UmgtGroup &$group) {
      $app = $this->getCurrentApplication();
      $group->addRelatedObject('Application2Group', $app);

      return $this->getORMapper()->saveObject($group);
   }

   /**
    * @public
    *
    * Saves a role object within the current application.
    *
    * @param UmgtRole $role current role.
    *
    * @return int The id of the role.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    */
   public function saveRole(UmgtRole &$role) {
      $app = $this->getCurrentApplication();
      $role->addRelatedObject('Application2Role', $app);

      return $this->getORMapper()->saveObject($role);
   }

   /**
    * @public
    *
    * Saves a permission object within the current application.
    *
    * @param UmgtPermission $permission the permission.
    *
    * @return int The id of the permission.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    * Version 0.2, 16.06.2008 (The permission set is lazy loaded when not present)<br />
    * Version 0.3, 28.12.2008 (Changed the API concerning the new UML diagram)<br />
    */
   public function savePermission(UmgtPermission &$permission) {
      $app = $this->getCurrentApplication();
      $permission->addRelatedObject('Application2Permission', $app);

      return $this->getORMapper()->saveObject($permission);
   }

   /**
    * @public
    *
    * Returns a list of users concerning the current page.
    *
    * @return UmgtUser[] List of users.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    * Version 0.2, 17.06.2008 (introduced query over current application)<br />
    */
   public function getPagedUserList() {
      $select = 'SELECT ent_user.* FROM ent_user
                    INNER JOIN cmp_application2user ON ent_user.UserID = cmp_application2user.Target_UserID
                    INNER JOIN ent_application ON cmp_application2user.Source_ApplicationID = ent_application.ApplicationID
                    WHERE ent_application.ApplicationID = \'' . $this->applicationId . '\'
                    ORDER BY ent_user.LastName ASC, ent_user.FirstName ASC';

      return $this->getORMapper()->loadObjectListByTextStatement('User', $select);
   }

   /**
    * @public
    *
    * Returns a list of groups concerning the current page.
    *
    * @return UmgtGroup[] List of groups.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   public function getPagedGroupList() {

      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Group', $app);
      $crit->addOrderIndicator('DisplayName', 'ASC');

      return $this->getORMapper()->loadObjectListByCriterion('Group', $crit);
   }

   /**
    * @public
    *
    * Returns a list of roles concerning the current page.
    *
    * @return UmgtRole[] List of roles.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function getPagedRoleList() {

      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Role', $app);
      $crit->addOrderIndicator('DisplayName', 'ASC');

      return $this->getORMapper()->loadObjectListByCriterion('Role', $crit);
   }

   /**
    * @public
    *
    * Returns a list of permissions concerning the current page.
    *
    * @return UmgtPermission[] List of permissions.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function getPagedPermissionList() {

      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Permission', $app);
      $crit->addOrderIndicator('DisplayName', 'ASC');

      return $this->getORMapper()->loadObjectListByCriterion('Permission', $crit);
   }

   /**
    * @public
    *
    * Returns the whole list of permissions.
    *
    * @return UmgtPermission[] A list of all permissions.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function getPermissionList() {
      $app = $this->getCurrentApplication();

      return $this->getORMapper()->loadRelatedObjects($app, 'Application2Permission');
   }

   /**
    * @public
    *
    * Returns a user domain object.
    *
    * @param int $userId id of the desired user
    *
    * @return UmgtUser The user domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function loadUserByID($userId) {
      return $this->getORMapper()->loadObjectByID('User', $userId);
   }

   /**
    * @public
    *
    * Returns a user domain object by it'd username and password.
    *
    * @param string $username the user's username.
    * @param string $password the user's password.
    *
    * @return UmgtUser The user domain object or null.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 30.12.2008<br />
    * Version 0.2, 02.01.2009 (Added sql injection security)<br />
    * Version 0.3, 31.01.2009 (Switched to the private hashing method)<br />
    * Version 0.4, 21.06.2011 (Supports fallback hash providers now)<br />
    */
   public function loadUserByUsernameAndPassword($username, $password) {

      $userObject = $this->loadUserByUserName($username);
      if ($userObject === null || !$this->comparePasswordHash($password, $userObject)) {
         return null;
      }

      return $userObject;
   }

   /**
    * @public
    *
    * Loads a user by it's display name.
    *
    * @param string $displayName The desired user's display name.
    *
    * @return UmgtUser|null The desired user or null in case the user has not been found.
    *
    * @author Coach83
    * @version
    * Version 0.1,10.2012<br />
    */
   public function loadUserByDisplayName($displayName) {

      $orm = & $this->getORMapper();

      if (UserFieldEncryptionProvider::propertyHasEncryptionEnabled('DisplayName')) {
         $displayName = UserFieldEncryptionProvider::encrypt($displayName);
      }

      // escape the input values
      $dbDriver = & $orm->getDbDriver();
      $displayName = $dbDriver->escapeValue($displayName);

      // create the statement and select user
      $select = 'SELECT * FROM `ent_user` WHERE `DisplayName` = \'' . $displayName . '\';';

      return $orm->loadObjectByTextStatement('User', $select);
   }

   /**
    * @public
    *
    * Loads a user by it's display name and password.
    *
    * @param string $displayName The desired user's display name.
    * @param string $password The user's password.
    *
    * @return UmgtUser|null The desired user or null in case the user has not been found or the password didn't match.
    *
    * @author Coach83
    * @version
    * Version 0.1,10.2012<br />
    */
   public function loadUserByDisplayNameAndPassword($displayName, $password) {

      $userObject = $this->loadUserByDisplayName($displayName);
      if ($userObject === null || !$this->comparePasswordHash($password, $userObject)) {
         return null;
      }

      return $userObject;
   }

   /**
    * @public
    *
    * Loads a user object by a given first name.
    *
    * @param string $firstName The first name of the user to load.
    *
    * @return UmgtUser The user domain object or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2009<br />
    */
   public function loadUserByFirstName($firstName) {

      $orm = & $this->getORMapper();

      if (UserFieldEncryptionProvider::propertyHasEncryptionEnabled('FirstName')) {
         $firstName = UserFieldEncryptionProvider::encrypt($firstName);
      }

      // escape the input values
      $dbDriver = & $orm->getDbDriver();
      $firstName = $dbDriver->escapeValue($firstName);

      // create the statement and select user
      $select = 'SELECT * FROM `ent_user` WHERE `FirstName` = \'' . $firstName . '\';';

      return $orm->loadObjectByTextStatement('User', $select);

   }

   /**
    * @public
    *
    * Loads a user object by a given last name.
    *
    * @param string $lastName The last name of the user to load.
    *
    * @return UmgtUser The user domain object or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2009<br />
    */
   public function loadUserByLastName($lastName) {

      $orm = & $this->getORMapper();

      if (UserFieldEncryptionProvider::propertyHasEncryptionEnabled('LastName')) {
         $lastName = UserFieldEncryptionProvider::encrypt($lastName);
      }

      // escape the input values
      $dbDriver = & $orm->getDbDriver();
      $lastName = $dbDriver->escapeValue($lastName);

      // create the statement and select user
      $select = 'SELECT * FROM `ent_user` WHERE `LastName` = \'' . $lastName . '\';';

      return $orm->loadObjectByTextStatement('User', $select);

   }

   /**
    * @public
    *
    * Loads a user object by a given email.
    *
    * @param string $email The email of the user to load.
    *
    * @return UmgtUser The user domain object or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2009<br />
    */
   public function loadUserByEMail($email) {

      $orm = & $this->getORMapper();

      if (UserFieldEncryptionProvider::propertyHasEncryptionEnabled('EMail')) {
         $email = UserFieldEncryptionProvider::encrypt($email);
      }

      // escape the input values
      $dbDriver = & $orm->getDbDriver();
      $email = $dbDriver->escapeValue($email);

      // create the statement and select user
      $select = 'SELECT * FROM `ent_user` WHERE `EMail` = \'' . $email . '\';';

      return $orm->loadObjectByTextStatement('User', $select);

   }

   /**
    * @public
    *
    * Loads a user object by a first and last name.
    *
    * @param string $firstName The first name of the user to load.
    * @param string $lastName The last name of the user to load.
    *
    * @return UmgtUser The user domain object or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2009<br />
    */
   public function loadUserByFirstNameAndLastName($firstName, $lastName) {

      $orm = & $this->getORMapper();

      if (UserFieldEncryptionProvider::propertyHasEncryptionEnabled('FirstName')) {
         $firstName = UserFieldEncryptionProvider::encrypt($firstName);
      }
      if (UserFieldEncryptionProvider::propertyHasEncryptionEnabled('LastName')) {
         $lastName = UserFieldEncryptionProvider::encrypt($lastName);
      }

      // escape the input values
      $dbDriver = & $orm->getDbDriver();
      $firstName = $dbDriver->escapeValue($firstName);
      $lastName = $dbDriver->escapeValue($lastName);

      // create the statement and select user
      $select = 'SELECT * FROM `ent_user` WHERE `FirstName` = \'' . $firstName . '\' AND `LastName` = \'' . $lastName . '\';';

      return $orm->loadObjectByTextStatement('User', $select);

   }

   /**
    * @public
    *
    * Loads a user object by a user name.
    *
    * @param string $username The user name of the user to load.
    *
    * @return UmgtUser The user domain object or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2009<br />
    */
   public function loadUserByUserName($username) {

      $orm = & $this->getORMapper();

      if (UserFieldEncryptionProvider::propertyHasEncryptionEnabled('Username')) {
         $username = UserFieldEncryptionProvider::encrypt($username);
      }

      // escape the input values
      $dbDriver = & $orm->getDbDriver();
      $username = $dbDriver->escapeValue($username);

      // create the statement and select user
      $select = 'SELECT * FROM `ent_user` WHERE `Username` = \'' . $username . '\';';

      return $orm->loadObjectByTextStatement('User', $select);

   }

   /**
    * @public
    *
    * Returns a user domain object by it'd email and password.
    *
    * @param string $email the user's email
    * @param string $password the user's password
    *
    * @return UmgtUser The user domain object or null
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    * Version 0.2, 02.01.2009 (Added sql injection security)<br />
    * Version 0.3, 31.01.2009 (Switched to the private hashing method)<br />
    */
   public function loadUserByEMailAndPassword($email, $password) {

      $userObject = $this->loadUserByEMail($email);
      if ($userObject === null || !$this->comparePasswordHash($password, $userObject)) {
         return null;
      }

      return $userObject;
   }

   /**
    * @protected
    *
    * Implements the central method to create the display name of a user object. If you desire
    * to use another algorithm, extend the UmgtManager and re-implement this method! Be sure, to keep
    * all other methods untouched.
    *
    * @param UmgtUser $user The user object to save.
    *
    * @return string The desired display name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2009<br />
    */
   protected function getDisplayName(UmgtUser $user) {
      $displayName = $user->getDisplayName();

      return empty($displayName)
            ? $user->getLastName() . ', ' . $user->getFirstName()
            : $user->getDisplayName();
   }

   /**
    * @public
    *
    * Returns a list of Permission domain objects for the given user.
    *
    * @param UmgtUser $user the user object
    *
    * @return UmgtPermission[] $permissions the user's permissions
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    * Version 0.2, 02.01.2009 (Implemented the method)<br />
    */
   public function loadUserPermissions(UmgtUser $user) {

      $orm = & $this->getORMapper();

      // load all roles by the user itself and it's groups
      $select = 'SELECT DISTINCT `ent_role`.`RoleID`
                 FROM `ent_role`
                 INNER JOIN `ass_role2user` ON `ent_role`.`RoleID` = `ass_role2user`.`Source_RoleID`
                 INNER JOIN `ent_user` ON `ass_role2user`.`Target_UserID` = `ent_user`.`UserID`
                 WHERE `ent_user`.`UserID` = \'' . $user->getObjectId() . '\';';
      /* @var $roles UmgtRole[] */
      $roles = $orm->loadObjectListByTextStatement('Role', $select);

      $groups = $this->loadGroupsWithUser($user);
      foreach ($groups as $group) {
         $select = 'SELECT DISTINCT `ent_role`.`RoleID`
                    FROM `ent_role`
                    INNER JOIN `ass_role2group` ON `ent_role`.`RoleID` = `ass_role2group`.`Source_RoleID`
                    INNER JOIN `ent_group` ON `ass_role2group`.`Target_GroupID` = `ent_group`.`GroupID`
                    WHERE `ent_group`.`GroupID` = \'' . $group->getObjectId() . '\';';
         $roles = array_merge($roles, $orm->loadObjectListByTextStatement('Role', $select));
      }

      // we can use array_unique() here, because GenericORMapperDataObject implements __toString() method
      $roles = array_unique($roles);
      $permissions = array();
      foreach ($roles as $role) {
         $select = 'SELECT DISTINCT `ent_permission`.*
                    FROM `ent_permission`
                    INNER JOIN `ass_role2permission` ON `ent_permission`.`PermissionID` = `ass_role2permission`.`Target_PermissionID`
                    INNER JOIN `ent_role` ON `ass_role2permission`.`Source_RoleID` = `ent_role`.`RoleID`
                    WHERE `ent_role`.`RoleID` = \'' . $role->getObjectId() . '\';';
         $permissions = array_merge($permissions, $orm->loadObjectListByTextStatement('Permission', $select));
      }

      // due to the fact, that unique'ing the array is a cost-intensive operation, we agreed to return a
      // duplicate set of permissions.
      return $permissions;
   }

   /**
    * @public
    *
    * Returns a group domain object.
    *
    * @param int $groupId id of the desired group
    *
    * @return UmgtGroup The group domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function loadGroupByID($groupId) {
      return $this->getORMapper()->loadObjectByID('Group', $groupId);
   }

   /**
    * @public
    *
    * Loads a group by a given name.
    *
    * @param string $groupName The name of the group to load
    *
    * @return UmgtGroup The desired group domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.06.2010<br />
    */
   public function loadGroupByName($groupName) {
      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('DisplayName', $groupName);

      return $this->getORMapper()->loadObjectByCriterion('Group', $crit);
   }

   /**
    * @public
    *
    * Returns a role domain object.
    *
    * @param int $roleId id of the desired role
    *
    * @return UmgtRole The role domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function loadRoleByID($roleId) {
      return $this->getORMapper()->loadObjectByID('Role', $roleId);
   }

   /**
    * @public
    *
    * Returns a role domain object identified by it's display name.
    *
    * @param string $name The name of the role to load.
    *
    * @return UmgtRole The desired role.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.08.2011<br />
    */
   public function loadRoleByName($name) {
      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('DisplayName', $name);

      return $this->getORMapper()->loadObjectByCriterion('Role', $crit);
   }

   /**
    * @public
    *
    * Loads a permission by it's id.
    *
    * @param int $permissionId the permission's id
    *
    * @return UmgtPermission The desiried permission.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2008<br />
    */
   public function loadPermissionByID($permissionId) {
      return $this->getORMapper()->loadObjectByID('Permission', $permissionId);
   }

   /**
    * @public
    *
    * Deletes a user.
    *
    * @param UmgtUser $user The user to delete
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function deleteUser(UmgtUser $user) {
      $this->getORMapper()->deleteObject($user);
   }

   /**
    * @public
    *
    * Deletes a group.
    *
    * @param UmgtGroup $group The group to delete
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function deleteGroup(UmgtGroup $group) {
      $this->getORMapper()->deleteObject($group);
   }

   /**
    * @public
    *
    *  Deletes a role.
    *
    * @param UmgtRole $role the role to delete
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function deleteRole(UmgtRole $role) {
      $this->getORMapper()->deleteObject($role);
   }

   /**
    * @public
    *
    *  Deletes a Permission.
    *
    * @param UmgtPermission $permission the permission
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2008<br />
    */
   public function deletePermission(UmgtPermission $permission) {
      $this->getORMapper()->deleteObject($permission);
   }

   /**
    * @public
    *
    *  Associates a user with a list of groups.
    *
    * @param UmgtUser $user the user
    * @param UmgtGroup[] $groups the group list
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2008<br />
    */
   public function attachUser2Groups(UmgtUser $user, array $groups) {

      $orm = & $this->getORMapper();

      // create associations
      for ($i = 0; $i < count($groups); $i++) {
         $orm->createAssociation('Group2User', $user, $groups[$i]);
      }

   }

   /**
    * @public
    *
    *  Associates users with a group.
    *
    * @param UmgtUser[] $users the user list
    * @param UmgtGroup $group the group
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2008<br />
    * Version 0.2, 18.02.2009 (Bug-fix: addUser2Groups() does not exist)<br />
    */
   public function attachUsers2Group(array $users, UmgtGroup $group) {
      for ($i = 0; $i < count($users); $i++) {
         $this->attachUser2Groups($users[$i], array($group));
      }
   }

   /**
    * @public
    *
    * Associates a role with a list of users.
    *
    * @param UmgtUser[] $users The user list.
    * @param UmgtRole $role The role.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function attachUsersToRole(array $users, UmgtRole $role) {
      $orm = & $this->getORMapper();
      foreach ($users as $user) {
         $orm->createAssociation('Role2User', $role, $user);
      }
   }

   /**
    * @public
    *
    * Associates a role with a list of users.
    *
    * @param UmgtUser $user The user.
    * @param UmgtRole[] $roles The role list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function attachUser2Roles(UmgtUser $user, array $roles) {
      foreach ($roles as $role) {
         $this->attachUsersToRole(array($user), $role);
      }
   }

   /**
    * @public
    *
    * Loads all groups, that are assigned to a given user.
    *
    * @param UmgtUser $user the user
    *
    * @return UmgtGroup[] The group list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    * Version 02, 05.09.2009 (Now using the GORM to load the related objects, to allow serialized objects to be used as arguments)<br />
    */
   public function loadGroupsWithUser(UmgtUser &$user) {
      return $this->getORMapper()->loadRelatedObjects($user, 'Group2User');
   }

   /**
    * @public
    *
    * Loads the groups that are assigned to the given role.
    *
    * @param UmgtRole $role The role to load the assigned groups.
    *
    * @return UmgtGroup[] The list of groups, that are assigned to the given role.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.09.2011<br />
    */
   public function loadGroupsWithRole(UmgtRole $role) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Group', $app);

      return $this->getORMapper()->loadRelatedObjects($role, 'Role2Group', $crit);
   }

   /**
    * @public
    *
    * Loads the groups that are *not* assigned to the given role.
    *
    * @param UmgtRole $role The role to load the *not* assigned groups.
    *
    * @return UmgtGroup[] The list of groups, that are *not* assigned to the given role.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.09.2011<br />
    */
   public function loadGroupsNotWithRole(UmgtRole $role) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Group', $app);

      return $this->getORMapper()->loadNotRelatedObjects($role, 'Role2Group', $crit);
   }

   /**
    * @param UmgtRole $role
    * @param UmgtGroup[] $groups
    */
   public function attachRoleToGroups(UmgtRole $role, array $groups) {
      $orm = & $this->getORMapper();
      foreach ($groups as $group) {
         $orm->createAssociation('Role2Group', $role, $group);
      }
   }

   /**
    * @param UmgtRole $role
    * @param UmgtGroup[] $groups
    */
   public function detachRoleToGroups(UmgtRole $role, array $groups) {
      $orm = & $this->getORMapper();
      foreach ($groups as $group) {
         $orm->deleteAssociation('Role2Group', $role, $group);
      }
   }

   /**
    * @public
    *
    * Loads all groups, that are not assigned to a given user.
    *
    * @param UmgtUser $user the user
    *
    * @return UmgtGroup[] The group list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function loadGroupsNotWithUser(UmgtUser &$user) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Group', $app);

      return $this->getORMapper()->loadNotRelatedObjects($user, 'Group2User', $crit);
   }

   /**
    * @public
    *
    *  Loads all users, that are assigned to a given group.
    *
    * @param UmgtGroup $group the group
    *
    * @return UmgtUser[] The user list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    * Version 0.2, 30.12.2008 (Removed null pointer typo)<br />
    */
   public function loadUsersWithGroup(UmgtGroup &$group) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2User', $app);

      return $this->getORMapper()->loadRelatedObjects($group, 'Group2User', $crit);
   }

   /**
    * @public
    *
    *  Loads all users, that are not assigned to a given group.
    *
    * @param UmgtGroup $group the group
    *
    * @return UmgtUser[] The user list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   public function loadUsersNotWithGroup(UmgtGroup $group) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2User', $app);

      return $this->getORMapper()->loadNotRelatedObjects($group, 'Group2User', $crit);
   }

   /**
    * @public
    *
    *  Loads all roles, that are assigned to a given user.
    *
    * @param UmgtUser $user the user
    *
    * @return UmgtRole[] The role list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   public function loadRolesWithUser(UmgtUser $user) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Role', $app);

      return $this->getORMapper()->loadRelatedObjects($user, 'Role2User', $crit);
   }

   /**
    * @public
    *
    *  Loads all roles, that are not assigned to a given user.
    *
    * @param UmgtUser $user the user
    *
    * @return UmgtRole[] The role list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   public function loadRolesNotWithUser(UmgtUser $user) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Role', $app);

      return $this->getORMapper()->loadNotRelatedObjects($user, 'Role2User', $crit);
   }

   /**
    * @public
    *
    *  Loads a list of users, that have a certail role.
    *
    * @param UmgtRole $role the role, the users should have
    *
    * @return UmgtUser[] Desired user list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2008<br />
    */
   public function loadUsersWithRole(UmgtRole $role) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2User', $app);

      return $this->getORMapper()->loadRelatedObjects($role, 'Role2User', $crit);
   }

   /**
    * @public
    *
    * Loads a list of users, that don't have the given role.
    *
    * @param UmgtRole $role The role, the users should not have
    *
    * @return UmgtUser[] Desired user list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    * Version 0.2, 28.12.2008 (Bug-fix: criterion definition contained wrong relation indicator)<br />
    */
   public function loadUsersNotWithRole(UmgtRole $role) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2User', $app);

      return $this->getORMapper()->loadNotRelatedObjects($role, 'Role2User', $crit);
   }

   /**
    * @public
    *
    * Loads all roles, that are *not* assigned the applied group.
    *
    * @param UmgtGroup $group The group to load the roles with.
    *
    * @return UmgtRole[] The list of roles, that are *not* assigned to the applied group.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.09.2011<br />
    */
   public function loadRolesNotWithGroup(UmgtGroup $group) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Role', $app);

      return $this->getORMapper()->loadNotRelatedObjects($group, 'Role2Group', $crit);
   }

   /**
    * @public
    *
    * Loads all roles, that are assigned the applied group
    *
    * @param UmgtGroup $group The group to load the roles with.
    *
    * @return UmgtRole[] The list of roles, that are assigned to the applied group.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.09.2011<br />
    */
   public function loadRolesWithGroup(UmgtGroup $group) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Role', $app);

      return $this->getORMapper()->loadRelatedObjects($group, 'Role2Group', $crit);
   }

   /**
    * @public
    *
    *  Detaches a user from a role.
    *
    * @param UmgtUser $user The user.
    * @param UmgtRole $role The desired role to detach the user from.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2008<br />
    */
   public function detachUserFromRole(UmgtUser $user, UmgtRole $role) {
      $this->getORMapper()->deleteAssociation('Role2User', $role, $user);
   }

   /**
    * @public
    *
    *  Detaches a user from one or more roles.
    *
    * @param UmgtUser $user The user.
    * @param UmgtRole[] $roles The desired role to detach the user from.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.09.2011<br />
    */
   public function detachUserFromRoles(UmgtUser $user, array $roles) {
      foreach ($roles as $role) {
         $this->detachUserFromRole($user, $role);
      }
   }

   /**
    * @public
    *
    *  Detaches users from a role.
    *
    * @param UmgtUser[] $users a list of users
    * @param UmgtRole $role the desired role to detach the users from
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2008<br />
    */
   public function detachUsersFromRole(array $users, UmgtRole $role) {
      for ($i = 0; $i < count($users); $i++) {
         $this->detachUserFromRole($users[$i], $role);
      }
   }

   /**
    * @public
    *
    *  Removes a user from the given groups.
    *
    * @param UmgtUser $user the desired user
    * @param UmgtGroup $group the group
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
    */
   public function detachUserFromGroup(UmgtUser $user, UmgtGroup $group) {
      $this->getORMapper()->deleteAssociation('Group2User', $user, $group);
   }

   /**
    * @public
    *
    *  Removes a user from the given groups.
    *
    * @param UmgtUser $user the desired user
    * @param UmgtGroup[] $groups a list of groups
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
    */
   public function detachUserFromGroups(UmgtUser $user, array $groups) {
      for ($i = 0; $i < count($groups); $i++) {
         $this->detachUserFromGroup($user, $groups[$i]);
      }
   }

   /**
    * @public
    *
    * Removes users from a given group.
    *
    * @param UmgtUser[] $users a list of users
    * @param UmgtGroup $group the desired group
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   public function detachUsersFromGroup(array $users, UmgtGroup $group) {

      for ($i = 0; $i < count($users); $i++) {
         $this->detachUserFromGroup($users[$i], $group);
      }

   }

   /**
    * @public
    *
    * @return UmgtRole[] The list of all permissions.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.08.2011<br />
    */
   public function getRoleList() {
      $app = $this->getCurrentApplication();

      return $this->getORMapper()->loadRelatedObjects($app, 'Application2Permission');
   }

   /**
    * @public
    *
    * Loads the permission associated to the given role.
    *
    * @param UmgtRole $role The role to load it's permissions.
    *
    * @return UmgtPermission[] The permissions that are assigned to the applied role.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.08.2011<br />
    */
   public function loadPermissionsWithRole(UmgtRole $role) {
      return $this->getORMapper()->loadRelatedObjects($role, 'Role2Permission');
   }

   /**
    * @public
    *
    * Loads the permission *not* associated to the given role.
    *
    * @param UmgtRole $role The role to load it's *not* associated permissions.
    *
    * @return UmgtPermission[] The permissions that are *not* assigned to the applied role.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.09.2011<br />
    */
   public function loadPermissionsNotWithRole(UmgtRole $role) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Permission', $app);

      return $this->getORMapper()->loadNotRelatedObjects($role, 'Role2Permission', $crit);
   }

   /**
    * @public
    *
    * Loads all roles that are connected to the applied permission.
    *
    * @param UmgtPermission $permission The permission to load the roles.
    *
    * @return UmgtRole[] The roles, that are assigned the given permission.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.08.2011<br />
    */
   public function loadRolesWithPermission(UmgtPermission $permission) {
      return $this->getORMapper()->loadRelatedObjects($permission, 'Role2Permission');
   }

   /**
    * @public
    *
    * Loads all roles that are *not* connected to the applied permission.
    *
    * @param UmgtPermission $permission The permission to load the roles.
    *
    * @return UmgtRole[] The roles, that are *not* assigned the given permission.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.08.2011<br />
    */
   public function loadRolesNotWithPermission(UmgtPermission $permission) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Role', $app);

      return $this->getORMapper()->loadNotRelatedObjects($permission, 'Role2Permission', $crit);
   }

   /**
    * @param UmgtPermission $permission The permission to attach to the applied roles.
    * @param UmgtRole[] $roles The roles to add the permission to.
    */
   public function attachPermission2Roles(UmgtPermission $permission, array $roles) {

      $orm = & $this->getORMapper();

      foreach ($roles as $role) {
         $orm->createAssociation('Role2Permission', $role, $permission);
      }

   }

   /**
    * @param UmgtPermission $permission The permission to detach from the applied roles.
    * @param UmgtRole[] $roles The roles to remove the permission from.
    */
   public function detachPermissionFromRoles(UmgtPermission $permission, array $roles) {

      $orm = & $this->getORMapper();

      foreach ($roles as $role) {
         $orm->deleteAssociation('Role2Permission', $role, $permission);
      }
   }

   /**
    * @param UmgtPermission[] $permissions The permissions to attach to the applied role.
    * @param UmgtRole $role The role to add the permissions to.
    */
   public function attachPermissions2Role(array $permissions, UmgtRole $role) {
      $orm = & $this->getORMapper();
      foreach ($permissions as $permission) {
         $orm->createAssociation('Role2Permission', $role, $permission);
      }
   }

   /**
    * @param UmgtPermission[] $permissions The permissions to detach from the applied role.
    * @param UmgtRole $role The role to remove the permissions from.
    */
   public function detachPermissionsFromRole(array $permissions, UmgtRole $role) {
      $orm = & $this->getORMapper();
      foreach ($permissions as $permission) {
         $orm->deleteAssociation('Role2Permission', $role, $permission);
      }
   }

   /**
    * @public
    *
    * Assigns a group to a list of roles.
    *
    * @param UmgtGroup $group The group to add to the applied roles.
    * @param UmgtRole[] $roles The roles to add the group to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.09.2011<br />
    */
   public function attachGroupToRoles(UmgtGroup $group, array $roles) {
      $orm = & $this->getORMapper();
      foreach ($roles as $role) {
         $orm->createAssociation('Role2Group', $role, $group);
      }
   }

   /**
    * @public
    *
    * Removes a group from a list of roles.
    *
    * @param UmgtGroup $group The group to remove from the applied roles.
    * @param UmgtRole[] $roles The roles to remove the group from.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.09.2011<br />
    */
   public function detachGroupFromRoles(UmgtGroup $group, array $roles) {
      $orm = & $this->getORMapper();
      foreach ($roles as $role) {
         $orm->deleteAssociation('Role2Group', $role, $group);
      }
   }

   /**
    * @public
    *
    * Creates a visibility definition relating an application proxy object to the users and
    * groups, that should have access to the object.
    *
    * @param UmgtVisibilityDefinitionType $type The visibility type (object type of application's the target object).
    * @param UmgtVisibilityDefinition $definition The visibility definition (object id of application's the target object).
    * @param UmgtUser[] $users The list of users, that should have visibility permissions on the given application object.
    * @param UmgtGroup[] $groups The list of groups, that should have visibility permissions on the given application object.
    *
    * @return int The id of the desired visibility definition.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.05.2010<br />
    */
   public function createVisibilityDefinition(UmgtVisibilityDefinitionType $type, UmgtVisibilityDefinition $definition, array $users = array(), array $groups = array()) {

      $orm = & $this->getORMapper();

      // try to reuse existing visibility definitions having the same
      // combination of proxy + type!
      $criterion = new GenericCriterionObject();
      $criterion->addPropertyIndicator('AppObjectId', $definition->getAppObjectId());

      $criterion->addRelationIndicator('AppProxy2AppProxyType', $type);

      // Allow best balance between creating new visibility definitions for proxy id, proxy type, and permission setup.
      // For details on the discussion, please refer to http://forum.adventure-php-framework.org/viewtopic.php?f=1&t=5387.
      $criterion->addPropertyIndicator('ReadPermission', $definition->getReadPermission());
      $criterion->addPropertyIndicator('WritePermission', $definition->getWritePermission());
      $criterion->addPropertyIndicator('LinkPermission', $definition->getLinkPermission());
      $criterion->addPropertyIndicator('DeletePermission', $definition->getDeletePermission());

      $storedVisibilityDefinition = $orm->loadObjectByCriterion('AppProxy', $criterion);
      if ($storedVisibilityDefinition !== null) {
         $definition = $storedVisibilityDefinition;
      }

      // append proxy to current application
      $app = $this->getCurrentApplication();
      $definition->addRelatedObject('Application2AppProxy', $app);

      // create domain structure
      $definition->addRelatedObject('AppProxy2AppProxyType', $type);

      foreach ($users as $id => $DUMMY) {
         $definition->addRelatedObject('AppProxy2User', $users[$id]);
      }
      foreach ($groups as $id => $group) {
         $definition->addRelatedObject('AppProxy2Group', $groups[$id]);
      }

      // save domain structure
      return $orm->saveObject($definition);
   }

   /**
    * @public
    *
    * Deletes a visibility definition including all associations.
    *
    * @param UmgtVisibilityDefinition $definition The visibility definition to delete.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.06.2010<br />
    */
   public function deleteVisibilityDefinition(UmgtVisibilityDefinition $definition) {
      $this->getORMapper()->deleteObject($definition);
   }

   /**
    * @public
    *
    * Revokes access of the passed users to the given application proxy object.
    *
    * @param UmgtVisibilityDefinition $definition The application proxy object.
    * @param UmgtUser[] $users A list of users, that should be revoked access to the given application proxy.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function detachUsersFromVisibilityDefinition(UmgtVisibilityDefinition $definition, array $users) {
      $orm = & $this->getORMapper();
      foreach ($users as $user) {
         $orm->deleteAssociation('AppProxy2User', $definition, $user);
      }
   }

   /**
    * @public
    *
    * Revokes access of the passed groups to the given application proxy object.
    *
    * @param UmgtVisibilityDefinition $definition The application proxy object.
    * @param UmgtGroup[] $groups A list of groups, that should be revoked access to the given application proxy.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function detachGroupsFromVisibilityDefinition(UmgtVisibilityDefinition $definition, array $groups) {
      $orm = & $this->getORMapper();
      foreach ($groups as $group) {
         $orm->deleteAssociation('AppProxy2Group', $definition, $group);
      }
   }

   /**
    * @public
    *
    * Adds a given list of users to the applied visibility definition.
    *
    * @param UmgtVisibilityDefinition $definition The desired visibility definition.
    * @param UmgtUser[] $users The list of users to detach.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function attachUsers2VisibilityDefinition(UmgtVisibilityDefinition $definition, array $users) {
      $orm = & $this->getORMapper();
      foreach ($users as $user) {
         $orm->createAssociation('AppProxy2User', $definition, $user);
      }
   }

   /**
    * @public
    *
    * Adds a given list of groups to the applied visibility definition.
    *
    * @param UmgtVisibilityDefinition $definition The desired visibility definition.
    * @param UmgtGroup[] $groups The list of users to detach.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function attachGroups2VisibilityDefinition(UmgtVisibilityDefinition $definition, array $groups) {
      $orm = & $this->getORMapper();
      foreach ($groups as $group) {
         $orm->createAssociation('AppProxy2Group', $definition, $group);
      }
   }

   /**
    * @public
    *
    * Returns a list of all visibility definitions for the current application.
    *
    * @param UmgtVisibilityDefinitionType $type An optional visibility definitioyn type marker to limit the result.
    *
    * @return UmgtVisibilityDefinition[] The list of visibility definitions for the current application.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    * Version 0.2, 01.11.2010 (Added type restriction possibility)<br />
    */
   public function getPagedVisibilityDefinitionList(UmgtVisibilityDefinitionType $type = null) {
      $app = $this->getCurrentApplication();

      // limit result to the given type is desired
      if ($type === null) {
         $crit = null;
      } else {
         $crit = new GenericCriterionObject();
         $crit->addRelationIndicator('AppProxy2AppProxyType', $type);
      }

      return $this->getORMapper()->loadRelatedObjects($app, 'Application2AppProxy', $crit);
   }

   /**
    * @public
    *
    * Loads the list of users, that have visibility permissions on the given proxy object.
    *
    * @param UmgtVisibilityDefinition $proxy The proxy object.
    *
    * @return UmgtUser[] The list of users, that have visibility permission.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.05.2010<br />
    */
   public function loadUsersWithVisibilityDefinition(UmgtVisibilityDefinition $proxy) {
      return $this->getORMapper()->loadRelatedObjects($proxy, 'AppProxy2User');
   }

   /**
    * @public
    *
    * Loads the list of groups, that have visibility permissions on the given proxy object.
    *
    * @param UmgtVisibilityDefinition $proxy The proxy object.
    *
    * @return UmgtGroup[] The list of groups, that have visibility permission.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.05.2010<br />
    */
   public function loadGroupsWithVisibilityDefinition(UmgtVisibilityDefinition $proxy) {
      return $this->getORMapper()->loadRelatedObjects($proxy, 'AppProxy2Group');
   }

   /**
    * @public
    *
    * Loads a mixed list of users and groups, that have access to a given proxy.
    * Sorts the result according to the display name of the user and group.
    *
    * @param UmgtVisibilityDefinition $proxy The proxy the users and groups have access to.
    *
    * @return UmgtUser[]|UmgtGroup[] A mixed list of users and groups, that have access to a given proxy.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.06.2010<br />
    */
   public function loadUsersAndGroupsWithVisibilityDefinition(UmgtVisibilityDefinition $proxy) {
      return array_merge(
            $this->loadUsersWithVisibilityDefinition($proxy),
            $this->loadGroupsWithVisibilityDefinition($proxy)
      );
   }

   /**
    * @public
    *
    * Loads the list of users, that do not have visibility permissions on the given application proxy.
    *
    * @param UmgtVisibilityDefinition $definition The appropriate visibility definition.
    *
    * @return UmgtUser[] The users, that do not have visibility permissions in the given object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.06.2010<br />
    */
   public function loadUsersNotWithVisibilityDefinition(UmgtVisibilityDefinition $definition) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2User', $app);

      return $this->getORMapper()->loadNotRelatedObjects($definition, 'AppProxy2User', $crit);
   }

   /**
    * @public
    *
    * Loads the list of groups, that do not have visibility permissions on the given application proxy.
    *
    * @param UmgtVisibilityDefinition $definition The appropriate visibility definition.
    *
    * @return UmgtGroup[] The groups, that do not have visibility permissions in the given object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.06.2010<br />
    */
   public function loadGroupsNotWithVisibilityDefinition(UmgtVisibilityDefinition $definition) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Group', $app);

      return $this->getORMapper()->loadNotRelatedObjects($definition, 'AppProxy2Group', $crit);
   }

   /**
    * @public
    *
    * Loads the list of visibility definitions for the given type.
    *
    * @param UmgtVisibilityDefinitionType $type The visibility definition type.
    *
    * @return UmgtVisibilityDefinition[] A list of visibility definitions of the given type.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.05.2010<br />
    */
   public function loadVisibilityDefinitionsByType(UmgtVisibilityDefinitionType $type) {
      return $this->getORMapper()->loadRelatedObjects($type, 'AppProxy2AppProxyType');
   }

   /**
    * @public
    *
    * Loads all visibility definitions for the current user and it's group restricted by the
    * given visibility definition type (e.g. <em>Page</em>).
    *
    * @param UmgtUser $user The currently logged-in user.
    * @param UmgtVisibilityDefinitionType $type The type of visibility definition (e.g. <em>Page</em>)
    *
    * @return UmgtVisibilityDefinition[] A list of visibility definitions, the user and it'd groups have access to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.06.2010<br />
    */
   public function loadAllVisibilityDefinitions(UmgtUser $user, UmgtVisibilityDefinitionType $type = null) {

      $visDefs = $this->loadVisibilityDefinitionsByUser($user, $type);

      $groups = $this->loadGroupsWithUser($user);
      foreach ($groups as $id => $DUMMY) {
         $visDefs = array_merge($visDefs, $this->loadVisibilityDefinitionsByGroup($groups[$id], $type));
      }

      return array_slice(array_unique($visDefs), 0); // array_slice is used to re-order the array efficiently
   }

   /**
    * @public
    *
    * Loads all visibility definition for the given user restricted by the
    * given visibility definition type (e.g. <em>Page</em>).
    *
    * @param UmgtUser $user The currently logged-in user.
    * @param UmgtVisibilityDefinitionType $type The type of visibility definition (e.g. <em>Page</em>)
    *
    * @return UmgtVisibilityDefinition[] A list of visibility definitions, the user has access to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.06.2010<br />
    */
   public function loadVisibilityDefinitionsByUser(UmgtUser $user, UmgtVisibilityDefinitionType $type = null) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2AppProxy', $app);

      if ($type !== null) {
         $crit->addRelationIndicator('AppProxy2AppProxyType', $type);
      }

      return $this->getORMapper()->loadRelatedObjects($user, 'AppProxy2User', $crit);
   }

   /**
    * @public
    *
    * Loads all visibility definition for the given group restricted by the
    * given visibility definition type (e.g. <em>Page</em>).
    *
    * @param UmgtGroup $group A desired group.
    * @param UmgtVisibilityDefinitionType $type The type of visibility definition (e.g. <em>Page</em>)
    *
    * @return UmgtVisibilityDefinition[] A list of visibility definitions, the group has access to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.06.2010<br />
    */
   public function loadVisibilityDefinitionsByGroup(UmgtGroup $group, UmgtVisibilityDefinitionType $type = null) {
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2AppProxy', $app);

      if ($type !== null) {
         $crit->addRelationIndicator('AppProxy2AppProxyType', $type);
      }

      return $this->getORMapper()->loadRelatedObjects($group, 'AppProxy2Group', $crit);
   }

   /**
    * @public
    *
    * Loads a visibility definition by it's object id.
    *
    * @param string $id The of the visibility definition.
    *
    * @return UmgtVisibilityDefinition The desired visibility definition.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.05.2010<br />
    */
   public function loadVisibilityDefinitionById($id) {
      return $this->getORMapper()->loadObjectByID('AppProxy', $id);
   }

   /**
    * @public
    *
    * Loads a visibility definition by it's application object id and the type.
    * Providing the type is necessary due to the fact, that app objct id is not
    * unique throughout the system but the combination with the type is.
    *
    * @param int $appObjectId The application object id of the visibility definition.
    *
    * @return UmgtVisibilityDefinition The desired visibility definition.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.07.2010<br />
    * Version 0.2, 02.07.2010 (Added type for conceptual uniqueness reasons)<br />
    */
   public function loadVisibilityDefinitionByAppObjectId($appObjectId) {
      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('AppObjectId', $appObjectId);

      return $this->getORMapper()->loadObjectByCriterion('AppProxy', $crit);
   }

   /**
    * @public
    *
    * Saves a visibility definition type used to categorize an application object.
    *
    * @param UmgtVisibilityDefinitionType $proxyType The type to save.
    *
    * @return int The id of the type.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.04.2010<br />
    */
   public function saveVisibilityDefinitionType(UmgtVisibilityDefinitionType &$proxyType) {
      $app = $this->getCurrentApplication();
      $proxyType->addRelatedObject('Application2AppProxyType', $app);

      return $this->getORMapper()->saveObject($proxyType);
   }

   /**
    * @public
    *
    * Recursively deletes the proxy type and it's descendants (proxies + associations).
    *
    * @param UmgtVisibilityDefinitionType $proxyType The proxy type to delete.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.05.2010<br />
    */
   public function deleteVisibilityDefinitionType(UmgtVisibilityDefinitionType &$proxyType) {
      $proxies = $this->loadVisibilityDefinitionsByType($proxyType);
      $orm = & $this->getORMapper();
      foreach ($proxies as $proxy) {
         $orm->deleteObject($proxy);
      }
      $orm->deleteObject($proxyType);
   }

   /**
    * @public
    *
    * Returns a proxy type object specified by the given id.
    *
    * @param int $id The id of the proxy type.
    *
    * @return UmgtVisibilityDefinitionType The desired proxy type.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.04.2010<br />
    */
   public function loadVisibilityDefinitionTypeById($id) {
      return $this->getORMapper()->loadObjectByID('AppProxyType', $id);
   }

   /**
    * @param string $name The name of the visibility definition type.
    *
    * @return UmgtVisibilityDefinitionType
    */
   public function loadVisibilityDefinitionTypeByName($name) {
      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('AppObjectName', $name);

      return $this->getORMapper()->loadObjectByCriterion('AppProxyType', $crit);
   }

   /**
    * @public
    *
    * Returns the type associated to the given application proxy object.
    *
    * @param UmgtVisibilityDefinition $proxy The proxy object to load the type of.
    *
    * @return UmgtVisibilityDefinitionType The desired proxy type.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function loadVisibilityDefinitionType(UmgtVisibilityDefinition $proxy) {
      $crit = new GenericCriterionObject();
      $crit->addRelationIndicator('AppProxy2AppProxyType', $proxy);

      return $this->getORMapper()->loadObjectByCriterion('AppProxyType', $crit);
   }

   /**
    * @public
    *
    * Returns a list of proxy type objects. They represent a certain
    * data type (=class) within the application the user management
    * module is integrated in. Managing visibility, proxy types in
    * conjunction with proxy objects for the dedicated objects of the
    * desired type must be created.
    *
    * @return UmgtVisibilityDefinitionType[] List of proxy types.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.04.2010<br />
    */
   public function loadVisibilityDefinitionTypes() {
      $crit = new GenericCriterionObject();
      $crit->addOrderIndicator('AppObjectName');

      return $this->getORMapper()->loadObjectListByCriterion('AppProxyType', $crit);
   }

   /**
    * @public
    *
    * Fetches the token representation by a given token string.
    * <p/>
    * This method is used to resolve the user by the current cookie.
    *
    * @param string $token The token string from the auto login cookie.
    *
    * @return UmgtAuthToken|null Returns the desired token or null, if no token has been found.
    */
   public function loadAuthTokenByTokenString($token) {
      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('Token', $token);

      return $this->getORMapper()->loadObjectByCriterion('AuthToken', $crit);
   }

   /**
    * @public
    *
    * Resolves the appropriate user from the given auth token.
    *
    * @param UmgtAuthToken $token The current auth token.
    *
    * @return UmgtUser|null The corresponding user or null if the user cannot be found.
    */
   public function loadUserByAuthToken(UmgtAuthToken $token) {
      $crit = new GenericCriterionObject();
      $crit->addRelationIndicator('User2AuthToken', $token);

      return $this->getORMapper()->loadObjectByCriterion('User', $crit);
   }

   /**
    * @public
    *
    * Creates/saves an auto-login token for the given user.
    *
    * @param UmgtUser $user The user to create the token for.
    * @param UmgtAuthToken $token The user's auto-login token.
    */
   public function saveAuthToken(UmgtUser $user, UmgtAuthToken $token) {
      $token->addRelatedObject('User2AuthToken', $user);
      $this->getORMapper()->saveObject($token);
   }

   /**
    * @public
    *
    * Returns the life time of the auto login cookie.
    *
    * @return int The life time of the auto login cookie.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.10.2012<br />
    */
   public function getAutoLoginCookieLifeTime() {
      try {
         $config = $this->getConfiguration('APF\modules\usermanagement\pres', 'login.ini');
         $section = $config->getSection(UmgtManager::CONFIG_SECTION_NAME);
         $cookieLifeTime = $section == null
               ? self::AUTO_LOGIN_COOKIE_LIFETIME
               : $section->getValue('cookie.lifetime', self::AUTO_LOGIN_COOKIE_LIFETIME);
      } catch (ConfigurationException $e) {
         $cookieLifeTime = self::AUTO_LOGIN_COOKIE_LIFETIME;
      }

      return $cookieLifeTime;
   }

   /**
    * Activates encryption. Loads configurations and gives them to encryption provider.
    * Needs to be called before using UmgtUser-Objects or loading data from umgt,
    * when encryption should be used.
    */
   public function activateEncryption() {
      if (UserFieldEncryptionProvider::$encryptedFieldNames === null) {
         $config = $this->getConfigurationSection()->getSection('FieldEncryption');
         /* @var $config Configuration */

         if ($config === null) {
            return;
         }

         $fieldNamesString = $config->getValue('FieldNames', '');
         if (strlen($fieldNamesString) !== 0) {
            UserFieldEncryptionProvider::$encryptedFieldNames = explode('|', $fieldNamesString);
            UserFieldEncryptionProvider::$encryptionConfigKey = $config->getValue('Key', '');
         }
      }
   }

}
