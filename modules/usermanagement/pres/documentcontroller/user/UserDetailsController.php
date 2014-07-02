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
namespace APF\modules\usermanagement\pres\documentcontroller\user;

use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;
use APF\tools\request\RequestHandler;

/**
 * @package APF\modules\usermanagement\pres\documentcontroller\user
 * @class UserDetailsController
 *
 * Implements the controller to display a user's details.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.12.2008<br />
 */
class UserDetailsController extends UmgtBaseController {

   public function transformContent() {

      // load data
      $uM = & $this->getManager();
      $userId = RequestHandler::getValue('userid');
      $user = $uM->loadUserByID($userId);

      // display user data
      $this->getLabel('headline')->setPlaceHolder('display-name', $user->getDisplayName());

      $template = & $this->getTemplate('User');
      $template->setPlaceHolder('FirstName', $user->getFirstName());
      $template->setPlaceHolder('LastName', $user->getLastName());
      $template->setPlaceHolder('EMail', $user->getEMail());
      $template->transformOnPlace();

      // display groups
      $groups = $uM->loadGroupsWithUser($user);
      $iteratorGroups = & $this->getIterator('Groups');
      $iteratorGroups->fillDataContainer($groups);
      $iteratorGroups->transformOnPlace();

      // display roles
      $roles = $uM->loadRolesWithUser($user);
      $iteratorRoles = & $this->getIterator('Roles');
      $iteratorRoles->fillDataContainer($roles);
      $iteratorRoles->transformOnPlace();

      $proxies = $uM->loadAllVisibilityDefinitions($user);
      $iteratorProxies = & $this->getIterator('Proxies');
      $iteratorProxies->fillDataContainer($proxies);
      $iteratorProxies->transformOnPlace();

   }

}
