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
 * @package modules::usermanagement::pres::documentcontroller
 * @class umgt_ass2role_controller
 *
 * Implements the controller to assign a permission to a role.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 19.08.2011<br />
 */
class umgt_ass2role_controller extends umgt_base_controller {

   public function transformContent() {

      // get permission id
      $permissionId = RequestHandler::getValue('permissionid');

      // initialize the form
      $form = &$this->getForm('Role');
      $permissionControl = &$form->getFormElementByName('Permission');
      /* @var $permissionControl form_taglib_multiselect */
      $uM = &$this->getManager();
      $permission = $uM->loadPermissionByID($permissionId);
      $roles = $uM->loadRolesNotWithPermission($permission);

      $count = count($roles);

      // display a hint, if a role already assigned to all users
      if ($count == 0) {
         $template = &$this->getTemplate('NoMorePermissions');
         $template->transformOnPlace();
         return true;
      }

      // display permission name
      $form->setPlaceHolder('permission-name', $permission->getDisplayName());

      // fill multi-select field
      for ($i = 0; $i < $count; $i++) {
         $permissionControl->addOption($roles[$i]->getDisplayName(), $roles[$i]->getObjectId());
      }

      // assign permission to the desired roles
      if ($form->isSent() && $form->isValid()) {

         $options = &$permissionControl->getSelectedOptions();
         $newRoles = array();

         for ($i = 0; $i < count($options); $i++) {
            $newRole = new UmgtRole();
            $newRole->setObjectId($options[$i]->getAttribute('value'));
            $newRoles[] = $newRole;
            unset($newRole);
         }

         $uM->attachPermission2Roles($permission, $newRoles);
         HeaderManager::forward($this->generateLink(array('mainview' => 'permission', 'permissionview' => '', 'permissionid' => '')));

      } else {
         $form->transformOnPlace();
      }

   }

}

?>