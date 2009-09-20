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

   /**
    * @package modules::usermanagement::biz
    * @module umgtManager
    *
    * Business component of the user management module. Uses the md5 algo to create password hashes.
    * If you desire to use another one, extend this class and overwrite the __createPasswordHash()
    * function with your own functionality. Please be sure to keep all the other methods untouched!
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.04.2008<br />
    * Version 0.2, 23.06.2008 (Mapper is now loaded by an internal method that uses the GenericORMapperFactory)<br />
    * Version 0.3, 31.01.2009 (Introduced the possibility to switch the hash algo)<br />
    */
   class umgtManager extends coreObject {

      /**
       * @protected
       * @var int Indicates the id of the current application/project.
       */
      protected $__ApplicationID = 1;

      /**
       * @protected
       * @var string Indicates the database connection key.
       */
      protected $__ConnectionKey = null;

      /**
       * @protected
       * @var string Defines the service mode of the generic or mapper.
       */
      protected $__ServiceMode = 'SESSIONSINGLETON';

      /**
       * @protected
       * @var boolean indicates, if the component is already initialized.
       */
      protected $__IsInitialized = false;

      public function umgtManager(){
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
      public function init($initParam){

         if($this->__IsInitialized === false){

            // setup the component
            $config = &$this->__getConfiguration('modules::usermanagement','umgtconfig');

            $appID = $config->getValue($initParam,'ApplicationID');
            if($appID !== null){
               $this->__ApplicationID = $appID;
             // end if
            }

            $serviceMode = $config->getValue($initParam,'ServiceMode');
            if($serviceMode !== null){
               $this->__ServiceMode = $serviceMode;
             // end if
            }
            $this->__ConnectionKey = $config->getValue($initParam,'ConnectionKey');

            // set to initialized
            $this->__IsInitialized = true;

          // end if
         }

       // end function
      }

      /**
       * @protected
       *
       * Implements the central hashing method. If you desire to use another hash algo, extend the
       * UmgtManager and reimplement this method! Be sure, to keep all other methods untouched.
       *
       * @param string $password the password to hash
       * @return string The desired hash of the given password.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 31.01.2009<br />
       */
      protected function __createPasswordHash($password){
         return md5($password);
       // end function
      }

      /**
       * @protected
       *
       * Returns an initialized Application object.
       *
       * @return GenericDomainObject Ccurrent application domain object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.06.2008<br />
       */
      protected function __getCurrentApplication(){
         $app = new GenericDomainObject('Application');
         $app->setProperty('ApplicationID',$this->__ApplicationID);
         return $app;
       // end function
      }

      /**
       * @protected
       *
       * Returns an initialized or mapper instance.
       *
       * @return GenericORRelationMapper Instance of the generic or relation mapper.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.06.2008<br />
       */
      protected function &__getORMapper(){
         $ORMFactory = &$this->__getServiceObject('modules::genericormapper::data','GenericORMapperFactory');
         return $ORMFactory->getGenericORMapper('modules::usermanagement','umgt',$this->__ConnectionKey,$this->__ServiceMode);
       // end function
      }

      /**
       * @public
       *
       * Saves a user object within the current application.
       *
       * @param GenericDomainObject $user current user.
       * @return int The id of the user.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.06.2008<br />
       * Version 0.2, 23.06.2009 (Introduced a generic possibility to create the display name.)<br />
       * Version 0.3, 20.09.2009 (Bugfix for bug 202. Password was hased twice on update.)<br />
       */
      public function saveUser($user){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // check, whether user is an existing user, and yes, resolve the
         // password conflict, described under http://forum.adventure-php-framework.org/de/viewtopic.php?f=8&t=202
         $userId = $user->getProperty('UserID');
         $password = $user->getProperty('Password');
         if($userId !== null && $password !== null){

            $storedUser = $oRM->loadObjectByID('User',$userId);

            // In case, the stored password is different to the current one,
            // hash the password. In all other cases, the password would be
            // hashed twice!
            if($storedUser->getProperty('Password') != $password){
               $user->setProperty('Password',$this->__createPasswordHash($user->getProperty('Password')));
            }
            else {
               $user->deleteProperty('Password');
            }

          // end if
         }

         // setup the composition
         $app = $this->__getCurrentApplication();
         $user->setProperty('DisplayName',$this->__getDisplayName($user));
         $app->addRelatedObject('Application2User',$user);

         return $oRM->saveObject($app);

       // end function
      }

      /**
       * @public
       *
       * Saves a group object within the current application.
       *
       * @param GenericDomainObject $group current group.
       * @return int The id of the group.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.06.2008<br />
       */
      public function saveGroup($group){
         $oRM = &$this->__getORMapper();
         $app = $this->__getCurrentApplication();
         $app->addRelatedObject('Application2Group',$group);
         return $oRM->saveObject($app);
       // end function
      }

      /**
       * @public
       *
       * Saves a role object within the current application.
       *
       * @param GenericDomainObject $role current role.
       * @return int The id of the role.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.06.2008<br />
       */
      public function saveRole($role){
         $oRM = &$this->__getORMapper();
         $app = $this->__getCurrentApplication();
         $app->addRelatedObject('Application2Role',$role);
         return $oRM->saveObject($app);
       // end function
      }

      /**
       * @public
       *
       * Saves a permission set object within the current application.
       *
       * @param GenericDomainObject $permissionSet a permission set.
       * @return int The id of the permission set.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.06.2008<br />
       * Version 0.2, 28.12.2008 (Bugfix: unnecessary associations are now deleted)<br />
       */
      public function savePermissionSet($permissionSet){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // compose the permission set under the application
         $app = $this->__getCurrentApplication();
         $app->addRelatedObject('Application2PermissionSet',$permissionSet);

         // check for deleted associations
         $permissions = &$permissionSet->getRelatedObjects('PermissionSet2Permission');
         $permissionIDs = array();
         for($i = 0; $i < count($permissions); $i++){
            $permissionIDs[] = $permissions[$i]->getProperty('PermissionID');
          // end for
         }

         // delete the unnecessary relations
         $allPermissions = $this->loadPermissionList();
         for($i = 0; $i < count($allPermissions); $i++){
            if(!in_array($allPermissions[$i]->getProperty('PermissionID'),$permissionIDs)){
               $oRM->deleteAssociation('PermissionSet2Permission',$permissionSet,$allPermissions[$i]);
             // end if
            }
          // end for
         }

         return $oRM->saveObject($app);

       // end function
      }

      /**
       * @public
       *
       * Saves a permission object within the current application.
       *
       * @param GenericDomainObject $permission the permission.
       * @return int The id of the permission.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.06.2008<br />
       * Version 0.2, 16.06.2008 (The permission set is lazy loaded when not present)<br />
       * Version 0.3, 28.12.2008 (Changed the API concerning the new UML diagram)<br />
       */
      public function savePermission($permission){

         // load generic or mapper
         $oRM = &$this->__getORMapper();

         // add permission to structure
         $app = $this->__getCurrentApplication();
         $app->addRelatedObject('Application2Permission',$permission);

         return $oRM->saveObject($app);

       // end function
      }

      /**
       * @public
       *
       * Returns a list of users concerning the current page.
       *
       * @return GenericDomainObject[] List of users.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.06.2008<br />
       * Version 0.2, 17.06.2008 (introduced query over current application)<br />
       */
      public function getPagedUserList(){

         // initialize or mapper
         $ORM = &$this->__getORMapper();

         // select by statement
         $select = 'SELECT ent_user.* FROM ent_user
                    INNER JOIN cmp_application2user ON ent_user.UserID = cmp_application2user.UserID
                    INNER JOIN ent_application ON cmp_application2user.ApplicationID = ent_application.ApplicationID
                    WHERE ent_application.ApplicationID = \''.$this->__ApplicationID.'\'
                    ORDER BY ent_user.LastName ASC, ent_user.FirstName ASC';
         return $ORM->loadObjectListByTextStatement('User', $select);

       // end function
      }

      /**
       * @public
       *
       * Returns a list of groups concerning the current page.
       *
       * @return GenericDomainObject[] List of groups.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 27.12.2008<br />
       */
      public function getPagedGroupList(){

         // get or mapper instance
         $oRM = &$this->__getORMapper();

         // configure criterion object
         $crit = new GenericCriterionObject();
         $crit->addRelationIndicator('Application2Group',$this->__getCurrentApplication());
         $crit->addOrderIndicator('DisplayName','ASC');

         // return list
         return $oRM->loadObjectListByCriterion('Group',$crit);

       // end function
      }

      /**
       * @public
       *
       * Returns a list of roles concerning the current page.
       *
       * @return GenericDomainObject[] List of roles.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function getPagedRoleList(){
         $ORM = &$this->__getORMapper();
         $select = 'SELECT * FROM ent_role ORDER BY DisplayName ASC';
         return $ORM->loadObjectListByTextStatement('Role',$select);
       // end function
      }

      /**
       * @public
       *
       * Returns a list of permission sets concerning the current page.
       *
       * @return GenericDomainObject[] List of permission sets.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function getPagedPermissionSetList(){
         $oRM = &$this->__getORMapper();
         $select = 'SELECT * FROM ent_permissionset ORDER BY DisplayName ASC';
         return $oRM->loadObjectListByTextStatement('PermissionSet',$select);
       // end function
      }

      /**
       * @public
       *
       * Returns a list of permissions concerning the current page.
       *
       * @return GenericDomainObject[] List of permissions.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function getPagedPermissionList(){
         $oRM = &$this->__getORMapper();
         $select = 'SELECT * FROM ent_permission ORDER BY DisplayName ASC';
         return $oRM->loadObjectListByTextStatement('Permission',$select);
       // end function
      }

      /**
       * @public
       *
       * Returns a user domain object.
       *
       * @param int $userID id of the desired user
       * @return GenericDomainObject[] The user domain object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function loadUserByID($userID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('User',$userID);
       // end function
      }

      /**
       * @public
       *
       * Returns a user domain object by it'd username and password.
       *
       * @param string $username the user's username.
       * @param string $password the user's password.
       * @return GenericDomainObject The user domain object or null.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.12.2008<br />
       * Version 0.2, 02.01.2009 (Added sql injection security)<br />
       * Version 0.3, 31.01.2009 (Switched to the private hashing method)<br />
       */
      public function loadUserByUsernameAndPassword($username,$password){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // escape the input values
         $dbDriver = &$oRM->getByReference('DBDriver');
         $username = $dbDriver->escapeValue($username);
         $password = $dbDriver->escapeValue($password);

         // create the statement and select user
         $password = $this->__createPasswordHash($password);
         $select = 'SELECT * FROM ent_user WHERE Username = \''.$username.'\' AND Password = \''.$password.'\';';
         return $oRM->loadObjectByTextStatement('User',$select);

       // end function
      }

      /**
       * @public
       *
       * Loads a user object by a given first name.
       *
       * @param string $firstName The first name of the user to load.
       * @return GenericDomainObject The user domain object or null.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.06.2009<br />
       */
      public function loadUserByFirstName($firstName){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // escape the input values
         $dbDriver = &$oRM->getByReference('DBDriver');
         $firstName = $dbDriver->escapeValue($firstName);

         // create the statement and select user
         $select = 'SELECT * FROM ent_user WHERE FirstName = \''.$firstName.'\';';
         return $oRM->loadObjectByTextStatement('User',$select);

       // end function
      }

      /**
       * @public
       *
       * Loads a user object by a given last name.
       *
       * @param string $lastName The last name of the user to load.
       * @return GenericDomainObject The user domain object or null.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.06.2009<br />
       */
      public function loadUserByLastName($lastName){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // escape the input values
         $dbDriver = &$oRM->getByReference('DBDriver');
         $lastName = $dbDriver->escapeValue($lastName);

         // create the statement and select user
         $select = 'SELECT * FROM ent_user WHERE LastName = \''.$lastName.'\';';
         return $oRM->loadObjectByTextStatement('User',$select);

       // end function
      }

      /**
       * @public
       *
       * Loads a user object by a given email.
       *
       * @param string $email The email of the user to load.
       * @return GenericDomainObject The user domain object or null.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.06.2009<br />
       */
      public function loadUserByEMail($email){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // escape the input values
         $dbDriver = &$oRM->getByReference('DBDriver');
         $email = $dbDriver->escapeValue($email);

         // create the statement and select user
         $select = 'SELECT * FROM ent_user WHERE EMail = \''.$email.'\';';
         return $oRM->loadObjectByTextStatement('User',$select);

       // end function
      }

      /**
       * @public
       *
       * Loads a user object by a first and last name.
       *
       * @param string $firstName The first name of the user to load.
       * @param string $lastName The last name of the user to load.
       * @return GenericDomainObject The user domain object or null.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.06.2009<br />
       */
      public function loadUserByFirstNameAndLastName($firstName,$lastName){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // escape the input values
         $dbDriver = &$oRM->getByReference('DBDriver');
         $firstName = $dbDriver->escapeValue($firstName);
         $lastName = $dbDriver->escapeValue($lastName);

         // create the statement and select user
         $select = 'SELECT * FROM ent_user WHERE FirstName = \''.$firstName.'\' AND LastName = \''.$lastName.'\';';
         return $oRM->loadObjectByTextStatement('User',$select);

       // end function
      }

      /**
       * @public
       *
       * Loads a user object by a user name.
       *
       * @param string $username The user name of the user to load.
       * @return GenericDomainObject The user domain object or null.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.06.2009<br />
       */
      public function loadUserByUserName($username){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // escape the input values
         $dbDriver = &$oRM->getByReference('DBDriver');
         $username = $dbDriver->escapeValue($username);

         // create the statement and select user
         $select = 'SELECT * FROM ent_user WHERE Username = \''.$username.'\';';
         return $oRM->loadObjectByTextStatement('User',$select);

       // end function
      }

      /**
       * @protected
       * 
       * Implements the central method to create the display name of a user object. If you desire
       * to use another algo, extend the UmgtManager and reimplement this method! Be sure, to keep
       * all other methods untouched.
       *
       * @param GenericDomainObject $user The user object to save.
       * @return string The desired display name.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.06.2009<br />
       */
      protected function __getDisplayName($user){
         return $user->getProperty('LastName').', '.$user->getProperty('FirstName');
       // end function
      }

      /**
       * @public
       *
       * Returns a user domain object by it'd email and password.
       *
       * @param string $email the user's email
       * @param string $password the user's password
       * @return GenericDomainObject The user domain object or null
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       * Version 0.2, 02.01.2009 (Added sql injection security)<br />
       * Version 0.3, 31.01.2009 (Switched to the private hashing method)<br />
       */
      public function loadUserByEMailAndPassword($email,$password){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // escape the input values
         $dbDriver = &$oRM->getByReference('DBDriver');
         $email = $dbDriver->escapeValue($email);
         $password = $dbDriver->escapeValue($password);

         // create the statenent and select user
         $password = $this->__createPasswordHash($password);
         $select = 'SELECT * FROM ent_user WHERE EMail = \''.$email.'\' AND Password = \''.$password.'\';';
         return $oRM->loadObjectByTextStatement('User',$select);

       // end function
      }

      /**
       * @public
       *
       * Returns a list of Permission domain objects for the given user.
       *
       * @param GenericDomainObject $user the user object
       * @return GenericDomainObject[] $permissions the user's permissions
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       * Version 0.2, 02.01.2009 (Implemented the method)<br />
       */
      public function loadUserPermissions(&$user){

         // build select statement
         $select = 'SELECT `ent_permission`.* FROM `ent_permission`
                    INNER JOIN ass_permissionset2permission ON ent_permission.PermissionID = ass_permissionset2permission.PermissionID
                    INNER JOIN ent_permissionset ON ass_permissionset2permission.PermissionSetID = ent_permissionset.PermissionSetID
                    INNER JOIN ass_role2permissionset ON ent_permissionset.PermissionSetID = ass_role2permissionset.PermissionSetID
                    INNER JOIN ent_role ON ass_role2permissionset.RoleID = ent_role.RoleID
                    INNER JOIN ass_role2user ON ent_role.RoleID = ass_role2user.RoleID
                    INNER JOIN ent_user ON ass_role2user.UserID = ent_user.UserID
                    WHERE ent_user.UserID = \''.$user->getProperty('UserID').'\'
                    GROUP BY `ent_permission`.`PermissionID`;';

         // load permissions
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectListByTextStatement('Permission',$select);

       // end function
      }

      /**
       * @public
       *
       * Returns a group domain object.
       *
       * @param int $groupID id of the desired group
       * @return GenericDomainObject[] The group domain object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function loadGroupByID($groupID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('Group',$groupID);
       // end function
      }

      /**
       * @public
       *
       * Returns a role domain object.
       *
       * @param int $roleID id of the desired role
       * @return GenericDomainObject[] The role domain object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function loadRoleByID($roleID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('Role',$roleID);
       // end function
      }

      /**
       * @public
       *
       * Loads a permission set by it's id.
       *
       * @param int $permissionSetID the permission set's id
       * @return GenericDomainObject The permission set.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function loadPermissionSetByID($permissionSetID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('PermissionSet',$permissionSetID);
       // end function
      }

      /**
       * @public
       *
       * Loads a list of permissions of the current application.
       *
       * @return GenericDomainObject[] The permission list.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.12.2008<br />
       */
      public function loadPermissionList(){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // setup the criterion
         $crit = new GenericCriterionObject();
         $crit->addRelationIndicator('Application2Permission',$this->__getCurrentApplication());

         // load permission list
         return $oRM->loadObjectListByCriterion('Permission',$crit);

       // end function
      }

      /**
       * @public
       *
       * Loads a permission by it's id.
       *
       * @param int $permID the permission's id
       * @return GenericDomainObject The desiried permission.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.12.2008<br />
       */
      public function loadPermissionByID($permID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('Permission',$permID);
       // end function
      }

      /**
       * @public
       *
       * Loads a list of roles, that are not associated with the permission set.
       *
       * @param GenericDomainObject $permissionSet the desiried permission set
       * @return GenericDomainObject[] The roles, that are not associated.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function loadRolesNotWithPermissionSet($permissionSet){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // setup driterion
         $crit = new GenericCriterionObject();
         $crit->addRelationIndicator('Application2Role',$this->__getCurrentApplication());

         // load roles, that are not associated
         return $oRM->loadNotRelatedObjects($permissionSet,'Role2PermissionSet',$crit);

       // end function
      }

      /**
       * @public
       *
       * Loads a list of roles, that are associated with the permission set.
       *
       * @param GenericDomainObject $permissionSet the desiried permission set
       * @return GenericDomainObject[] The roles, that are associated.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function loadRolesWithPermissionSet($permissionSet){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // setup driterion
         $crit = new GenericCriterionObject();
         $crit->addRelationIndicator('Application2Role',$this->__getCurrentApplication());

         // load roles, that are not associated
         return $oRM->loadRelatedObjects($permissionSet,'Role2PermissionSet',$crit);

       // end function
      }

      /**
       * @public
       *
       * Associates a given permission set to a list of roles.
       *
       * @param GenericDomainObject $permissionSet the desiried permission set
       * @param GenericDomainObject[] $roles the roles, that have to be associated
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function assignPermissionSet2Roles($permissionSet,$roles = array()){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // create the associations
         for($i = 0; $i < count($roles); $i++){
            $oRM->createAssociation('Role2PermissionSet',$roles[$i],$permissionSet);
          // end for
         }

       // end function
      }

      /**
       * @public
       *
       * Removes a given permission set from a list of roles.
       *
       * @param GenericDomainObject $permissionSet the desiried permission set
       * @param GenericDomainObject[] $roles the roles, that have to be associated
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function detachPermissionSetFromRoles($permissionSet,$roles = array()){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // delete the associations
         for($i = 0; $i < count($roles); $i++){
            $oRM->deleteAssociation('Role2PermissionSet',$roles[$i],$permissionSet);
          // end for
         }

       // end function
      }

      /**
       * @public
       *
       * Deletes a user.
       *
       * @param GenericDomainObject[] $user the user to delete
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function deleteUser($user){
         $oRM = &$this->__getORMapper();
         $oRM->deleteObject($user);
       // end function
      }

      /**
       * @public
       *
       * Deletes a group.
       *
       * @param GenericDomainObject[] $group the group to delete
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function deleteGroup($group){
         $oRM = &$this->__getORMapper();
         $oRM->deleteObject($group);
       // end function
      }

      /**
      *  @public
      *
      *  Deletes a role.
      *
      *  @param GenericDomainObject[] $role the role to delete
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      public function deleteRole($role){
         $oRM = &$this->__getORMapper();
         $oRM->deleteObject($role);
       // end function
      }

      /**
      *  @public
      *
      *  Deletes a PermissionSet.
      *
      *  @param GenericDomainObject $permissionSet the permission set
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      public function deletePermissionSet($permissionSet){
         $oRM = &$this->__getORMapper();
         $oRM->deleteObject($permissionSet);
       // end function
      }

      /**
      *  @public
      *
      *  Deletes a Permission.
      *
      *  @param GenericDomainObject $permission the permission
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      public function deletePermission($permission){
         $oRM = &$this->__getORMapper();
         $oRM->deleteObject($permission);
       // end function
      }

      /**
      *  @public
      *
      *  Associates a user with a list of groups.
      *
      *  @param GenericDomainObject $user the user
      *  @param GenericDomainObject[] $groups the group list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      public function assignUser2Groups($user,$groups = array()){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // create the association
         for($i = 0; $i < count($groups); $i++){
            $oRM->createAssociation('Group2User',$user,$groups[$i]);
          // end for
         }

       // end function
      }

      /**
      *  @public
      *
      *  Associates users with a group.
      *
      *  @param GenericDomainObject[] $users the user list
      *  @param GenericDomainObject $group the group
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      *  Version 0.2, 18.02.2009 (Bugfix: addUser2Groups() does not exist)<br />
      */
      public function assignUsers2Group($users = array(),$group){

         for($i = 0; $i < count($users); $i++){
            $this->assignUser2Groups($users[$i],array($group));
          // end for
         }

       // end function
      }

      /**
       * @public
       *
       * Associates a role with a list of users.
       *
       * @param GenericDomainObject $role the role
       * @param GenericDomainObject[] $users the user list
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function assignRole2Users($role,$users = array()){

         // get the mapper
         $orm = &$this->__getORMapper();

         // create the association
         for($i = 0; $i < count($users); $i++){
            $orm->createAssociation('Role2User',$role,$users[$i]);
          // end for
         }

       // end function
      }

      /**
       * @public
       *
       * Loads all groups, that are assigned to a given user.
       *
       * @param GenericDomainObject $user the user
       * @return GenericDomainObject[] The group list.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       * Version 02, 05.09.2009 (Now using the GORM to load the related objects, to allow serialized objects to be used as arguments)<br />
       */
      public function loadGroupsWithUser(&$user){
         $orm = &$this->__getORMapper();
         return $orm->loadRelatedObjects($user,'Group2User');
       // end function
      }

      /**
       * @public
       *
       * Loads all groups, that are not assigned to a given user.
       *
       * @param GenericDomainObject $user the user
       * @return GenericDomainObject[] The group list.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.12.2008<br />
       */
      public function loadGroupsNotWithUser(&$user){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // setup driterion
         $crit = new GenericCriterionObject();
         $crit->addRelationIndicator('Application2Group',$this->__getCurrentApplication());

         // load roles, that are not associated
         return $oRM->loadNotRelatedObjects($user,'Group2User',$crit);

       // end function
      }

      /**
      *  @public
      *
      *  Loads all users, that are assigned to a given group.
      *
      *  @param GenericDomainObject $group the group
      *  @return GenericDomainObject[] The user list.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      *  Version 0.2, 30.12.2008 (Removed null pointer typo)<br />
      */
      public function loadUsersWithGroup(&$group){
         $oRM = &$this->__getORMapper();
         return $oRM->loadRelatedObjects($group,'Group2User');
       // end function
      }

      /**
      *  @public
      *
      *  Loads all users, that are not assigned to a given group.
      *
      *  @param GenericDomainObject $group the group
      *  @return GenericDomainObject[] The user list.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      */
      public function loadUsersNotWithGroup(&$group) {

         // get the mapper
         $oRM = &$this->__getORMapper();

         // setup the criterion
         $crit = new GenericCriterionObject();
         $app = $this->__getCurrentApplication();
         $crit->addRelationIndicator('Application2User',$app);

         // load the user list
         return $oRM->loadNotRelatedObjects($group,'Group2User',$crit);

       // end function
      }

      /**
      *  @public
      *
      *  Loads all roles, that are assigned to a given user.
      *
      *  @param GenericDomainObject $user the user
      *  @return GenericDomainObject[] The role list.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      */
      public function loadRolesWithUser(&$user){
         return $user->loadRelatedObjects('Role2User');
       // end function
      }

      /**
      *  @public
      *
      *  Loads all roles, that are not assigned to a given user.
      *
      *  @param GenericDomainObject $user the user
      *  @return GenericDomainObject[] The role list.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      */
      public function loadRolesNotWithUser(&$user){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // setup the criterion
         $crit = new GenericCriterionObject();
         $app = $this->__getCurrentApplication();
         $crit->addRelationIndicator('Application2User',$app);

         // load the user list
         return $oRM->loadNotRelatedObjects($user,'Role2User',$crit);

       // end function
      }

      /**
      *  @public
      *
      *  Loads a list of users, that have a certail role.
      *
      *  @param GenericDomainObject $role the role, the users should have
      *  @return GenericDomainObject[] Desired user list.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      public function loadUsersWithRole(&$role){
         return $role->loadRelatedObjects('Role2User');
       // end function
      }

      /**
      *  @public
      *
      *  Loads a list of users, that don't have the given role.
      *
      *  @param GenericDomainObject $role the role, the users should not have
      *  @return GenericDomainObject[] Desired user list.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      *  Version 0.2, 28.12.2008 (Bugfix: criterion definition contained wrong relation indicator)<br />
      */
      public function loadUsersNotWithRole(&$role){

         $oRM = &$this->__getORMapper();
         $crit = new GenericCriterionObject();
         $app = $this->__getCurrentApplication();
         $crit->addRelationIndicator('Application2User',$app);
         return $oRM->loadNotRelatedObjects($role,'Role2User',$crit);

       // end function
      }

      /**
      *  @public
      *
      *  Loads the permissions associated with a permission set.
      *
      *  @param GenericDomainObject $permissionSet the permission set
      *  @return GenericDomainObject[] The list of permissions.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      public function loadPermissionsOfPermissionSet(&$permissionSet){
         $oRM = &$this->__getORMapper();
         return $oRM->loadRelatedObjects($permissionSet,'PermissionSet2Permission');
       // end function
      }

      /**
      *  @public
      *
      *  Detaches a user from a role.
      *
      *  @param GenericDomainObject $user the user
      *  @param GenericDomainObject $role the desired role to detach the user from
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      public function detachUserFromRole($user,$role){
         $oRM = &$this->__getORMapper();
         $oRM->deleteAssociation('Role2User',$role,$user);
       // end function
      }

      /**
      *  @public
      *
      *  Detaches users from a role.
      *
      *  @param GenericDomainObject[] $users a list of users
      *  @param GenericDomainObject $role the desired role to detach the users from
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      public function detachUsersFromRole($users,$role){

         for($i = 0; $i < count($users); $i++){
            $this->detachUserFromRole($users[$i],$role);
          // end for
         }

       // end function
      }

      /**
      *  @public
      *
      *  Removes a user from the given groups.
      *
      *  @param GenericDomainObject $user the desired user
      *  @param GenericDomainObject $group the group
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.12.2008<br />
      */
      public function detachUserFromGroup($user,$group){
         $oRM = &$this->__getORMapper();
         $oRM->deleteAssociation('Group2User',$user,$group);
       // end function
      }

      /**
      *  @public
      *
      *  Removes a user from the given groups.
      *
      *  @param GenericDomainObject $user the desired user
      *  @param GenericDomainObject[] $groups a list of groups
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.12.2008<br />
      */
      public function detachUserFromGroups($user,$groups){

         for($i = 0; $i < count($groups); $i++){
            $this->detachUserFromGroup($user,$groups[$i]);
          // end for
         }

       // end function
      }

      /**
      *  @public
      *
      *  Removes users from a given group.
      *
      *  @param GenericDomainObject[] $users a list of users
      *  @param GenericDomainObject $group the desired group
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      */
      public function detachUsersFromGroup($users,$group){

         for($i = 0; $i < count($users); $i++){
            $this->detachUserFromGroup($users[$i],$group);
          // end for
         }

       // end function
      }

    // end class
   }
?>