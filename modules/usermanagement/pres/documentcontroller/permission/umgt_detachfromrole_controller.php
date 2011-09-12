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
 * @package modules::usermanagement::pres::documentcontroller::permission
 * @class umgt_detachfromrole_controller
 *
 * Let's you detach permissions from a role.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 23.08.2011<br />
 */
class umgt_detachfromrole_controller extends umgt_base_controller {

   public function transformContent() {
      
      // get the current permission id
      $permissionId = RequestHandler::getValue('permissionid');

      // initialize the form
      $form = &$this->getForm('Role');
      $rolesControl = &$form->getFormElementByName('Roles');
      /* @var $rolesControl form_taglib_multiselect */

      $uM = &$this->getManager();
      $permission = $uM->loadPermissionByID($permissionId);
      $roles = $uM->loadRolesWithPermission($permission);
      $count = count($roles);

      // display a hint, if no roles are assigned to this permission
      if ($count == 0) {
         $template = &$this->getTemplate('NoMoreRoles');
         $template->transformOnPlace();
         return true;
      }

      $form->setPlaceHolder('permission-name', $permission->getDisplayName());

      // fill the multiselect field
      for ($i = 0; $i < $count; $i++) {
         $rolesControl->addOption($roles[$i]->getDisplayName(), $roles[$i]->getObjectId());
      }

      // detach permission from the roles
      if ($form->isSent() && $form->isValid()) {

         $options = &$rolesControl->getSelectedOptions();
         $newRoles = array();
         for ($i = 0; $i < count($options); $i++) {
            $newRole = new UmgtRole();
            $newRole->setObjectId($options[$i]->getAttribute('value'));
            $newRoles[] = $newRole;
            unset($newRole);
         }
         $uM->detachPermissionFromRoles($permission, $newRoles);
         HeaderManager::forward($this->generateLink(array('mainview' => 'permission', 'permissionview' => '', 'permissionid' => '')));

      } else {
         $form->transformOnPlace();
      }

   }

}

?>