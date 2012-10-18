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
 * @package modules::usermanagement::pres::documentcontroller::group
 * @class umgt_group_remove_user_from_groups_controller
 *
 * Let's you remove a user from one or more groups starting at the user main view.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.09.2011<br />
 */
class umgt_group_remove_user_from_groups_controller extends umgt_base_controller {

   public function transformContent() {

      $form = &$this->getForm('Groups');

      $uM = &$this->getManager();

      $user = $uM->loadUserByID(RequestHandler::getValue('userid'));
      $groups = $uM->loadGroupsWithUser($user);

      if (count($groups) === 0) {
         $tmpl = &$this->getTemplate('NoGroups');
         $tmpl->getLabel('message-1')->setPlaceHolder('display-name', $user->getDisplayName());
         $tmpl->getLabel('message-2')->setPlaceHolder('user-view-link', $this->generateLink(array('mainview' => 'user', 'groupview' => null, 'userid' => null)));
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
         $groupsToRemove = array();
         foreach ($options as $option) {
            /* @var $option select_taglib_option */
            $groupToRemove = new UmgtGroup();
            $groupToRemove->setObjectId($option->getValue());
            $groupsToRemove[] = $groupToRemove;
            unset($groupToRemove);
         }

         $uM->detachUserFromGroups($user, $groupsToRemove);

         // back to user main view
         HeaderManager::forward($this->generateLink(array('mainview' => 'user', 'groupview' => null, 'userid' => null)));
      } else {
         $form->transformOnPlace();
      }
   }

}
