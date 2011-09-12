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
import('modules::usermanagement::pres::documentcontroller', 'umgt_base_controller');

/**
 * @package modules::usermanagement::pres::documentcontroller::user
 * @class umgt_user_list_controller
 *
 * Implements the list controller for users.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.12.2008<br />
 */
class umgt_user_list_controller extends umgt_base_controller {

   public function transformContent() {

      $uM = &$this->getManager();
      $userList = $uM->getPagedUserList();

      $buffer = '';
      $template = $this->getTemplate('User');
      foreach ($userList as $user) {
         $template->setPlaceHolder('DisplayName', $user->getDisplayName());
         $template->setPlaceHolder('Username', $user->getUsername());

         $template->setPlaceHolder('Groups', $this->getGroupNames($user));
         $template->setPlaceHolder('Roles', $this->getRoleNames($user));

         $userId = $user->getObjectId();

         // user main actions
         $template->setPlaceHolder('LinkUserDetails', $this->generateLink(array('mainview' => 'user', 'userview' => 'details', 'userid' => $userId)));
         $template->setPlaceHolder('LinkUserEdit', $this->generateLink(array('mainview' => 'user', 'userview' => 'edit', 'userid' => $userId)));
         $template->setPlaceHolder('LinkUserDelete', $this->generateLink(array('mainview' => 'user', 'userview' => 'delete', 'userid' => $userId)));

         // relating actions
         $template->setPlaceHolder('AddUserToGroup', $this->generateLink(array('mainview' => 'group', 'groupview' => 'add_user_to_groups', 'userid' => $userId)));
         $template->setPlaceHolder('RemoveUserFromGroup', $this->generateLink(array('mainview' => 'group', 'groupview' => 'remove_user_from_groups', 'userid' => $userId)));
         $template->setPlaceHolder('AssignRoleToUser', $this->generateLink(array('mainview' => 'role', 'roleview' => 'add_user_to_roles', 'userid' => $userId)));
         $template->setPlaceHolder('RemoveRoleFromUser', $this->generateLink(array('mainview' => 'role', 'roleview' => 'remove_user_from_roles', 'userid' => $userId)));

         $buffer .= $template->transformTemplate();
      }

      $this->setPlaceHolder('UserList', $buffer);
   }

   private function getRoleNames(UmgtUser $user) {
      $roles = $this->getManager()->loadRolesWithUser($user);
      $roleNames = '<ul>';
      foreach ($roles as $role) {
         $roleNames .= '<li>' . $role->getDisplayName() . '</li>';
      }
      return $roleNames . '</ul>';
   }

   private function getGroupNames(UmgtUser $user) {
      $groups = $this->getManager()->loadGroupsWithUser($user);
      $groupNames = '<ul>';
      foreach ($groups as $group) {
         $groupNames .= '<li>' . $group->getDisplayName() . '</li>';
      }
      return $groupNames . '</ul>';
   }

}

?>