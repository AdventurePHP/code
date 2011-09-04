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
import('tools::http', 'HeaderManager');

/**
 * @package modules::usermanagement::pres::documentcontroller
 * @class umgt_edit_controller
 *
 * Implements the controller to edit a role.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class umgt_edit_controller extends umgt_base_controller {

   public function transformContent() {

      // get the current role id
      $roleId = RequestHandler::getValue('roleid');

      // initialize the form
      $form = &$this->getForm('RoleEdit');
      $permissionControl = &$form->getFormElementByName('Permission');
      /* @var $permissionControl form_taglib_multiselect */

      $hidden = &$form->getFormElementByName('roleid');
      $hidden->setAttribute('value', $roleId);

      $uM = &$this->getManager();
      $permissions = $uM->getPagedPermissionList();

      // load selected roles to be able to highlight them within the select field
      $role = $uM->loadRoleByID($roleId);
      $selectedPermissions = $role->loadRelatedObjects('Role2Permission');

      // fill multi-select field
      $count = count($permissions);
      for ($i = 0; $i < $count; $i++) {
         $permissionControl->addOption(
            $permissions[$i]->getProperty('DisplayName'),
            $permissions[$i]->getObjectId(),
            $this->isSelectedPermission($selectedPermissions, $permissions[$i])
         );
      }

      if ($form->isSent() == true) {

         if ($form->isValid() == true) {

            $displayName = &$form->getFormElementByName('DisplayName');
            $newlySelectedPermissions = &$permissionControl->getSelectedOptions();

            $role = new GenericDomainObject('Role');
            $role->setProperty('RoleID', $roleId);
            $role->setProperty('DisplayName', $displayName->getValue());

            foreach ($newlySelectedPermissions as $selectedPermission) {
               /* @var $option select_taglib_option */
               //$
            }

            $uM->saveRole($role);
            HeaderManager::forward($this->generateLink(array('mainview' => 'role', 'roleview' => '', 'roleid' => '')));

         } else {
            $form->transformOnPlace();
         }

      } else {

         // load group
         $role = $uM->loadRoleByID($roleId);

         // prefill form
         $displayName = &$form->getFormElementByName('DisplayName');
         $displayName->setAttribute('value', $role->getProperty('DisplayName'));

         // display form
         $form->transformOnPlace();

      }

   }

   private function isSelectedPermission(array $roles, GenericORMapperDataObject $currentRole) {
      foreach ($roles as $role) {
         /* @var $role GenericORMapperDataObject */
         if ($role->getObjectId() == $currentRole->getObjectId()) {
            return true;
         }
      }
      return false;
   }

}

?>