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

use APF\modules\usermanagement\biz\model\UmgtUser;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;
use APF\tools\form\taglib\MultiSelectBoxTag;
use APF\tools\http\HeaderManager;

/**
 * Implements the controller to assign a role to a user.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 * Version 0.2, 29.12.2008 (Applied API change of the usermanagement manager)<br />
 */
class RoleAddUsersController extends UmgtBaseController {

   public function transformContent() {

      // get role id
      $roleId = self::getRequest()->getParameter('roleid');

      // initialize the form
      $form = & $this->getForm('User');
      $user = & $form->getFormElementByName('User');
      /* @var $user MultiSelectBoxTag */
      $uM = & $this->getManager();
      $role = $uM->loadRoleById($roleId);
      $users = $uM->loadUsersNotWithRole($role);
      $count = count($users);

      // display a hint, if a role already assigned to all users
      if ($count == 0) {
         $template = & $this->getTemplate('NoMoreUser');
         $template->getLabel('message-1')->setPlaceHolder('display-name', $role->getDisplayName());
         $template->getLabel('message-2')->setPlaceHolder('role-view-link', $this->generateLink(array('mainview' => 'role', 'roleview' => null, 'roleid' => null)));
         $template->transformOnPlace();

         return;
      }

      // fill multi-select field
      for ($i = 0; $i < $count; $i++) {
         $user->addOption($users[$i]->getLastName() . ', ' . $users[$i]->getFirstName(), $users[$i]->getObjectId());
      }

      // assign role to the desired users
      if ($form->isSent() && $form->isValid()) {

         $options = & $user->getSelectedOptions();
         $newUsers = array();

         for ($i = 0; $i < count($options); $i++) {
            $newUser = new UmgtUser();
            $newUser->setObjectId($options[$i]->getAttribute('value'));
            $newUsers[] = $newUser;
            unset($newUser);
         }

         $uM->attachUsersToRole($newUsers, $role);
         HeaderManager::forward($this->generateLink(array('mainview' => 'role', 'roleview' => '', 'roleid' => '')));

      } else {
         $form->transformOnPlace();
      }

   }

}
