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
import('tools::request', 'RequestHandler');
import('tools::http', 'HeaderManager');

/**
 * @package modules::usermanagement::pres::documentcontroller::group
 * @class umgt_group_add_role_to_groups_controller
 *
 * Let's you add a role to one or more groups starting at the role main view.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 07.09.2011<br />
 */
class umgt_group_add_role_to_groups_controller extends umgt_base_controller {

   public function transformContent() {

      $form = &$this->getForm('Groups');

      $uM = &$this->getManager();

      $role = $uM->loadRoleByID(RequestHandler::getValue('roleid'));
      $groups = $uM->loadGroupsNotWithRole($role);

      if (count($groups) === 0) {
         $tmpl = &$this->getTemplate('NoMoreGroups');
         $tmpl->setPlaceHolder('Role', $role->getDisplayName());
         $tmpl->setPlaceHolder('RoleViewLink', $this->generateLink(array('mainview' => 'role', 'groupview' => null, 'roleid' => null)));
         $tmpl->transformOnPlace();
         return;
      }

      $groupsControl = &$form->getFormElementByName('Groups');
      /* @var $groupsControl form_taglib_multiselect */
      foreach ($groups as $group) {
         $groupsControl->addOption($group->getDisplayName(), $group->getObjectId());
      }

      if ($form->isSent() && $form->isValid()) {

         $options = &$groupsControl->getSelectedOptions();
         $additionalGroups = array();
         foreach ($options as $option) {
            /* @var $option select_taglib_option */
            $additionalGroup = new UmgtGroup();
            $additionalGroup->setObjectId($option->getValue());
            $additionalGroups[] = $additionalGroup;
            unset($additionalGroup);
         }

         $uM->attachRoleToGroups($role, $additionalGroups);

         // back to user main view
         HeaderManager::forward($this->generateLink(array('mainview' => 'role', 'groupview' => null, 'roleid' => null)));
      } else {
         $form->transformOnPlace();
      }
   }

}

?>