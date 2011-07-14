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
import('modules::genericormapper::data', 'GenericCriterionObject');

/**
 * @package modules::usermanagement::biz
 * @module UmgtManager
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
    * @protected
    * @var int Indicates the id of the current application/project.
    */
   protected $applicationId = 1;

   /**
    * @protected
    * @var string Indicates the database connection key.
    */
   protected $connectionKey = null;

   /**
    * @protected
    * @var boolean indicates, if the component is already initialized.
    */
   protected $isInitialized = false;
   
   /**
    * Stores the providers, that hashes the user's password.
    * @var PasswordHashProvider[] The password hash providers.
    */
   protected $passwordHashProviders = array();

   /**
    * Due to incomplete object problems, we need to now this state explicit
    * @var boolean Indicates if we already imported the providers in this request
    */
   protected $passwordHashProvidersAreImported = false;
   
    /**
    * @protected
    * @var string The service mode of the generic or mapper.
    */
    protected $gormServiceMode = APFObject::SERVICE_TYPE_SESSIONSINGLETON;

   public function __construct() {
      $this->gormServiceMode = APFObject::SERVICE_TYPE_SESSIONSINGLETON;
   }

   /**
    * @public
    *
    * Implements the init() method for the service manager. Initializes the connection key.
    *
    * @param string $connectionKey the desired connection key
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.12.2008<br />
    */
   public function init($initParam) {
      
      $PasswordHashProviderList = array();
      
      // we need to import the password hash providers on each request once, due to incomplete object
      // bug, when saving in session.
      if(!$this->passwordHashProvidersAreImported){
          
          $passwordHashProvider = $section->getSection('PasswordHashProvider');
          if($passwordHashProvider !== null) {
              $providerSectionNames = $passwordHashProvider->getSectionNames();

              // single provider given (and fallback for old configurations)
              if(count($providerSectionNames) === 0){
                  $passHashNamespace = $passwordHashProvider->getValue('Namespace');
                  $passHashClass = $passwordHashProvider->getValue('Class');
                  if($passHashNamespace!==null && $passHashClass!==null) {
                      import($passHashNamespace, $passHashClass);
                      $PasswordHashProviderList[] = array($passHashNamespace, $passHashClass);
                  }
              }
              // multiple providers given
              else {
                  foreach($providerSectionNames as $subSection) {
                      $passHashNamespace = $passwordHashProvider->getSection($subSection)->getValue('Namespace');
                      $passHashClass = $passwordHashProvider->getSection($subSection)->getValue('Class');
                      if($passHashNamespace!==null && $passHashClass!==null) {
                          import($passHashNamespace, $passHashClass);
                          $PasswordHashProviderList[] = array($passHashNamespace, $passHashClass);
                      }
                  }
              }
              
          }

          if(count($PasswordHashProviderList) === 0) {
              //fallback to default provider
              import($passHashNamespace, $passHashClass);
              $PasswordHashProviderList[] = array('modules::usermanagement::biz::provider::crypt', 'CryptHardcodedSaltPasswordHashProvider');
          }
          
           $this->passwordHashProvidersAreImported = true;
          
      }
         
      if ($this->isInitialized === false) {

         // setup the component
         $config = $this->getConfiguration('modules::usermanagement', 'umgtconfig.ini');
         $section = $config->getSection($initParam);

         $appId = $section->getValue('ApplicationID');
         if ($appId !== null) {
            $this->applicationId = $appId;
         }

         $serviceMode = $section->getValue('ServiceMode');
         if ($serviceMode !== null) {
            $this->gormServiceMode = $serviceMode;
         }

         $this->connectionKey = $section->getValue('ConnectionKey');

         
         // initialize the password hash providers
         foreach($PasswordHashProviderList as $ProviderInfo){
             $passwordHashProviderObject = $this->getAndInitServiceObject($ProviderInfo[0], $ProviderInfo[1], $initParam);
             $this->passwordHashProviders[] = $passwordHashProviderObject;
             unset($passwordHashProviderObject);
         }
         
         // set to initialized
         $this->isInitialized = true;
      }
   }
   
   /**
    * @public
    * 
    * When serialising in session, passwordhashproviders need to be imported
    * to avoid incomplete object bug.
    * 
    * @author Ralf Schubert
    * @version
    * Version 0.1, 14.07.2011 <br />
    */
   public function __wakeup() {
       $this->passwordHashProvidersAreImported = false;
   }

   /**
    * @protected
    *
    * Implements the comparing of stored hash with given password,
    * supporting fallback hash-providers and on-the-fly updating
    * of hashes in database to new providers.
    *
    * @param string $password the password to hash
    * @param GenericORMapperDataObject $user current user.
    * @return bool Returns true if password matches.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 21.06.2011 <br />
    */
    public function comparePasswordHash($password, GenericORMapperDataObject &$user) {
        // check if current default hash provider matches
        $defaultHashedPassword = $this->createPasswordHash($password, $user);
        if($user->getProperty('Password') === $defaultHashedPassword){
            return true;
        }
        
        // if there is no fallback provider, password didn't match
        if(count($this->passwordHashProviders) === 1) {
            return false;
        }
        
        // check each fallback, but skip default provider
        $firstSkipped = false;
        foreach($this->passwordHashProviders as $passwordHashProvider) {
            if(!$firstSkipped) {
                $firstSkipped = true;
                continue;
            }
            $hashedPassword = $passwordHashProvider->createPasswordHash($password, $this->getDynamicSalt($user));
            if($user->getProperty('Password') === $hashedPassword){
                // if fallback matched, first update hash in database to new provider (on-the-fly updating to new provider)
                $user->setProperty('Password', $password);
                $this->saveUser($user);
                return true;
            }
        }
        
        // no fallback matched.
        return false;
    }

    /**
     * @protected
     *
     * Implements the central dynamic salt method. If you desire to use another
     * dynamic salt, extend the UmgtManager and reimplement this method! Be sure,
     * to keep all other methods untouched.
     *
     * @param GenericORMapperDataObject $user Current user
     * @return string The dynamic salt
     *
     * @author Tobias Lückel
     * @version
     * Version 0.1, 05.04.2011<br />
     */
    public function getDynamicSalt(GenericORMapperDataObject &$user) {
        
        $dynamicSalt = $user->getProperty('DynamicSalt');
        $dynamicSalt = ($dynamicSalt === null) ? '' : trim($dynamicSalt);
        
        if($dynamicSalt === '') {
            $dynamicSalt = md5(rand(10000,99999));
            $user->setProperty('DynamicSalt', $dynamicSalt);
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
     * @param string $password the password to hash
     * @param GenericORMapperDataObject $user current user.
     * @return string The desired hash of the given password.
     *
     * @author Tobias Lückel
     * @version
     * Version 0.1, 21.06.2011<br />
     */
    public function createPasswordHash($password, GenericORMapperDataObject &$user) {
        return $this->passwordHashProviders[0]->createPasswordHash($password, $this->getDynamicSalt($user));
    }
    
    
    
   /**
    * @protected
    *
    * Returns an initialized Application object.
    *
    * @return GenericORMapperDataObject Ccurrent application domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    */
   protected function getCurrentApplication() {
      $app = new GenericDomainObject('Application');
      $app->setProperty('ApplicationID', $this->applicationId);
      return $app;
   }

   /**
    * @public
    *
    * Returns the configured instance of the generic o/r mapper the usermanagement
    * business component is currently using.
    * <p/>
    * This can be used to directly query the usermanagement database in cases, the
    * UmgtManager is missing a special feature.
    *
    * @return GenericORRelationMapper Instance of the generic or relation mapper.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2008<br />
    * Version 0.2, 16.03.2010 (Bugfix 299: moved the service type to the GORM factory call)<br />
    */
   public function &getORMapper() {
       return $this->getServiceObject(
                      'modules::genericormapper::data',
                      'GenericORMapperFactory',
                      $this->gormServiceMode)
              ->getGenericORMapper(
                      'modules::usermanagement',
                      'umgt',
                      $this->connectionKey
      );
   }

   /**
    * @public
    *
    * Saves a user object within the current application.
    *
    * @param GenericORMapperDataObject $user current user.
    * @return int The id of the user.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    * Version 0.2, 23.06.2009 (Introduced a generic possibility to create the display name.)<br />
    * Version 0.3, 20.09.2009 (Bugfix for bug 202. Password was hased twice on update.)<br />
    * Version 0.4, 27.09.2009 (Bugfix for bug related to 202. Password for new user was not hashed.)<br />
    */
   public function saveUser(GenericORMapperDataObject &$user) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // check, whether user is an existing user, and yes, resolve the
      // password conflict, described under http://forum.adventure-php-framework.org/de/viewtopic.php?f=8&t=202
      $userId = $user->getProperty('UserID');
      $password = $user->getProperty('Password');
      if ($userId !== null && $password !== null) {

         $storedUser = $oRM->loadObjectByID('User', $userId);

         // In case, the stored password is different to the current one,
         // hash the password. In all other cases, the password would be
         // hashed twice!
         if ($storedUser->getProperty('Password') != $password) {
            $user->setProperty(
                    'Password',
                    $this->createPasswordHash($password, $user)
            );
         } else {
            $user->deleteProperty('Password');
         }

      } else {
         // only create password for not empty strings!
         if (!empty($password)) {
            $user->setProperty(
                    'Password',
                    $this->createPasswordHash($password, $user)
            );
         }
      }

      // set display name
      $user->setProperty('DisplayName', $this->getDisplayName($user));

      // save the user and return it's id
      $app = $this->getCurrentApplication();
      $user->addRelatedObject('Application2User', $app);
      return $oRM->saveObject($user);

   }

   /**
    * @public
    *
    * Saves an application object.
    *
    * @param GenericORMapperDataObject $app The application object to save.
    * @return int The id of the application.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.07.2010<br />
    */
   public function saveApplication(GenericORMapperDataObject &$app) {
      return $this->getORMapper()->saveObject($app);
   }

   /**
    * @public
    *
    * Saves a group object within the current application.
    *
    * @param GenericORMapperDataObject $group current group.
    * @return int The id of the group.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    */
   public function saveGroup(GenericORMapperDataObject &$group) {
      $oRM = &$this->getORMapper();
      $app = $this->getCurrentApplication();
      $group->addRelatedObject('Application2Group', $app);
      // save the group and return it's id
      return $oRM->saveObject($group);
   }

   /**
    * @public
    *
    * Saves a role object within the current application.
    *
    * @param GenericORMapperDataObject $role current role.
    * @return int The id of the role.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    */
   public function saveRole(GenericORMapperDataObject &$role) {
      $oRM = &$this->getORMapper();
      $app = $this->getCurrentApplication();
      $role->addRelatedObject('Application2Role', $app);
      // save the group and return it's id
      return $oRM->saveObject($role);
   }

   /**
    * @public
    *
    * Saves a permission set object within the current application.
    *
    * @param GenericORMapperDataObject $permissionSet a permission set.
    * @return int The id of the permission set.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    * Version 0.2, 28.12.2008 (Bugfix: unnecessary associations are now deleted)<br />
    */
   public function savePermissionSet(GenericORMapperDataObject &$permissionSet) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // compose the permission set under the application
      $app = $this->getCurrentApplication();
      $permissionSet->addRelatedObject('Application2PermissionSet', $app);

      // check for deleted associations
      $permissions = &$permissionSet->getRelatedObjects('PermissionSet2Permission');
      $permissionIDs = array();
      for ($i = 0; $i < count($permissions); $i++) {
         $permissionIDs[] = $permissions[$i]->getProperty('PermissionID');
      }

      // delete the unnecessary relations
      $allPermissions = $this->loadPermissionList();
      for ($i = 0; $i < count($allPermissions); $i++) {
         if (!in_array($allPermissions[$i]->getProperty('PermissionID'), $permissionIDs)) {
            $oRM->deleteAssociation('PermissionSet2Permission', $permissionSet, $allPermissions[$i]);
         }
      }

      // save the permission set and return it's id
      return $oRM->saveObject($permissionSet);

   }

   /**
    * @public
    *
    * Saves a permission object within the current application.
    *
    * @param GenericORMapperDataObject $permission the permission.
    * @return int The id of the permission.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    * Version 0.2, 16.06.2008 (The permission set is lazy loaded when not present)<br />
    * Version 0.3, 28.12.2008 (Changed the API concerning the new UML diagram)<br />
    */
   public function savePermission(GenericORMapperDataObject &$permission) {
      $oRM = &$this->getORMapper();
      $app = $this->getCurrentApplication();
      $permission->addRelatedObject('Application2Permission', $app);
      // save the permission and return it's id
      return $oRM->saveObject($permission);
   }

   /**
    * @public
    *
    * Returns a list of users concerning the current page.
    *
    * @return GenericORMapperDataObject[] List of users.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.06.2008<br />
    * Version 0.2, 17.06.2008 (introduced query over current application)<br />
    */
   public function getPagedUserList() {

      // initialize or mapper
      $ORM = &$this->getORMapper();

      // select by statement
      $select = 'SELECT ent_user.* FROM ent_user
                    INNER JOIN cmp_application2user ON ent_user.UserID = cmp_application2user.Target_UserID
                    INNER JOIN ent_application ON cmp_application2user.Source_ApplicationID = ent_application.ApplicationID
                    WHERE ent_application.ApplicationID = \'' . $this->applicationId . '\'
                    ORDER BY ent_user.LastName ASC, ent_user.FirstName ASC';
      return $ORM->loadObjectListByTextStatement('User', $select);

   }

   /**
    * @public
    *
    * Returns a list of groups concerning the current page.
    *
    * @return GenericORMapperDataObject[] List of groups.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   public function getPagedGroupList() {

      // get or mapper instance
      $oRM = &$this->getORMapper();

      // configure criterion object
      $crit = new GenericCriterionObject();
      $crit->addRelationIndicator('Application2Group', $this->getCurrentApplication());
      $crit->addOrderIndicator('DisplayName', 'ASC');

      // return list
      return $oRM->loadObjectListByCriterion('Group', $crit);

   }

   /**
    * @public
    *
    * Returns a list of roles concerning the current page.
    *
    * @return GenericORMapperDataObject[] List of roles.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function getPagedRoleList() {
      $ORM = &$this->getORMapper();
      $select = 'SELECT * FROM ent_role ORDER BY DisplayName ASC';
      return $ORM->loadObjectListByTextStatement('Role', $select);
   }

   /**
    * @public
    *
    * Returns a list of permission sets concerning the current page.
    *
    * @return GenericORMapperDataObject[] List of permission sets.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function getPagedPermissionSetList() {
      $oRM = &$this->getORMapper();
      $select = 'SELECT * FROM ent_permissionset ORDER BY DisplayName ASC';
      return $oRM->loadObjectListByTextStatement('PermissionSet', $select);
   }

   /**
    * @public
    *
    * Returns a list of permissions concerning the current page.
    *
    * @return GenericORMapperDataObject[] List of permissions.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function getPagedPermissionList() {
      $oRM = &$this->getORMapper();
      $select = 'SELECT * FROM ent_permission ORDER BY DisplayName ASC';
      return $oRM->loadObjectListByTextStatement('Permission', $select);
   }

   /**
    * @public
    *
    * Returns a user domain object.
    *
    * @param int $userId id of the desired user
    * @return GenericORMapperDataObject[] The user domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function loadUserByID($userId) {
      $oRM = &$this->getORMapper();
      return $oRM->loadObjectByID('User', $userId);
   }

   /**
    * @public
    *
    * Returns a user domain object by it'd username and password.
    *
    * @param string $username the user's username.
    * @param string $password the user's password.
    * @return GenericORMapperDataObject The user domain object or null.
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
      if($userObject === null || !$this->comparePasswordHash($password, $userObject)) {
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
    * @return GenericORMapperDataObject The user domain object or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2009<br />
    */
   public function loadUserByFirstName($firstName) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // escape the input values
      $dbDriver = &$oRM->getDBDriver();
      $firstName = $dbDriver->escapeValue($firstName);

      // create the statement and select user
      $select = 'SELECT * FROM ent_user WHERE FirstName = \'' . $firstName . '\';';
      return $oRM->loadObjectByTextStatement('User', $select);

   }

   /**
    * @public
    *
    * Loads a user object by a given last name.
    *
    * @param string $lastName The last name of the user to load.
    * @return GenericORMapperDataObject The user domain object or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2009<br />
    */
   public function loadUserByLastName($lastName) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // escape the input values
      $dbDriver = &$oRM->getDBDriver();
      $lastName = $dbDriver->escapeValue($lastName);

      // create the statement and select user
      $select = 'SELECT * FROM ent_user WHERE LastName = \'' . $lastName . '\';';
      return $oRM->loadObjectByTextStatement('User', $select);

   }

   /**
    * @public
    *
    * Loads a user object by a given email.
    *
    * @param string $email The email of the user to load.
    * @return GenericORMapperDataObject The user domain object or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2009<br />
    */
   public function loadUserByEMail($email) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // escape the input values
      $dbDriver = &$oRM->getDBDriver();
      $email = $dbDriver->escapeValue($email);

      // create the statement and select user
      $select = 'SELECT * FROM ent_user WHERE EMail = \'' . $email . '\';';
      return $oRM->loadObjectByTextStatement('User', $select);

   }

   /**
    * @public
    *
    * Loads a user object by a first and last name.
    *
    * @param string $firstName The first name of the user to load.
    * @param string $lastName The last name of the user to load.
    * @return GenericORMapperDataObject The user domain object or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2009<br />
    */
   public function loadUserByFirstNameAndLastName($firstName, $lastName) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // escape the input values
      $dbDriver = &$oRM->getDBDriver();
      $firstName = $dbDriver->escapeValue($firstName);
      $lastName = $dbDriver->escapeValue($lastName);

      // create the statement and select user
      $select = 'SELECT * FROM ent_user WHERE FirstName = \'' . $firstName . '\' AND LastName = \'' . $lastName . '\';';
      return $oRM->loadObjectByTextStatement('User', $select);

   }

   /**
    * @public
    *
    * Loads a user object by a user name.
    *
    * @param string $username The user name of the user to load.
    * @return GenericORMapperDataObject The user domain object or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2009<br />
    */
   public function loadUserByUserName($username) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // escape the input values
      $dbDriver = &$oRM->getDBDriver();
      $username = $dbDriver->escapeValue($username);

      // create the statement and select user
      $select = 'SELECT * FROM ent_user WHERE Username = \'' . $username . '\';';
      return $oRM->loadObjectByTextStatement('User', $select);

   }

   /**
    * @protected
    *
    * Implements the central method to create the display name of a user object. If you desire
    * to use another algo, extend the UmgtManager and reimplement this method! Be sure, to keep
    * all other methods untouched.
    *
    * @param GenericORMapperDataObject $user The user object to save.
    * @return string The desired display name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.06.2009<br />
    */
   protected function getDisplayName(GenericORMapperDataObject $user) {
      $displayName = $user->getProperty('DisplayName');
      return empty($displayName) ? $user->getProperty('LastName') . ', ' . $user->getProperty('FirstName') : $user->getProperty('DisplayName');
   }

   /**
    * @public
    *
    * Returns a user domain object by it'd email and password.
    *
    * @param string $email the user's email
    * @param string $password the user's password
    * @return GenericORMapperDataObject The user domain object or null
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    * Version 0.2, 02.01.2009 (Added sql injection security)<br />
    * Version 0.3, 31.01.2009 (Switched to the private hashing method)<br />
    */
   public function loadUserByEMailAndPassword($email, $password) {

      $userObject = $this->loadUserByEMail($email);
      if($userObject === null || !$this->comparePasswordHash($password, $userObject)) {
          return null;
      }
      
      return $userObject;

   }

   /**
    * @public
    *
    * Returns a list of Permission domain objects for the given user.
    *
    * @param GenericORMapperDataObject $user the user object
    * @return GenericORMapperDataObject[] $permissions the user's permissions
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    * Version 0.2, 02.01.2009 (Implemented the method)<br />
    */
   public function loadUserPermissions(GenericORMapperDataObject &$user) {

      // build select statement
      $select = 'SELECT `ent_permission`.* FROM `ent_permission`
                    INNER JOIN ass_permissionset2permission ON ent_permission.PermissionID = ass_permissionset2permission.Target_PermissionID
                    INNER JOIN ent_permissionset ON ass_permissionset2permission.Source_PermissionSetID = ent_permissionset.PermissionSetID
                    INNER JOIN ass_role2permissionset ON ent_permissionset.PermissionSetID = ass_role2permissionset.Target_PermissionSetID
                    INNER JOIN ent_role ON ass_role2permissionset.Source_RoleID = ent_role.RoleID
                    INNER JOIN ass_role2user ON ent_role.RoleID = ass_role2user.Source_RoleID
                    INNER JOIN ent_user ON ass_role2user.Target_UserID = ent_user.UserID
                    WHERE ent_user.UserID = \'' . $user->getProperty('UserID') . '\'
                    GROUP BY `ent_permission`.`PermissionID`;';

      // load permissions
      $oRM = &$this->getORMapper();
      return $oRM->loadObjectListByTextStatement('Permission', $select);

   }

   /**
    * @public
    *
    * Returns a group domain object.
    *
    * @param int $groupID id of the desired group
    * @return GenericORMapperDataObject The group domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function loadGroupByID($groupID) {
      $oRM = &$this->getORMapper();
      return $oRM->loadObjectByID('Group', $groupID);
   }

   /**
    * @public
    *
    * Loads a group by a given name.
    *
    * @param string $groupName The name of the group to load
    * @return GenericCriterionObject The desired group domain object.
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
    * @param int $roleID id of the desired role
    * @return GenericORMapperDataObject[] The role domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function loadRoleByID($roleID) {
      $oRM = &$this->getORMapper();
      return $oRM->loadObjectByID('Role', $roleID);
   }

   /**
    * @public
    *
    * Loads a permission set by it's id.
    *
    * @param int $permissionSetID the permission set's id
    * @return GenericORMapperDataObject The permission set.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function loadPermissionSetByID($permissionSetID) {
      $oRM = &$this->getORMapper();
      return $oRM->loadObjectByID('PermissionSet', $permissionSetID);
   }

   /**
    * @public
    *
    * Loads a list of permissions of the current application.
    *
    * @return GenericORMapperDataObject[] The permission list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2008<br />
    */
   public function loadPermissionList() {

      // get the mapper
      $oRM = &$this->getORMapper();

      // setup the criterion
      $crit = new GenericCriterionObject();
      $crit->addRelationIndicator('Application2Permission', $this->getCurrentApplication());

      // load permission list
      return $oRM->loadObjectListByCriterion('Permission', $crit);

   }

   /**
    * @public
    *
    * Loads a permission by it's id.
    *
    * @param int $permID the permission's id
    * @return GenericORMapperDataObject The desiried permission.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2008<br />
    */
   public function loadPermissionByID($permID) {
      $oRM = &$this->getORMapper();
      return $oRM->loadObjectByID('Permission', $permID);
   }

   /**
    * @public
    *
    * Loads a list of roles, that are not associated with the permission set.
    *
    * @param GenericORMapperDataObject $permissionSet the desiried permission set
    * @return GenericORMapperDataObject[] The roles, that are not associated.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function loadRolesNotWithPermissionSet(GenericORMapperDataObject $permissionSet) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // setup driterion
      $crit = new GenericCriterionObject();
      $crit->addRelationIndicator('Application2Role', $this->getCurrentApplication());

      // load roles, that are not associated
      return $oRM->loadNotRelatedObjects($permissionSet, 'Role2PermissionSet', $crit);

   }

   /**
    * @public
    *
    * Loads a list of roles, that are associated with the permission set.
    *
    * @param GenericORMapperDataObject $permissionSet the desiried permission set
    * @return GenericORMapperDataObject[] The roles, that are associated.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function loadRolesWithPermissionSet(GenericORMapperDataObject $permissionSet) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // setup driterion
      $crit = new GenericCriterionObject();
      $crit->addRelationIndicator('Application2Role', $this->getCurrentApplication());

      // load roles, that are not associated
      return $oRM->loadRelatedObjects($permissionSet, 'Role2PermissionSet', $crit);

   }

   /**
    * @public
    *
    * Adds a permission to the given permission set.
    *
    * @param GenericORMapperDataObject $permission The permission to assign to the permission set.
    * @param GenericORMapperDataObject $permissionSet The desired permission set to add the permission to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.07.2010<br />
    */
   public function assignPermission2PermissionSet(GenericORMapperDataObject $permission, GenericORMapperDataObject $permissionSet) {
      $this->getORMapper()->createAssociation('PermissionSet2Permission', $permission, $permissionSet);
   }

   /**
    * @public
    *
    * Removes a permission from a given permission set.
    *
    * @param GenericORMapperDataObject $permission The permission to remove from the permission set.
    * @param GenericORMapperDataObject $permissionSet The desired permission set fremove the permission from.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.07.2010<br />
    */
   public function detachPermission2PermissionSet(GenericORMapperDataObject $permission, GenericORMapperDataObject $permissionSet) {
      $this->getORMapper()->deleteAssociation('PermissionSet2Permission', $permission, $permissionSet);
   }

   /**
    * @public
    *
    * Associates a given permission set to a list of roles.
    *
    * @param GenericORMapperDataObject $permissionSet the desiried permission set
    * @param GenericORMapperDataObject[] $roles the roles, that have to be associated
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function assignPermissionSet2Roles(GenericORMapperDataObject $permissionSet, array $roles) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // create the associations
      for ($i = 0; $i < count($roles); $i++) {
         $oRM->createAssociation('Role2PermissionSet', $roles[$i], $permissionSet);
      }

   }

   /**
    * @public
    *
    * Removes a given permission set from a list of roles.
    *
    * @param GenericORMapperDataObject $permissionSet the desiried permission set
    * @param GenericORMapperDataObject[] $roles the roles, that have to be associated
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function detachPermissionSetFromRoles(GenericORMapperDataObject $permissionSet, array $roles) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // delete the associations
      for ($i = 0; $i < count($roles); $i++) {
         $oRM->deleteAssociation('Role2PermissionSet', $roles[$i], $permissionSet);
      }

   }

   /**
    * @public
    *
    * Deletes a user.
    *
    * @param GenericORMapperDataObject[] $user the user to delete
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function deleteUser(GenericORMapperDataObject $user) {
      $oRM = &$this->getORMapper();
      $oRM->deleteObject($user);
   }

   /**
    * @public
    *
    * Deletes a group.
    *
    * @param GenericORMapperDataObject[] $group the group to delete
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function deleteGroup(GenericORMapperDataObject $group) {
      $oRM = &$this->getORMapper();
      $oRM->deleteObject($group);
   }

   /**
    *  @public
    *
    *  Deletes a role.
    *
    *  @param GenericORMapperDataObject[] $role the role to delete
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 29.12.2008<br />
    */
   public function deleteRole(GenericORMapperDataObject $role) {
      $oRM = &$this->getORMapper();
      $oRM->deleteObject($role);
   }

   /**
    *  @public
    *
    *  Deletes a PermissionSet.
    *
    *  @param GenericORMapperDataObject $permissionSet the permission set
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 28.12.2008<br />
    */
   public function deletePermissionSet(GenericORMapperDataObject $permissionSet) {
      $oRM = &$this->getORMapper();
      $oRM->deleteObject($permissionSet);
   }

   /**
    *  @public
    *
    *  Deletes a Permission.
    *
    *  @param GenericORMapperDataObject $permission the permission
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 28.12.2008<br />
    */
   public function deletePermission(GenericORMapperDataObject $permission) {
      $oRM = &$this->getORMapper();
      $oRM->deleteObject($permission);
   }

   /**
    *  @public
    *
    *  Associates a user with a list of groups.
    *
    *  @param GenericORMapperDataObject $user the user
    *  @param GenericORMapperDataObject[] $groups the group list
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 28.12.2008<br />
    */
   public function assignUser2Groups(GenericORMapperDataObject $user, array $groups) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // create the association
      for ($i = 0; $i < count($groups); $i++) {
         $oRM->createAssociation('Group2User', $user, $groups[$i]);
      }

   }

   /**
    *  @public
    *
    *  Associates users with a group.
    *
    *  @param GenericORMapperDataObject[] $users the user list
    *  @param GenericORMapperDataObject $group the group
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 28.12.2008<br />
    *  Version 0.2, 18.02.2009 (Bugfix: addUser2Groups() does not exist)<br />
    */
   public function assignUsers2Group(array $users, GenericORMapperDataObject $group) {

      for ($i = 0; $i < count($users); $i++) {
         $this->assignUser2Groups($users[$i], array($group));
      }

   }

   /**
    * @public
    *
    * Associates a role with a list of users.
    *
    * @param GenericORMapperDataObject $role the role
    * @param GenericORMapperDataObject[] $users the user list
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function assignRole2Users(GenericORMapperDataObject $role, array $users) {

      // get the mapper
      $orm = &$this->getORMapper();

      // create the association
      for ($i = 0; $i < count($users); $i++) {
         $orm->createAssociation('Role2User', $role, $users[$i]);
      }

   }

   /**
    * @public
    *
    * Loads all groups, that are assigned to a given user.
    *
    * @param GenericORMapperDataObject $user the user
    * @return GenericORMapperDataObject[] The group list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    * Version 02, 05.09.2009 (Now using the GORM to load the related objects, to allow serialized objects to be used as arguments)<br />
    */
   public function loadGroupsWithUser(GenericORMapperDataObject &$user) {
      $orm = &$this->getORMapper();
      return $orm->loadRelatedObjects($user, 'Group2User');
   }

   /**
    * @public
    *
    * Loads all groups, that are not assigned to a given user.
    *
    * @param GenericORMapperDataObject $user the user
    * @return GenericORMapperDataObject[] The group list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2008<br />
    */
   public function loadGroupsNotWithUser(GenericORMapperDataObject &$user) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // setup driterion
      $crit = new GenericCriterionObject();
      $crit->addRelationIndicator('Application2Group', $this->getCurrentApplication());

      // load roles, that are not associated
      return $oRM->loadNotRelatedObjects($user, 'Group2User', $crit);

   }

   /**
    *  @public
    *
    *  Loads all users, that are assigned to a given group.
    *
    *  @param GenericORMapperDataObject $group the group
    *  @return GenericORMapperDataObject[] The user list.
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 29.12.2008<br />
    *  Version 0.2, 30.12.2008 (Removed null pointer typo)<br />
    */
   public function loadUsersWithGroup(GenericORMapperDataObject &$group) {
      $oRM = &$this->getORMapper();
      return $oRM->loadRelatedObjects($group, 'Group2User');
   }

   /**
    *  @public
    *
    *  Loads all users, that are not assigned to a given group.
    *
    *  @param GenericORMapperDataObject $group the group
    *  @return GenericORMapperDataObject[] The user list.
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 27.12.2008<br />
    */
   public function loadUsersNotWithGroup(GenericORMapperDataObject &$group) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // setup the criterion
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2User', $app);

      // load the user list
      return $oRM->loadNotRelatedObjects($group, 'Group2User', $crit);

   }

   /**
    *  @public
    *
    *  Loads all roles, that are assigned to a given user.
    *
    *  @param GenericORMapperDataObject $user the user
    *  @return GenericORMapperDataObject[] The role list.
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 27.12.2008<br />
    */
   public function loadRolesWithUser(GenericORMapperDataObject &$user) {
      return $user->loadRelatedObjects('Role2User');
   }

   /**
    *  @public
    *
    *  Loads all roles, that are not assigned to a given user.
    *
    *  @param GenericORMapperDataObject $user the user
    *  @return GenericORMapperDataObject[] The role list.
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 27.12.2008<br />
    */
   public function loadRolesNotWithUser(GenericORMapperDataObject &$user) {

      // get the mapper
      $oRM = &$this->getORMapper();

      // setup the criterion
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2User', $app);

      // load the user list
      return $oRM->loadNotRelatedObjects($user, 'Role2User', $crit);

   }

   /**
    *  @public
    *
    *  Loads a list of users, that have a certail role.
    *
    *  @param GenericORMapperDataObject $role the role, the users should have
    *  @return GenericORMapperDataObject[] Desired user list.
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 28.12.2008<br />
    */
   public function loadUsersWithRole(GenericORMapperDataObject &$role) {
      return $role->loadRelatedObjects('Role2User');
   }

   /**
    *  @public
    *
    *  Loads a list of users, that don't have the given role.
    *
    *  @param GenericORMapperDataObject $role the role, the users should not have
    *  @return GenericORMapperDataObject[] Desired user list.
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 27.12.2008<br />
    *  Version 0.2, 28.12.2008 (Bugfix: criterion definition contained wrong relation indicator)<br />
    */
   public function loadUsersNotWithRole(GenericORMapperDataObject &$role) {

      $oRM = &$this->getORMapper();
      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2User', $app);
      return $oRM->loadNotRelatedObjects($role, 'Role2User', $crit);

   }

   /**
    *  @public
    *
    *  Loads the permissions associated with a permission set.
    *
    *  @param GenericORMapperDataObject $permissionSet the permission set
    *  @return GenericORMapperDataObject[] The list of permissions.
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 28.12.2008<br />
    */
   public function loadPermissionsOfPermissionSet(GenericORMapperDataObject &$permissionSet) {
      $oRM = &$this->getORMapper();
      return $oRM->loadRelatedObjects($permissionSet, 'PermissionSet2Permission');
   }

   /**
    *  @public
    *
    *  Detaches a user from a role.
    *
    *  @param GenericORMapperDataObject $user the user
    *  @param GenericORMapperDataObject $role the desired role to detach the user from
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 28.12.2008<br />
    */
   public function detachUserFromRole(GenericORMapperDataObject $user, GenericORMapperDataObject $role) {
      $oRM = &$this->getORMapper();
      $oRM->deleteAssociation('Role2User', $role, $user);
   }

   /**
    *  @public
    *
    *  Detaches users from a role.
    *
    *  @param GenericORMapperDataObject[] $users a list of users
    *  @param GenericORMapperDataObject $role the desired role to detach the users from
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 28.12.2008<br />
    */
   public function detachUsersFromRole(array $users, GenericORMapperDataObject $role) {

      for ($i = 0; $i < count($users); $i++) {
         $this->detachUserFromRole($users[$i], $role);
      }

   }

   /**
    *  @public
    *
    *  Removes a user from the given groups.
    *
    *  @param GenericORMapperDataObject $user the desired user
    *  @param GenericORMapperDataObject $group the group
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 26.12.2008<br />
    */
   public function detachUserFromGroup(GenericORMapperDataObject $user, GenericORMapperDataObject $group) {
      $oRM = &$this->getORMapper();
      $oRM->deleteAssociation('Group2User', $user, $group);
   }

   /**
    *  @public
    *
    *  Removes a user from the given groups.
    *
    *  @param GenericORMapperDataObject $user the desired user
    *  @param GenericORMapperDataObject[] $groups a list of groups
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 26.12.2008<br />
    */
   public function detachUserFromGroups(GenericORMapperDataObject $user, array $groups) {

      for ($i = 0; $i < count($groups); $i++) {
         $this->detachUserFromGroup($user, $groups[$i]);
      }

   }

   /**
    * @public
    *
    * Removes users from a given group.
    *
    * @param GenericORMapperDataObject[] $users a list of users
    * @param GenericORMapperDataObject $group the desired group
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.12.2008<br />
    */
   public function detachUsersFromGroup(array $users, GenericORMapperDataObject $group) {

      for ($i = 0; $i < count($users); $i++) {
         $this->detachUserFromGroup($users[$i], $group);
      }

   }

   /**
    * @public
    *
    * Creates a visibility definition relating an application proxy object to the users and
    * groups, that should have access to the object.
    *
    * @param GenericORMapperDataObject $visibilityType The visibility type (object type of application's the target object).
    * @param GenericORMapperDataObject $visibilityDefinition The visibility definition (object id of application's the target object).
    * @param GenericORMapperDataObject[] $users The list of users, that should have visibility permissions on the given application object.
    * @param GenericORMapperDataObject[] $groups The list of groups, that should have visibility permissions on the given application object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.05.2010<br />
    */
   public function createVisibilityDefinition(GenericORMapperDataObject $visibilityType, GenericORMapperDataObject $visibilityDefinition, $users = array(), $groups = array()) {

      $orm = &$this->getORMapper();

      // try to reuse existing visibility definitions having the same
      // combination of proxy + type!
      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('AppObjectId', $visibilityDefinition->getProperty('AppObjectId'));
      $crit->addRelationIndicator('AppProxy2AppProxyType', $visibilityType);
      $storedVisibilityDefinition = $orm->loadObjectByCriterion('AppProxy', $crit);
      if ($storedVisibilityDefinition != null) {
         $visibilityDefinition = $storedVisibilityDefinition;
      }

      // append proxy to current application
      $app = $this->getCurrentApplication();
      $visibilityDefinition->addRelatedObject('Application2AppProxy', $app);

      // create domain structure
      $visibilityDefinition->addRelatedObject('AppProxy2AppProxyType', $visibilityType);

      foreach ($users as $id => $DUMMY) {
         $visibilityDefinition->addRelatedObject('AppProxy2User', $users[$id]);
      }
      foreach ($groups as $id => $group) {
         $visibilityDefinition->addRelatedObject('AppProxy2Group', $groups[$id]);
      }

      // save domain structure
      return $orm->saveObject($visibilityDefinition);
   }

   /**
    * @public
    *
    * Deletes a visibility definition including all associations.
    *
    * @param GenericORMapperDataObject $visibilityDefinition The visibility definition to delete.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.06.2010<br />
    */
   public function deleteVisibilityDefinition(GenericORMapperDataObject $visibilityDefinition) {
      $this->getORMapper()->deleteObject($visibilityDefinition);
   }

   /**
    * @public
    *
    * Revokes access of the passed users to the given application proxy object.
    *
    * @param GenericORMapperDataObject $visibilityDefinition The application proxy object.
    * @param GenericORMapperDataObject[] $users A list of users, that should be revoked access to the given application proxy.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function detachUsersFromVisibilityDefinition(GenericORMapperDataObject $visibilityDefinition, array $users) {
      $orm = &$this->getORMapper();
      foreach ($users as $user) {
         $orm->deleteAssociation('AppProxy2User', $visibilityDefinition, $user);
      }
   }

   /**
    * @public
    *
    * Revokes access of the passed groups to the given application proxy object.
    *
    * @param GenericORMapperDataObject $visibilityDefinition The application proxy object.
    * @param GenericORMapperDataObject[] $groups A list of groups, that should be revoked access to the given application proxy.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function detachGroupsFromVisibilityDefinition(GenericORMapperDataObject $visibilityDefinition, array $groups) {
      $orm = &$this->getORMapper();
      foreach ($groups as $group) {
         $orm->deleteAssociation('AppProxy2Group', $visibilityDefinition, $group);
      }
   }

   /**
    * @public
    *
    * Adds a given list of users to the applied visibility definition.
    *
    * @param GenericORMapperDataObject $visibilityDefinition The desired visibility definition.
    * @param GenericORMapperDataObject[] $users The list of users to detach.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function attachUsers2VisibilityDefinition(GenericORMapperDataObject $visibilityDefinition, array $users) {
      $orm = &$this->getORMapper();
      foreach ($users as $user) {
         $orm->createAssociation('AppProxy2User', $visibilityDefinition, $user);
      }
   }

   /**
    * @public
    *
    * Adds a given list of groups to the applied visibility definition.
    *
    * @param GenericORMapperDataObject $visibilityDefinition The desired visibility definition.
    * @param GenericORMapperDataObject[] $groups The list of users to detach.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function attachGroups2VisibilityDefinition(GenericORMapperDataObject $visibilityDefinition, array $groups) {
      $orm = &$this->getORMapper();
      foreach ($groups as $group) {
         $orm->createAssociation('AppProxy2Group', $visibilityDefinition, $group);
      }
   }

   /**
    * @public
    *
    * Returns a list of all visibility definitions for the current application.
    *
    * @param GenericORMapperDataObject $type An optional visibility definitioyn type marker to limit the result.
    * @return GenericORMapperDataObject[] The list of visibility definitions for the current application.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    * Version 0.2, 01.11.2010 (Added type restriction possibility)<br />
    */
   public function getPagedVisibilityDefinitionList(GenericORMapperDataObject $type = null) {
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
    * @param GenericORMapperDataObject $proxy The proxy object.
    * @return GenericORMapperDataObject[] The list of users, that have visibility permission.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.05.2010<br />
    */
   public function loadUsersWithVisibilityDefinition(GenericORMapperDataObject $proxy) {
      return $this->getORMapper()->loadRelatedObjects($proxy, 'AppProxy2User');
   }

   /**
    * @public
    *
    * Loads the list of groups, that have visibility permissions on the given proxy object.
    *
    * @param GenericORMapperDataObject $proxy The proxy object.
    * @return GenericORMapperDataObject[] The list of groups, that have visibility permission.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.05.2010<br />
    */
   public function loadGroupsWithVisibilityDefinition(GenericORMapperDataObject $proxy) {
      return $this->getORMapper()->loadRelatedObjects($proxy, 'AppProxy2Group');
   }

   /**
    * @public
    *
    * Loads a mixed list of users and groups, that have access to a given proxy.
    * Sorts the result according to the display name of the user and group.
    *
    * @param GenericORMapperDataObject $proxy The proxy the users and groups have access to.
    * @return GenericORMapperDataObject[] A mixed list of users and groups, that have access to a given proxy.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.06.2010<br />
    */
   public function loadUsersAndGroupsWithVisibilityDefinition(GenericORMapperDataObject $proxy) {
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
    * @param GenericORMapperDataObject $visibilityDefinition The appropriate visibility definition.
    * @return GenericORMapperDataObject[] The users, that do not have visibility permissions in the given object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.06.2010<br />
    */
   public function loadUsersNotWithVisibilityPermissions(GenericORMapperDataObject $visibilityDefinition) {

      $orm = &$this->getORMapper();

      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2User', $app);

      return $orm->loadNotRelatedObjects($visibilityDefinition, 'AppProxy2User', $crit);
   }

   /**
    * @public
    *
    * Loads the list of groups, that do not have visibility permissions on the given application proxy.
    *
    * @param GenericORMapperDataObject $visibilityDefinition The appropriate visibility definition.
    * @return GenericORMapperDataObject[] The groups, that do not have visibility permissions in the given object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.06.2010<br />
    */
   public function loadGroupsNotWithVisibilityPermissions(GenericORMapperDataObject $visibilityDefinition) {

      $orm = &$this->getORMapper();

      $crit = new GenericCriterionObject();
      $app = $this->getCurrentApplication();
      $crit->addRelationIndicator('Application2Group', $app);

      return $orm->loadNotRelatedObjects($visibilityDefinition, 'AppProxy2Group', $crit);
   }

   /**
    * @public
    *
    * Loads the list of visibility definitions for the given type.
    *
    * @param GenericORMapperDataObject $type The visibility definiton type.
    * @return GenericORMapperDataObject[] A list of visibility definitions of the given type.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.05.2010<br />
    */
   public function loadVisibilityDefinitionsByType(GenericORMapperDataObject $type) {
      return $this->getORMapper()->loadRelatedObjects($type, 'AppProxy2AppProxyType');
   }

   /**
    * @public
    *
    * Loads all visibility definitions for the current user and it's group restricted by the
    * given visibility definition type (e.g. <em>Page</em>).
    *
    * @param GenericORMapperDataObject $user The currently logged-in user.
    * @param GenericORMapperDataObject $type The type of visibility definition (e.g. <em>Page</em>)
    * @return GenericORMapperDataObject[] A list of visibility definitions, the user and it'd groups have access to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.06.2010<br />
    */
   public function loadAllVisibilityDefinitions(GenericORMapperDataObject $user, GenericORMapperDataObject $type) {

      $visDefs = $this->loadVisibilityDefinitionsByUser($user, $type);

      $groups = $this->loadGroupsWithUser($user);
      foreach ($groups as $id => $DUMMY) {
         $visDefs = array_merge($visDefs, $this->loadVisibilityDefinitionsByGroup($groups[$id], $type));
      }

      return array_unique($visDefs);
   }

   /**
    * @public
    *
    * Loads all visibility definition for the given user restricted by the
    * given visibility definition type (e.g. <em>Page</em>).
    *
    * @param GenericORMapperDataObject $user The currently logged-in user.
    * @param GenericORMapperDataObject $type The type of visibility definition (e.g. <em>Page</em>)
    * @return GenericORMapperDataObject[] A list of visibility definitions, the user has access to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.06.2010<br />
    */
   public function loadVisibilityDefinitionsByUser(GenericORMapperDataObject $user, GenericORMapperDataObject $type) {
      $crit = new GenericCriterionObject();
      $crit->addRelationIndicator('Application2AppProxy', $this->getCurrentApplication());
      $crit->addRelationIndicator('AppProxy2AppProxyType', $type);
      return $this->getORMapper()->loadRelatedObjects($user, 'AppProxy2User', $crit);
   }

   /**
    * @public
    *
    * Loads all visibility definition for the given group restricted by the
    * given visibility definition type (e.g. <em>Page</em>).
    *
    * @param GenericORMapperDataObject $group A desired group.
    * @param GenericORMapperDataObject $type The type of visibility definition (e.g. <em>Page</em>)
    * @return GenericORMapperDataObject[] A list of visibility definitions, the group has access to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.06.2010<br />
    */
   public function loadVisibilityDefinitionsByGroup(GenericORMapperDataObject $group, GenericORMapperDataObject $type) {
      $crit = new GenericCriterionObject();
      $crit->addRelationIndicator('Application2AppProxy', $this->getCurrentApplication());
      $crit->addRelationIndicator('AppProxy2AppProxyType', $type);
      return $this->getORMapper()->loadRelatedObjects($group, 'AppProxy2Group', $crit);
   }

   /**
    * @public
    *
    * Loads a visibility definition by it's object id.
    *
    * @param string $id The of the visibility definition.
    * @return GenericORMapperDataObject The desired visibility definition.
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
    * @param int $id The application object id of the visibility definition.
    * @return GenericORMapperDataObject The desired visibility definition.
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
    * @param GenericORMapperDataObject $proxyType The type to save.
    * @return int The id of the type.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.04.2010<br />
    */
   public function saveVisibilityDefinitionType(GenericORMapperDataObject &$proxyType) {
      $oRM = &$this->getORMapper();
      $app = $this->getCurrentApplication();
      $proxyType->addRelatedObject('Application2AppProxyType', $app);
      // save the permission and return it's id
      return $oRM->saveObject($proxyType);
   }

   /**
    * @public
    *
    * Recursively deletes the proxy type and it's decendants (proxies + associations).
    *
    * @param GenericORMapperDataObject $proxyType The proxy type to delete.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.05.2010<br />
    */
   public function deleteVisibilityDefinitionType(GenericORMapperDataObject &$proxyType) {
      $proxies = $this->loadVisibilityDefinitionsByType($proxyType);
      $orm = &$this->getORMapper();
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
    * @return GenericORMapperDataObject The desired proxy type.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.04.2010<br />
    */
   public function loadVisibilityDefinitionTypeById($id) {
      return $this->getORMapper()->loadObjectByID('AppProxyType', $id);
   }

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
    * @param GenericORMapperDataObject $proxy The proxy object to load the type of.
    * @return GenericORMapperDataObject The desired proxy type.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function loadVisibilityDefinitionType(GenericORMapperDataObject $proxy) {
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
    * @return GenericORMapperDataObject[] List of proxy types.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.04.2010<br />
    */
   public function loadVisibilityDefinitionTypes() {
      $crit = new GenericCriterionObject();
      $crit->addOrderIndicator('AppObjectName');
      $orm = &$this->getORMapper();
      return $orm->loadObjectListByCriterion('AppProxyType', $crit);
   }

}
?>