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
import('tools::request', 'RequestHandler');
import('modules::usermanagement::pres::documentcontroller', 'umgt_base_controller');

/**
 * @package modules::usermanagement::pres::documentcontroller::user
 * @class umgt_details_controller
 *
 * Implements the controller to display a user's details.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.12.2008<br />
 */
class umgt_details_controller extends umgt_base_controller {

   public function transformContent() {

      // load data
      $uM = &$this->getManager();
      $userId = RequestHandler::getValue('userid');
      $user = $uM->loadUserByID($userId);

      // display user data
      $templateUser = &$this->getTemplate('User');
      $templateUser->setPlaceHolder('FirstName', $user->getProperty('FirstName'));
      $templateUser->setPlaceHolder('LastName', $user->getProperty('LastName'));
      $templateUser->setPlaceHolder('EMail', $user->getProperty('EMail'));
      $templateUser->transformOnPlace();

      // display groups
      $Groups = $uM->loadGroupsWithUser($user);
      $iteratorGroups = &$this->getIterator('Groups');
      $iteratorGroups->fillDataContainer($Groups);
      $iteratorGroups->transformOnPlace();

      // display roles
      $Roles = $uM->loadRolesWithUser($user);
      $iteratorRoles = &$this->getIterator('Roles');
      $iteratorRoles->fillDataContainer($Roles);
      $iteratorRoles->transformOnPlace();

      $proxies = $uM->getPagedVisibilityDefinitionList();
      $iteratorProxies = &$this->getIterator('Proxies');
      $iteratorProxies->fillDataContainer($proxies);
      $iteratorProxies->transformOnPlace();

   }

}

?>