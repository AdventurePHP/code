<?php
namespace APF\modules\usermanagement\pres\documentcontroller\group;

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
use APF\modules\usermanagement\biz\model\UmgtGroup;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;

/**
 * Implements the controller to list the groups.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class GroupListController extends UmgtBaseController {

   public function transformContent() {

      // load group list
      $groups = $this->getManager()->getPagedGroupList();

      // display group list
      $buffer = (string) '';
      $template = & $this->getTemplate('Group');

      foreach ($groups as $group) {
         $groupId = $group->getObjectId();

         $template->setPlaceHolder('DisplayName', $group->getDisplayName());
         $template->setPlaceHolder('Users', $this->getUsers($group));
         $template->setPlaceHolder('Roles', $this->getRoles($group));

         $template->setPlaceHolder('group_edit', $this->generateLink(array('mainview' => 'group', 'groupview' => 'edit', 'groupid' => $groupId)));
         $template->setPlaceHolder('group_details', $this->generateLink(array('mainview' => 'group', 'groupview' => 'details', 'groupid' => $groupId)));
         $template->setPlaceHolder('group_delete', $this->generateLink(array('mainview' => 'group', 'groupview' => 'delete', 'groupid' => $groupId)));
         $template->setPlaceHolder('AddUsersToGroup', $this->generateLink(array('mainview' => 'group', 'groupview' => 'useradd', 'groupid' => $groupId)));
         $template->setPlaceHolder('RemoveUsersFromGroup', $this->generateLink(array('mainview' => 'group', 'groupview' => 'userrem', 'groupid' => $groupId)));
         $template->setPlaceHolder('AddRolesToGroup', $this->generateLink(array('mainview' => 'role', 'roleview' => 'add_group_to_roles', 'groupid' => $groupId)));
         $template->setPlaceHolder('RemoveRolesFromGroup', $this->generateLink(array('mainview' => 'role', 'roleview' => 'remove_group_from_roles', 'groupid' => $groupId)));

         $buffer .= $template->transformTemplate();
      }

      $this->setPlaceHolder('Grouplist', $buffer);

   }

   private function getUsers(UmgtGroup $group) {
      $users = $this->getManager()->loadUsersWithGroup($group);

      if (count($users) < 1) {
         return '';
      }

      $userNames = '<ul>';
      foreach ($users as $user) {
         $userNames .= '<li>' . $user->getDisplayName() . '</li>';
      }

      return $userNames . '</ul>';
   }

   private function getRoles(UmgtGroup $group) {
      $roles = $this->getManager()->loadRolesWithGroup($group);

      if (count($roles) < 1) {
         return '';
      }

      $roleNames = '<ul>';
      foreach ($roles as $role) {
         $roleNames .= '<li>' . $role->getDisplayName() . '</li>';
      }

      return $roleNames . '</ul>';
   }
}
