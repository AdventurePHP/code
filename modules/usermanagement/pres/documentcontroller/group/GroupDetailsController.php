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
namespace APF\modules\usermanagement\pres\documentcontroller\group;

use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;

/**
 * Implements the controller to show a group's details.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class GroupDetailsController extends UmgtBaseController {

   public function transformContent() {

      // load data
      $uM = & $this->getManager();
      $groupId = $this->getRequest()->getParameter('groupid');
      $group = $uM->loadGroupByID($groupId);

      // display user data
      $this->getLabel('headline')->setPlaceHolder('display-name', $group->getDisplayName());

      // display users
      $users = $uM->loadUsersWithGroup($group);
      $usersIterator = & $this->getIterator('Users');
      $usersIterator->fillDataContainer($users);
      $usersIterator->transformOnPlace();

      // display roles
      $roles = $uM->loadRolesWithGroup($group);
      $iteratorRoles = & $this->getIterator('Roles');
      $iteratorRoles->fillDataContainer($roles);
      $iteratorRoles->transformOnPlace();

   }

}
