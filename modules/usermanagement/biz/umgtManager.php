<?php
   import('modules::genericormapper::data','GenericORMapperFactory');


   /**
   *  @package modules::usermanagement::biz
   *  @module umgtManager
   *
   *  Business component of the user management module.<br />
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
         $App = new GenericDomainObject('Application');
         $App->setProperty('ApplicationID',$this->__ApplicationID);
         return $App;
       // end function
      }


      /**
      *  @private
      *
      *  Returns an initialized or mapper instance.<br />
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
         return $ORMFactory->getGenericORMapper('modules::usermanagement','umgt_'.$this->__ApplicationID,'usermanagement_test','SESSIONSINGLETON');

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
         $ORM = &$this->__getORMapper();
         $App = $this->__getCurrentApplication();
         $User->setProperty('DisplayName',$User->getProperty('LastName').', '.$User->getProperty('FirstName'));
         $App->addRelatedObject('Application2User',$User);
         $ORM->saveObject($App);
       // end function
      }


      /**
      *  @public
      *
      *  Saves a group object within the current application.<br />
      *
      *  @param GenericDomainObject $Group; Current group
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.06.2008<br />
      */
      function saveGroup($Group){
         $ORM = &$this->__getORMapper();
         $App = $this->__getCurrentApplication();
         $App->addRelatedObject('Application2Group',$Group);
         $ORM->saveObject($App);
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
      *  Saves a permission set object within the current application.<br />
      *
      *  @param GenericDomainObject $PermissionSet; Current permission set
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.06.2008<br />
      */
      function savePermissionSet($PermissionSet){
         $ORM = &$this->__getORMapper();
         $App = $this->__getCurrentApplication();
         $App->addRelatedObject('Application2PermissionSet',$PermissionSet);
         $ORM->saveObject($App);
       // end function
      }


      /**
      *  @public
      *
      *  Saves a permission object within the current application.<br />
      *
      *  @param GenericDomainObject $Permission; Current permission
      *  @param GenericDomainObject $PermissionSet; Current permission set
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.06.2008<br />
      *  Version 0.2, 16.06.2008 (The permission set is lazy loaded when not present)<br />
      */
      function savePermission($Permission,$PermissionSet = null){

         // load generic or mapper
         $ORM = &$this->__getORMapper();

         // lazy load permission set, if not present
         if($PermissionSet === null){
            $RelObjects = $ORM->loadRelatedObjects($Permission,'PermissionSet2Permission');
            $PermissionSet = $RelObjects[0];
          // end if
         }

         // add permission to structure
         $PermissionSet->addRelatedObject('PermissionSet2Permission',$Permission);

         // add permissionset to structure
         $App = $this->__getCurrentApplication();
         $App->addRelatedObject('Application2PermissionSet',$PermissionSet);

         // save tree
         $ORM->saveObject($App);

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
         return $ORM->loadObjectListByTextStatement('User',$select);
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

      function getPagedGroupList(){

         // get or mapper instance
         $ORM = &$this->__getORMapper();

         // configure criterion object
         $Crit = new GenericCriterionObject();
         $Crit->addRelationIndicator('Application2Group',$this->__getCurrentApplication());

         // return desired list
         return $ORM->loadObjectListByCriterion('Group',$Crit);

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
         $ORM = &$this->__getORMapper();
         return $ORM->loadObjectByID('Group',$GroupID);
       // end function
      }

      function loadRoleByID($RoleID){
         $ORM = &$this->__getORMapper();
         return $ORM->loadObjectByID('Role',$RoleID);
       // end function
      }

      function loadPermissionSetByID($PermissionSetID){
         $ORM = &$this->__getORMapper();
         return $ORM->loadObjectByID('PermissionSet',$PermissionSetID);
       // end function
      }

      function loadPermissionByID($PermissionID){
         $ORM = &$this->__getORMapper();
         return $ORM->loadObjectByID('Permission',$PermissionID);
       // end function
      }

      function deleteUser($User){
         $ORM = &$this->__getORMapper();
         $ORM->deleteObject($User);
       // end function
      }

      function deleteGroup($Group){
         $ORM = &$this->__getORMapper();
         $ORM->deleteObject($Group);
       // end function
      }

      function deleteRole($Role){
         $ORM = &$this->__getORMapper();
         $ORM->deleteObject($Role);
       // end function
      }

      function deletePermissionSet($PermissionSet){

         // get or mapper
         $ORM = &$this->__getORMapper();

         // load permissions
         $PermissionSet->setByReference('DataComponent',$ORM);
         $Permissions = $PermissionSet->loadRelatedObjects('PermissionSet2Permission');

         // delete permissions
         $count = count($Permissions);
         for($i = 0; $i < $count; $i++){
            $ORM->deleteObject($Permissions[$i]);
          // end for
         }

         // delete permissionset itself
         $ORM->deleteObject($PermissionSet);

       // end function
      }

      function deletePermission($Permission){
         $ORM = &$this->__getORMapper();
         $ORM->deleteObject($Permission);
       // end function
      }



      /**
      *  @public
      *
      *  Loads a list of user objects, but excludes the group of the given user.
      *
      *  @param int $userId id ot the user, whom groups should be excluded
      *  @return GenericDomainObject[] $Groups a list of groups
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.06.2008<br />
      */
      function loadGroupList($userId,$loadNotRelatedObjects = false){

         // get the mapper
         $ORM = &$this->__getORMapper();

         // create user object
         $user = new GenericDomainObject('User');
         $user->setProperty('UserID',$userId);

         // load the desired object list
         if($loadNotRelatedObjects === true){
            $criterion = new GenericCriterionObject();
            //$app = $this->__getCurrentApplication();
            $app = new GenericDomainObject('Application');
            $app->setProperty('ApplicationID',1);
            $criterion->addRelationIndicator('Application2Group',$app);
            //$criterion->addPropertyIndicator('DisplayName','N%');
            return $ORM->loadNotRelatedObjects($user,'Group2User',$criterion);
          // end if
         }
         else{
            return $ORM->loadRelatedObjects($user,'Group2User');
          // end else
         }

/*
         if($user !== null){

            $userID = $user->getProperty('UserID');

            $select = 'SELECT ent_group . *
                       FROM ent_group
                       WHERE ent_group.GroupID NOT
                       IN
                       (
                          SELECT ent_group.GroupID
                          FROM ent_group
                          INNER JOIN ass_group2user ON ent_group.GroupID = ass_group2user.GroupID
                          INNER JOIN ent_user ON ass_group2user.UserID = ent_user.UserID
                          WHERE ent_user.UserID = '.$userID.'
                       )';

          // end if
         }
         else{
            $select = 'SELECT * FROM ent_group ORDER BY DisplayName ASC;';
          // end else
         }
*/
         //echo printObject($ORM->__MappingTable);

         //echo $select;
         //return $ORM->loadObjectListByTextStatement('Group',$select);

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

      function loadUserRoles(&$User){
         return $User->loadRelatedObjects('Role2User');
       // end function
      }

      function loadRoleUsers(&$Role){
         return $Role->loadRelatedObjects('Role2User');
       // end function
      }

      function loadPermissionSetPermissions(&$PermissionSet){
         return $PermissionSet->loadRelatedObjects('PermissionSet2Permission');
       // end function
      }

      function removeUserFromGroup($UserID,$GroupID){

         $ORM = &$this->__getORMapper();

         $User = new GenericDomainObject('User');
         $User->setProperty('UserID',$UserID);

         $Group = new GenericDomainObject('Group');
         $Group->setProperty('GroupID',$GroupID);

         $ORM->deleteAssociation('Group2User',$User,$Group);

       // end function
      }

      function detachUserFromRole($UserID,$RoleID){

         $ORM = &$this->__getORMapper();

         $User = new GenericDomainObject('User');
         $User->setProperty('UserID',$UserID);

         $Role = new GenericDomainObject('Role');
         $Role->setProperty('RoleID',$RoleID);

         $ORM->deleteAssociation('Role2User',$Role,$User);

       // end function
      }


      /**
      *  @public
      *
      *  Test method, that selects a list of users, that are composed to one application and
      *  have a certain role.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 25.10.2008<br />
      */
      function getUserListForApplicationAndRole(){

         $oRM = &$this->__getORMapper();
         $app = $this->__getCurrentApplication();

         $role = new GenericDomainObject('Role');
         $role->setProperty('RoleID',2);

         $crit = new GenericCriterionObject();
         $crit->addRelationIndicator('Role2User',$role);

         //$user = new GenericDomainObject('User');
         //$user->setProperty('UserID',6);

         return $oRM->loadRelatedObjects($app,'Application2User',$crit);

       // end function
      }


      function testAddMappingConfiguration(){
         $oRMF = &$this->__getServiceObject('modules::genericormapper::data','GenericORMapperFactory');
         $oRM = &$oRMF->getGenericORMapper('modules::usermanagement','umgt_1','usermanagement_test');
         $oRM->addMappingConfiguration('modules::usermanagement','umgt_2');
         $oRM->addRelationConfiguration('modules::usermanagement','umgt_2');
         //echo 'MappingTable: '.printObject($oRM->__MappingTable);
         //echo 'RelationTable: '.printObject($oRM->__RelationTable);
       // end function
      }

    // end class
   }
?>