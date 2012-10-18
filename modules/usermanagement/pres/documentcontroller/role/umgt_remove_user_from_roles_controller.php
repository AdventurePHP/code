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
 * @class umgt_remove_user_from_roles_controller
 *
 * Let's you remove a user from one or more roles.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.09.2011<br />
 */
class umgt_remove_user_from_roles_controller extends umgt_base_controller {

   public function transformContent() {

      $form = &$this->getForm('Roles');

      $uM = &$this->getManager();

      $user = $uM->loadUserByID(RequestHandler::getValue('userid'));
      $roles = $uM->loadRolesWithUser($user);

      if (count($roles) === 0) {
         $tmpl = &$this->getTemplate('NoMoreRoles');
         $tmpl->getLabel('message-1')->setPlaceHolder('display-name', $user->getDisplayName());
         $tmpl->getLabel('message-2')->setPlaceHolder('user-view-link', $this->generateLink(array('mainview' => 'user', 'groupview' => null, 'userid' => null)));
         $tmpl->transformOnPlace();
         return;
      }

      $rolesControl = &$form->getFormElementByName('Roles');
      /* @var $rolesControl form_taglib_multiselect */
      foreach ($roles as $role) {
         $rolesControl->addOption($role->getDisplayName(), $role->getObjectId());
      }

      if ($form->isSent() && $form->isValid()) {

         $options = &$rolesControl->getSelectedOptions();
         $rolesToRemove = array();
         foreach ($options as $option) {
            /* @var $option select_taglib_option */
            $roleToRemove = new UmgtRole();
            $roleToRemove->setObjectId($option->getValue());
            $rolesToRemove[] = $roleToRemove;
            unset($roleToRemove);
         }

         $uM->detachUserFromRoles($user, $rolesToRemove);

         // back to user main view
         HeaderManager::forward($this->generateLink(array('mainview' => 'user', 'roleview' => null, 'userid' => null)));
      } else {
         $form->transformOnPlace();
      }
   }

}
