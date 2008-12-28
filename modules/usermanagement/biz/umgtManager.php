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
      *  Returns an initialized Application object.<br />
      *
      *  @return GenericDomainObject $App; current application domain object
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
      *  Returns an initialized or mapper instance.<br />
      *
      *  @return GenericORRelationMapper $ORM; instance of the generic or relation mapper
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.06.2008<br />
      */
      function &__getORMapper(){

         // obtain a reference on the mapper factory
         $ORMFactory = $this->__getServiceObject('modules::genericormapper::data','GenericORMapperFactory');

         // return mapper instance
         return $ORMFactory->getGenericORMapper('modules::usermanagement','umgt','usermanagement_test','SESSIONSINGLETON');

       // end function
      }


      /**
      *  @public
      *
      *  Saves a user object within the current application.<br />
      *
      *  @param GenericDomainObject $User; Current user
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
      *  Saves a group object within the current application.<br />
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
      *  Saves a role object within the current application.<br />
      *
      *  @param GenericDomainObject $Role; Current role
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.06.2008<br />
      */
      function saveRole($Role){
         $ORM = &$this->__getORMapper();
         $App = $this->__getCurrentApplication();
         $App->addRelatedObject('Application2Role',$Role);
         $ORM->saveObject($App);
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
      *  Returns a list of users concerning the current page.<br />
      *
      *  @return GenericDomainObject[] $UserList; list of users
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
/*
         // select by criterion
         $Crit = new GenericCriterionObject();
         // --> Kriterium erzeugen

         $Crit->addRelationIndicator('Application2User',$this->__getCurrentApplication());
         $Group = new GenericDomainObject('Group');
         $Group->setProperty('GroupID',1);
         $Crit->addRelationIndicator('Group2User',$Group);
         // --> ausdrücken, dass zu selektierende Objekte per Beziehung XXX unterhalb des übergebenen Objekts
         //     komponiert sein sollen, bzw. zum entsprechenden Objekt assoziiert sein sollen.

         $Crit->addCountIndicator(0,3);
         //$Crit->addCountIndicator(2);
         // --> ausdrücken, dass beginnend ab 0 in der Liste 10 Ergebnisse selekteirt werden sollen

         $Crit->addPropertyIndicator('LastName','Achatz');
         // --> Ausdrücken, welches Attribut als weitere WHERE-Einschränkung gilt

         $Crit->addOrderIndicator('FirstName','ASC');
         $Crit->addOrderIndicator('LastName','ASC');
         // --> ausdrücken, welches Attribut in welcher Reihenfolge als Sortier-Kriterium dient

         $Crit->addLoadedProperty('FirstName');
         $Crit->addLoadedProperty('LastName');
         // --> ausdrücken, welche Attribute geladen werden sollen

         return $ORM->loadObjectListByCriterion('User',$Crit);
*/
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


      function getPagedRoleList(){
         $ORM = &$this->__getORMapper();
         $select = 'SELECT * FROM ent_role ORDER BY DisplayName ASC';
         return $ORM->loadObjectListByTextStatement('Role',$select);
       // end function
      }

      function getPagedPermissionSetList(){
         $ORM = &$this->__getORMapper();
         $select = 'SELECT * FROM ent_permissionset ORDER BY DisplayName ASC';
         return $ORM->loadObjectListByTextStatement('PermissionSet',$select);
       // end function
      }

      function loadPermissionSetList(){
         $ORM = &$this->__getORMapper();
         $select = 'SELECT * FROM ent_permissionset ORDER BY DisplayName ASC';
         return $ORM->loadObjectListByTextStatement('PermissionSet',$select);
       // end function
      }

      function getPagedPermissionList(){
         $ORM = &$this->__getORMapper();
         $select = 'SELECT * FROM ent_permission ORDER BY DisplayName ASC';
         return $ORM->loadObjectListByTextStatement('Permission',$select);
       // end function
      }

      function loadUserByID($UserID){
         $ORM = &$this->__getORMapper();
         return $ORM->loadObjectByID('User',$UserID);
       // end function
      }

      function loadGroupByID($GroupID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('Group',$GroupID);
       // end function
      }

      function loadRoleByID($RoleID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('Role',$RoleID);
       // end function
      }

      function loadPermissionSetByID($PermissionSetID){
         $oRM = &$this->__getORMapper();
         return $oRM->loadObjectByID('PermissionSet',$PermissionSetID);
       // end function
      }


      /**
      *  @public
      *
      *  Loads a list of permissions of the current application.
      *
      *  @return array $permissions the permission list
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


      function deleteUser($User){
         $oRM = &$this->__getORMapper();
         $oRM->deleteObject($User);
       // end function
      }

      function deleteGroup($Group){
         $oRM = &$this->__getORMapper();
         $oRM->deleteObject($Group);
       // end function
      }

      function deleteRole($Role){
         $oRM = &$this->__getORMapper();
         $oRM->deleteObject($Role);
       // end function
      }


      /**
      *  @public
      *
      *  Deletes a PermissionSet and removes the associations.
      *
      *  @param GenericDomainObject $permissionSet the permission set
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      function deletePermissionSet($permissionSet){

         // get or mapper
         $oRM = &$this->__getORMapper();

         // load permissions
         $permissions = $oRM->loadRelatedObjects($permissionSet,'PermissionSet2Permission');

         // delete associations
         for($i = 0; $i < count($permissions); $i++){
            $oRM->deleteAssociation('PermissionSet2Permission',$permissionSet,$permissions[$i]);
          // end for
         }

         // delete permission set itself
         $oRM->deleteObject($permissionSet);

       // end function
      }




      function deletePermission($Permission){
         $ORM = &$this->__getORMapper();
         $ORM->deleteObject($Permission);
       // end function
      }

      function loadGroupList(){
         $ORM = &$this->__getORMapper();
         $select = 'SELECT * FROM ent_group ORDER BY DisplayName ASC;';
         return $ORM->loadObjectListByTextStatement('Group',$select);
       // end function
      }

      function loadUserList(){
         $ORM = &$this->__getORMapper();
         $select = 'SELECT * FROM ent_user ORDER BY LastName ASC, FirstName ASC;';
         return $ORM->loadObjectListByTextStatement('User',$select);
       // end function
      }

      function addUser2Groups($UserID,$GroupIDs = array()){

         $ORM = &$this->__getORMapper();
         $User = new GenericDomainObject('User');
         $User->setProperty('UserID',$UserID);

         $count = count($GroupIDs);
         for($i = 0; $i < $count; $i++){
            $Group = new GenericDomainObject('Group');
            $Group->setProperty('GroupID',$GroupIDs[$i]);
            $ORM->createAssociation('Group2User',$User,$Group);
          // end for
         }

       // end function
      }

      function addUsers2Group($UserIDs = array(),$GroupID){

         $ORM = &$this->__getORMapper();
         $Group = new GenericDomainObject('Group');
         $Group->setProperty('GroupID',$GroupID);

         $count = count($UserIDs);
         for($i = 0; $i < $count; $i++){
            $User = new GenericDomainObject('User');
            $User->setProperty('UserID',$UserIDs[$i]);
            $ORM->createAssociation('Group2User',$User,$Group);
          // end for
         }

       // end function
      }

      function assignRole2Users($RoleID,$UserIDs = array()){

         $ORM = &$this->__getORMapper();
         $Role = new GenericDomainObject('Role');
         $Role->setProperty('RoleID',$RoleID);

         $count = count($UserIDs);
         for($i = 0; $i < $count; $i++){
            $User = new GenericDomainObject('User');
            $User->setProperty('UserID',$UserIDs[$i]);
            $ORM->createAssociation('Role2User',$Role,$User);
          // end for
         }

       // end function
      }

      function loadUserGroups(&$User){
         return $User->loadRelatedObjects('Group2User');
       // end function
      }

      function loadnotUserGroups(&$User){
       $select = "select * from ent_group g where not exists (select * from ass_group2user au
                  where g.GroupID = au.GroupID and au.UserID = " . $User->getProperty('UserID') . ")";
       $ORM = $this->__getORMapper();
       return $ORM->loadObjectListByTextStatement('User', $select);
      }


      // load all Users that belong to group $Group
      function loadGroupUsers(&$Group){

         /*$Crit = new GenericCriterionObject();
         $Crit->addOrderIndicator('DisplayName','ASC');
         $Crit->addPropertyIndicator('DisplayName','%A%');
         $Crit->addCountIndicator(10);
         return $Group->loadRelatedObjects('Group2User',$Crit);*/

         $ORM = &$this->__getORMapper();
         return $ORM->loadRelatedObjects($Group,'Group2User');

       // end function
      }


      /**
      *  @public
      *
      *  Loads all users, that are not within the given group.
      *
      *  @param GenericDomainObject $group the desired group
      *  @return array $users the users, that are not within the group
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      */
      function loadUsersNotInGroup($group) {

         $oRM = &$this->__getORMapper();
         $crit = new GenericCriterionObject();
         $app = $this->__getCurrentApplication();
         $crit->addRelationIndicator('Application2User',$app);
         return $oRM->loadNotRelatedObjects($group,'Group2User',$crit);

       // end function
      }


      function loadUserRoles(&$User){
         return $User->loadRelatedObjects('Role2User');
       // end function
      }

      function loadnotUserRoles($User) {
       $select = "select * from ent_role r where not exists (select * from ass_role2user ru where ru.RoleID = r.RoleID and
                  ru.UserID = " . $User->getProperty('UserID') . ")";
       $ORM = $this->__getORMapper();
       return $ORM->loadObjectListByTextStatement('Role', $select);
      }


      /**
      *  @public
      *
      *  Loads a list of users, that have a certail role.
      *
      *  @param GenericDomainObject $role the role, the users should have
      *  @return array $users desired user list
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
      *  @return array $users desired user list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      *  Version 0.2, 28.12.2008 (Bugfix: criterion definition contained wrong relation indicator)<br />
      */
      function loadUsersNotWithRole($role){

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
      *  @return array $permissions the list of permissions
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      function loadPermissionSetPermissions(&$permissionSet){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // load the permissions
         return $oRM->loadRelatedObjects($permissionSet,'PermissionSet2Permission');

       // end function
      }


      /**
      *  @public
      *
      *  Detaches a user from a role.
      *
      *  @param array $userID a user id
      *  @param int $roleID the desired role to detach the user from
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      function detachUserFromRole($userID,$roleID){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // create the user
         $user = new GenericDomainObject('User');
         $user->setProperty('UserID',$userID);

         // create the role and delete the association
         $role = new GenericDomainObject('Role');
         $role->setProperty('RoleID',$roleID);
         $oRM->deleteAssociation('Role2User',$role,$user);

       // end function
      }


      /**
      *  @public
      *
      *  Detaches users from a role.
      *
      *  @param array $userIDs a list of user ids
      *  @param int $roleID the desired role to detach the users from
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      function detachUsersFromRole($userIDs,$roleID){

         for($i = 0; $i < count($userIDs); $i++){
            $this->detachUserFromRole($userIDs[$i],$roleID);
          // end for
         }

       // end function
      }


      /**
      *  @public
      *
      *  Removes a user from the given groups.
      *
      *  @param int $userID the id of the desired user
      *  @param int $groupID the group id
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.12.2008<br />
      */
      function removeUserFromGroup($userID,$groupID){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // create user
         $user = new GenericDomainObject('User');
         $user->setProperty('UserID',$userID);

         // delete the association between user and group
         $group = new GenericDomainObject('Group');
         $group->setProperty('GroupID',$groupID);
         $oRM->deleteAssociation('Group2User',$user,$group);

       // end function
      }


      /**
      *  @public
      *
      *  Removes a user from the given groups.
      *
      *  @param array $userIDs a list of user ids
      *  @param int $groupID a group id
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      */
      function removeUsersFromGroup($userIDs,$groupID){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // delete the association between user and group
         $group = new GenericDomainObject('Group');
         $group->setProperty('GroupID',$groupID);

         for($i = 0; $i < count($userIDs); $i++){
            $user = new GenericDomainObject('User');
            $user->setProperty('UserID',$userIDs[$i]);
            $oRM->deleteAssociation('Group2User',$user,$group);
          // end for
         }

       // end function
      }


      /**
      *  @public
      *
      *  Removes a user from the given groups.
      *
      *  @param int $userID the id of the desired user
      *  @param array $groupIDs a list of group ids
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.12.2008<br />
      */
      function removeUserFromGroups($userID,$groupIDs){

         // get the mapper
         $oRM = &$this->__getORMapper();

         // create user
         $user = new GenericDomainObject('User');
         $user->setProperty('UserID',$userID);

         // delete the association between user and group
         foreach($groupIDs as $groupID){

            $group = new GenericDomainObject('Group');
            $group->setProperty('GroupID',$groupID);
            $oRM->deleteAssociation('Group2User',$user,$group);

          // end foreach
         }

       // end function
      }

    // end class
   }
?>