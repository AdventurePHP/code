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
import('modules::usermanagement::pres::documentcontroller', 'UmgtBaseController');

/**
 * @package modules::usermanagement::pres::documentcontroller::group
 * @class umgt_group_remove_role_from_groups_controller
 *
 * Let's you remove a role from one or more groups starting at the role main view.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.09.2011<br />
 */
class umgt_group_remove_role_from_groups_controller extends UmgtBaseController {

   public function transformContent() {

      $form = &$this->getForm('Groups');

      $uM = &$this->getManager();

      $role = $uM->loadRoleByID(RequestHandler::getValue('roleid'));
      $groups = $uM->loadGroupsWithRole($role);

      if (count($groups) === 0) {
         $tmpl = &$this->getTemplate('NoMoreGroups');
         $tmpl->getLabel('message-1')->setPlaceHolder('display-name', $role->getDisplayName());
         $tmpl->getLabel('message-2')->setPlaceHolder('role-view-link', $this->generateLink(array('mainview' => 'role', 'groupview' => null, 'roleid' => null)));
         $tmpl->transformOnPlace();
         return;
      }

      $groupsControl = &$form->getFormElementByName('Groups');
      /* @var $groupsControl MultiSelectBoxTag */
      foreach ($groups as $group) {
         $groupsControl->addOption($group->getDisplayName(), $group->getObjectId());
      }

      if ($form->isSent() && $form->isValid()) {

         $options = &$groupsControl->getSelectedOptions();
         $additionalGroups = array();
         foreach ($options as $option) {
            /* @var $option SelectBoxOptionTag */
            $additionalGroup = new UmgtGroup();
            $additionalGroup->setObjectId($option->getValue());
            $additionalGroups[] = $additionalGroup;
            unset($additionalGroup);
         }

         $uM->detachRoleToGroups($role, $additionalGroups);

         // back to user main view
         HeaderManager::forward($this->generateLink(array('mainview' => 'role', 'groupview' => null, 'roleid' => null)));
      } else {
         $form->transformOnPlace();
      }
   }

}
