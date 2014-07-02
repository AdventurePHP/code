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
use APF\tools\http\HeaderManager;
use APF\tools\request\RequestHandler;

/**
 * Implements the controller to delete a role.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class RoleDeleteController extends UmgtBaseController {

   public function transformContent() {

      $roleId = RequestHandler::getValue('roleid');
      $uM = & $this->getManager();

      $role = $uM->loadRoleById($roleId);
      $this->getLabel('display-name')->setPlaceHolder('display-name', $role->getDisplayName());

      $formNo = & $this->getForm('RoleDelNo');
      $formYes = & $this->getForm('RoleDelYes');

      if ($formYes->isSent()) {

         $role = new UmgtRole();
         $role->setObjectId($roleId);
         $uM->deleteRole($role);
         HeaderManager::forward($this->generateLink(array('mainview' => 'role', 'roleview' => '', 'roleid' => '')));

      } elseif ($formNo->isSent()) {
         HeaderManager::forward($this->generateLink(array('mainview' => 'role', 'roleview' => '', 'roleid' => '')));
      } else {
         $formNo->transformOnPlace();
         $formYes->transformOnPlace();
      }

   }

}
