<?php
   import('modules::genericormapper::data','GenericORMapperFactory');


   /**
   *  @package modules::usermanagement::biz
   *  @module umgtManager
   *
   *  Business component of the user management module.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.04.2008<br />
   *  Version 0.2, 23.06.2008 (Mapper is now loaded by an internal method that uses the GenericORMapperFactory)<br />
   */
   class umgtManager extends coreObject
   {

      /**
      *  @private
      *  Indicates the id of the current application/project.
      */
      var $__ApplicationID = 1;


      function umgtManager(){
      }


      /**
      *  @private
      *
      *  Returns an initialized Application object.
      *
      *  @return GenericDomainObject $app current application domain object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.06.2008<br />
      */
      function __getCurrentApplication(){
         $app = new GenericDomainObject('Application');
         $app->setProperty('ApplicationID',$this->__ApplicationID);
         return $app;
       // end function
      }


      /**
      *  @private
      *
      *  Returns an initialized or mapper instance.
      *
      *  @return GenericORRelationMapper $ORM instance of the generic or relation mapper
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.06.2008<br />
      */
      function &__getORMapper(){

         // obtain a reference on the mapper factory
         $ORMFactory = &$this->__getServiceObject('modules::genericormapper::data','GenericORMapperFactory');

         // return mapper instance
         return $ORMFactory->getGenericORMapper('modules::usermanagement','umgt','usermanagement_test','SESSIONSINGLETON');

       // end function
      }


      /**
      *  @public
      *
      *  Saves a user object within the current application.
      *
      *  @param GenericDomainObject $User current user
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.06.2008<br />
      */
      function saveUser($User){
         $oRM = &$this->__getORMapper();
         $App = $this->__getCurrentApplication();
         $User->setProperty('DisplayName',$User->getProperty('LastName').', '.$User->getProperty('FirstName'));
         $App->addRelatedObject('Application2User',$User);
         $oRM->saveObject($App);
       // end function
      }


      /**
      *  @public
      *
      *  Saves a group object within the current application.
      *
      *  @param GenericDomainObject $group current group
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.06.2008<br />
      */
      function saveGroup($group){
         $oRM = &$this->__getORMapper();
         $app = $this->__getCurrentApplication();
         $app->addRelatedObject('Application2Group',$group);
         $oRM->saveObject($app);
       // end function
      }


      /**
      *  @public
      *
      *  Saves a role object within the current application.
      *
      *  @param GenericDomainObject $role current role
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.06.2008<br />
      */
      function saveRole($role){
         $oRM = &$this->__getORMapper();
         $app = $this->__getCurrentApplication();
         $app->addRelatedObject('Application2Role',$role);
         $oRM->saveObject($app);
       // end function
      }


      /**
      *  @public
      *
      *  Saves a permission set object within the current application.
      *
      *  @param GenericDomainObject $permissionSet a permission set
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.06.2008<br />
      *  Version 0.2, 28.12.2008 (Bugfix: unnecessary associations are now deleted)<br />
      */
      function savePermissionSet($permissionSet){

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

         // save tree
         $oRM->saveObject($app);

       // end function
      }


      /**
      *  @public
      *
      *  Saves a permission object within the current application.
      *
      *  @param GenericDomainObject $permission the permission
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.06.2008<br />
      *  Version 0.2, 16.06.2008 (The permission set is lazy loaded when not present)<br />
      *  Version 0.3, 28.12.2008 (Changed the API concerning the new UML diagram)<br />
      */
      function savePermission($permission){

         // load generic or mapper
         $oRM = &$this->__getORMapper();

         // add permission to structure
         $app = $this->__getCurrentApplication();
         $app->addRelatedObject('Application2Permission',$permission);

         // save tree
         $oRM->saveObject($app);

       // end function
      }


      /**
      *  @public
      *
      *  Returns a list of users concerning the current page.
      *
      *  @return GenericDomainObject[] $users list of users
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.06.2008<br />
      *  Version 0.2, 17.06.2008 (introduced query over current application)<br />
      */
      function getPagedUserList(){

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
      *  @public
      *
      *  Returns a list of groups concerning the current page.
      *
      *  @return GenericDomainObject[] $groupList list of groups
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      */
      function getPagedGroupList(){

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
      *  @public
      *
      *  Returns a list of roles concerning the current page.
      *
      *  @return GenericDomainObject[] $roleList list of roles
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function getPagedRoleList(){
         $ORM = &$this->__getORMapper();
         $select = 'SELECT * FROM ent_role ORDER BY DisplayName ASC';
         return $ORM->loadObjectListByTextStatement('Role',$select);
       // end function
      }


      /**
      *  @public
      *
      *  Returns a list of permission sets concerning the current page.
      *
      *  @return GenericDomainObject[] $permissionSetList list of permission sets
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function getPagedPermissionSetList(){
         $oRM = &$this->__getORMapper();
         $select = 'SELECT * FROM ent_permissionset ORDER BY DisplayName ASC';
         return $oRM->loadObjectListByTextStatement('PermissionSet',$select);
       // end function
      }


      /**
      *  @public
      *
      *  Returns a list of permissions concerning the current page.
      *
      *  @return GenericDomainObject[] $permissionList list of permissions
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function getPagedPermissionList(){
         $oRM = &$this->__getORMapper();
         $select = 'SELECT * FROM ent_permission ORDER BY DisplayName ASC';
         return $oRM->loadObjectListByTextStatement('Permission',$select);
       // end function
      }


      /**
      *  @public
      *
      *  Returns a user domain object.
      *
      *  @param int $userID id of the desired user
      *  @return GenericDomainObject[] $user the user domain object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function loadUserByID($userID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('User',$userID);
       // end function
      }


      /**
      *  @public
      *
      *  Returns a group domain object.
      *
      *  @param int $groupID id of the desired group
      *  @return GenericDomainObject[] $group the group domain object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function loadGroupByID($groupID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('Group',$groupID);
       // end function
      }


      /**
      *  @public
      *
      *  Returns a role domain object.
      *
      *  @param int $roleID id of the desired role
      *  @return GenericDomainObject[] $role the role domain object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function loadRoleByID($roleID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('Role',$roleID);
       // end function
      }


      /**
      *  @public
      *
      *  Loads a permission set by it's id.
      *
      *  @param int $permissionSetID the permission set's id
      *  @return GenericDomainObject $permissionSet the permission set
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function loadPermissionSetByID($permissionSetID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('PermissionSet',$permissionSetID);
       // end function
      }


      /**
      *  @public
      *
      *  Loads a list of permissions of the current application.
      *
      *  @return GenericDomainObject[] $permissions the permission list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      function loadPermissionList(){

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
      *  @public
      *
      *  Loads a permission by it's id.
      *
      *  @param int $permID the permission's id
      *  @return GenericDomainObject $permission the desiried permission
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      function loadPermissionByID($permID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('Permission',$permID);
       // end function
      }


      /**
      *  @public
      *
      *  Loads a list of roles, that are not associated with the permission set.
      *
      *  @param GenericDomainObject $permissionSet the desiried permission set
      *  @return GenericDomainObject[] $roles the roles, that are not associated
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function loadRolesNotWithPermissionSet($permissionSet){

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
      *  @public
      *
      *  Loads a list of roles, that are associated with the permission set.
      *
      *  @param GenericDomainObject $permissionSet the desiried permission set
      *  @return GenericDomainObject[] $roles the roles, that are associated
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function loadRolesWithPermissionSet($permissionSet){

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
      *  @public
      *
      *  Associates a given permission set to a list of roles.
      *
      *  @param GenericDomainObject $permissionSet the desiried permission set
      *  @param GenericDomainObject[] $roles the roles, that have to be associated
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function assignPermissionSet2Roles($permissionSet,$roles = array()){

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
      *  @public
      *
      *  Associates a given permission set to a list of roles.
      *
      *  @param GenericDomainObject $permissionSet the desiried permission set
      *  @param GenericDomainObject[] $roles the roles, that have to be associated
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function detachPermissionSetFromRoles($permissionSet,$roles = array()){

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
      *  @public
      *
      *  Deletes a user.
      *
      *  @param GenericDomainObject[] $user the user to delete
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function deleteUser($user){
         $oRM = &$this->__getORMapper();
         $oRM->deleteObject($user);
       // end function
      }


      /**
      *  @public
      *
      *  Deletes a group.
      *
      *  @param GenericDomainObject[] $group the group to delete
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function deleteGroup($group){
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
      function deleteRole($role){
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
      function deletePermissionSet($permissionSet){
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
      function deletePermission($permission){
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
      function assignUser2Groups($user,$groups = array()){

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
      */
      function assignUsers2Group($users = array(),$group){

         for($i = 0; $i < count($users); $i++){
            $this->addUser2Groups($users[$i],array($group));
          // end for
         }

       // end function
      }


      /**
      *  @public
      *
      *  Associates a role with a list of users.
      *
      *  @param GenericDomainObject $role the role
      *  @param GenericDomainObject[] $users the user list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function assignRole2Users($role,$users = array()){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // create the association
         for($i = 0; $i < count($users); $i++){
            $oRM->createAssociation('Role2User',$role,$users[$i]);
          // end for
         }

       // end function
      }


      /**
      *  @public
      *
      *  Loads all groups, that are assigned to a given user.
      *
      *  @param GenericDomainObject $user the user
      *  @return GenericDomainObject[] $roles the role list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function loadGroupsWithUser(&$user){
         return $user->loadRelatedObjects('Group2User');
       // end function
      }


      /**
      *  @public
      *
      *  Loads all groups, that are not assigned to a given user.
      *
      *  @param GenericDomainObject $user the user
      *  @return GenericDomainObject[] $roles the role list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function loadGroupsNotWithUser(&$user){

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
      *  @return GenericDomainObject[] $users the user list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.12.2008<br />
      */
      function loadUsersWithGroup(&$group){
         $oRM = &$this->__getORMapper();
         return $oRM->loadRelatedObjects($Group,'Group2User');
       // end function
      }


      /**
      *  @public
      *
      *  Loads all users, that are not assigned to a given group.
      *
      *  @param GenericDomainObject $group the group
      *  @return GenericDomainObject[] $users the user list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      */
      function loadUsersNotWithGroup(&$group) {

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
      *  @return GenericDomainObject[] $roles the role list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      */
      function loadRolesWithUser(&$user){
         return $user->loadRelatedObjects('Role2User');
       // end function
      }


      /**
      *  @public
      *
      *  Loads all roles, that are not assigned to a given user.
      *
      *  @param GenericDomainObject $user the user
      *  @return GenericDomainObject[] $roles the role list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      */
      function loadRolesNotWithUser(&$user){

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
      *  @return GenericDomainObject[] $users desired user list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      function loadUsersWithRole(&$role){
         return $role->loadRelatedObjects('Role2User');
       // end function
      }


      /**
      *  @public
      *
      *  Loads a list of users, that don't have the given role.
      *
      *  @param GenericDomainObject $role the role, the users should not have
      *  @return GenericDomainObject[] $users desired user list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      *  Version 0.2, 28.12.2008 (Bugfix: criterion definition contained wrong relation indicator)<br />
      */
      function loadUsersNotWithRole(&$role){

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
      *  @return GenericDomainObject[] $permissions the list of permissions
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      function loadPermissionsOfPermissionSet(&$permissionSet){
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
      function detachUserFromRole($user,$role){
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
      function detachUsersFromRole($users,$role){

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
      function detachUserFromGroup($user,$group){
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
      function detachUserFromGroups($user,$groups){

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
      function detachUsersFromGroup($users,$group){

         for($i = 0; $i < count($users); $i++){
            $this->detachUserFromGroup($users[$i],$group);
          // end for
         }

       // end function
      }

    // end class
   }
?>