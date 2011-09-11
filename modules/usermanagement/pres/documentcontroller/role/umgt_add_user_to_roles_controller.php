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
 * @class umgt_add_user_to_roles_controller
 *
 * Let's you add a user to one or more roles.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.09.2011<br />
 */
class umgt_add_user_to_roles_controller extends umgt_base_controller {

   public function transformContent() {

      $form = &$this->getForm('Roles');

      $uM = &$this->getManager();

      $user = $uM->loadUserByID(RequestHandler::getValue('userid'));
      $roles = $uM->loadRolesNotWithUser($user);

      if (count($roles) === 0) {
         $tmpl = &$this->getTemplate('NoMoreRoles');
         $tmpl->setPlaceHolder('User', $user->getProperty('DisplayName'));
         $tmpl->setPlaceHolder('UserViewLink', $this->generateLink(array('mainview' => 'user', 'groupview' => null, 'userid' => null)));
         $tmpl->transformOnPlace();
         return;
      }

      $rolesControl = &$form->getFormElementByName('Roles');
      /* @var $rolesControl form_taglib_multiselect */
      foreach ($roles as $role) {
         $rolesControl->addOption($role->getProperty('DisplayName'), $role->getObjectId());
      }

      if ($form->isSent() && $form->isValid()) {

         $options = &$rolesControl->getSelectedOptions();
         $additionalRoles = array();
         foreach ($options as $option) {
            /* @var $option select_taglib_option */
            $additionalRole = new UmgtRole();
            $additionalRole->setObjectId($option->getValue());
            $additionalRoles[] = $additionalRole;
            unset($additionalRole);
         }

         $uM->attachUser2Roles($user, $additionalRoles);

         // back to user main view
         HeaderManager::forward($this->generateLink(array('mainview' => 'user', 'roleview' => null, 'userid' => null)));

      } else {
         $form->transformOnPlace();
      }
   }

}

?>