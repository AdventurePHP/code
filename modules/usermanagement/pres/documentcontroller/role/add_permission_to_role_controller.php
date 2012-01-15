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
 * @package modules::usermanagement::pres::documentcontroller::role
 * @class add_permission_to_role_controller
 *
 * Let's you add permissions to a role.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.09.2011<br />
 */
class add_permission_to_role_controller extends umgt_base_controller {

   public function transformContent() {

      $form = &$this->getForm('Permissions');
      $uM = &$this->getManager();

      $role = $uM->loadRoleByID(RequestHandler::getValue('roleid'));
      $form->setPlaceHolder('RoleName', $role->getDisplayName());

      $permissions = $uM->loadPermissionsNotWithRole($role);

      if (count($permissions) === 0) {
         $template = &$this->getTemplate('NoMorePermissions');
         $template->setPlaceHolder('Role', $role->getDisplayName());
         $template->setPlaceHolder('RoleViewLink', $this->generateLink(array('mainview' => 'role', 'roleview' => null, 'roleid' => null)));
         $template->transformOnPlace();
         return;
      }

      $permissionControl = &$form->getFormElementByName('Permissions');
      /* @var $permissionControl form_taglib_multiselect */

      foreach ($permissions as $permission) {
         $permissionControl->addOption($permission->getDisplayName(), $permission->getObjectId());
      }

      if ($form->isSent() && $form->isValid()) {

         $options = $permissionControl->getSelectedOptions();
         $permissionsToAdd = array();
         foreach ($options as $option) {
            /* @var $option select_taglib_option */
            $permissionToAdd = new UmgtPermission();
            $permissionToAdd->setObjectId($option->getValue());
            $permissionsToAdd[] = $permissionToAdd;
            unset($permissionToAdd);
         }

         $uM->attachPermissions2Role($permissionsToAdd, $role);
         HeaderManager::forward($this->generateLink(array('mainview' => 'role', 'roleview' => null, 'roleid' => null)));

      } else {
         $form->transformOnPlace();
      }


   }

}

?>