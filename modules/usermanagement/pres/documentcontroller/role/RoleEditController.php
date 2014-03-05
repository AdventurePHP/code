<?php
namespace APF\modules\usermanagement\pres\documentcontroller\role;

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
use APF\modules\usermanagement\biz\model\UmgtRole;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;
use APF\tools\http\HeaderManager;
use APF\tools\request\RequestHandler;

/**
 * @package APF\modules\usermanagement\pres\documentcontroller
 * @class RoleEditController
 *
 * Implements the controller to edit a role.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class RoleEditController extends UmgtBaseController {

   public function transformContent() {

      // get the current role id
      $roleId = RequestHandler::getValue('roleid');

      // initialize the form
      $form = & $this->getForm('RoleEdit');

      $hidden = & $form->getFormElementByName('roleid');
      $hidden->setAttribute('value', $roleId);

      $displayName = & $form->getFormElementByName('DisplayName');
      $description = & $form->getFormElementByName('Description');

      $uM = & $this->getManager();

      // load selected roles to be able to highlight them within the select field
      $role = $uM->loadRoleByID($roleId);

      if ($form->isSent() == true) {

         if ($form->isValid() == true) {

            $displayName = & $form->getFormElementByName('DisplayName');

            $role = new UmgtRole();
            $role->setObjectId($roleId);
            $role->setDisplayName($displayName->getValue());
            $role->setDescription($description->getValue());

            $uM->saveRole($role);
            HeaderManager::forward($this->generateLink(array('mainview' => 'role', 'roleview' => '', 'roleid' => '')));

         } else {
            $form->transformOnPlace();
         }

      } else {

         // pre-fill form
         $displayName->setValue($role->getDisplayName());
         $description->setValue($role->getDescription());

         $form->transformOnPlace();

      }

   }
}
