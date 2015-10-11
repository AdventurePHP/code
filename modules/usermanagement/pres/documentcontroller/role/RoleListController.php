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
namespace APF\modules\usermanagement\pres\documentcontroller\role;

use APF\modules\usermanagement\biz\model\UmgtRole;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;

/**
 * Implements the controller list the existing roles.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class RoleListController extends UmgtBaseController {

   public function transformContent() {

      // load role list
      $uM = & $this->getManager();
      $roleList = $uM->getPagedRoleList();

      // display list
      $buffer = (string) '';
      $template = & $this->getTemplate('Role');
      foreach ($roleList as $role) {

         $roleId = $role->getObjectId();
         $template->setPlaceHolder('DisplayName', $role->getDisplayName());

         $template->setPlaceHolder('Permissions', $this->getPermissionList($role));
         $template->setPlaceHolder('Users', $this->getUserList($role));
         $template->setPlaceHolder('Groups', $this->getGroupList($role));

         $template->setPlaceHolder('role_details', $this->generateLink(['mainview' => 'role', 'roleview' => 'details', 'roleid' => $roleId]));
         $template->setPlaceHolder('role_edit', $this->generateLink(['mainview' => 'role', 'roleview' => 'edit', 'roleid' => $roleId]));
         $template->setPlaceHolder('role_delete', $this->generateLink(['mainview' => 'role', 'roleview' => 'delete', 'roleid' => $roleId]));

         $template->setPlaceHolder('AddPermissionToRole', $this->generateLink(['mainview' => 'role', 'roleview' => 'add_permission_to_role', 'roleid' => $roleId]));
         $template->setPlaceHolder('RemoveRoleFromPermission', $this->generateLink(['mainview' => 'role', 'roleview' => 'remove_permission_from_role', 'roleid' => $roleId]));
         $template->setPlaceHolder('AssignRoleToUser', $this->generateLink(['mainview' => 'role', 'roleview' => 'ass2user', 'roleid' => $roleId]));
         $template->setPlaceHolder('DetachRoleFromUser', $this->generateLink(['mainview' => 'role', 'roleview' => 'detachfromuser', 'roleid' => $roleId]));

         $template->setPlaceHolder('AddGroupToRole', $this->generateLink(['mainview' => 'group', 'groupview' => 'add_role_to_groups', 'roleid' => $roleId]));
         $template->setPlaceHolder('RemoveGroupFromRole', $this->generateLink(['mainview' => 'group', 'groupview' => 'remove_role_from_groups', 'roleid' => $roleId]));

         $buffer .= $template->transformTemplate();

      }
      $this->setPlaceHolder('RoleList', $buffer);

   }

   private function getPermissionList(UmgtRole $role) {

      $permissions = $this->getManager()->loadPermissionsWithRole($role);

      if (count($permissions) < 1) {
         return '';
      }

      $permissionList = '<ul>';
      foreach ($permissions as $permission) {
         $permissionList .= '<li>' . $permission->getDisplayName() . '</li>';
      }

      return $permissionList . '</ul>';
   }

   private function getUserList(UmgtRole $role) {

      $users = $this->getManager()->loadUsersWithRole($role);

      if (count($users) < 1) {
         return '';
      }

      $userList = '<ul>';
      foreach ($users as $user) {
         $userList .= '<li>' . $user->getDisplayName() . '</li>';
      }

      return $userList . '</ul>';
   }

   private function getGroupList(UmgtRole $role) {

      $groups = $this->getManager()->loadGroupsWithRole($role);

      if (count($groups) < 1) {
         return '';
      }

      $groupList = '<ul>';
      foreach ($groups as $group) {
         $groupList .= '<li>' . $group->getDisplayName() . '</li>';
      }

      return $groupList . '</ul>';
   }

}
