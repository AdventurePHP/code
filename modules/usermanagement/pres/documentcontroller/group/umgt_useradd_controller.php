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
 * @class umgt_useradd_controller
 *
 * Implements the controller to list the groups.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class umgt_useradd_controller extends umgt_base_controller {

   public function transformContent() {

      // initialize form
      $form = &$this->getForm('User');
      $userControl = &$form->getFormElementByName('User');

      /* @var $userControl form_taglib_multiselect */
      $groupId = RequestHandler::getValue('groupid');

      $uM = &$this->getManager();
      $group = $uM->loadGroupById($groupId);

      $users = $uM->loadUsersNotWithGroup($group);
      $count = count($users);

      // display hint, if group has associated all users
      if ($count == 0) {
         $template = &$this->getTemplate('NoMoreUser');
         $template->setPlaceHolder('Group', $group->getDisplayName());
         $template->setPlaceHolder('GroupViewLink', $this->generateLink(array('mainview' => 'group', 'groupview' => null, 'groupid' => null)));
         $template->transformOnPlace();
         return;
      }

      // fill multi-select field
      for ($i = 0; $i < $count; $i++) {
         $userControl->addOption($users[$i]->getDisplayName(), $users[$i]->getObjectId());
      }

      if ($form->isSent() && $form->isValid()) {

         $options = &$userControl->getSelectedOptions();
         $count = count($options);

         $newUsers = array();
         for ($i = 0; $i < $count; $i++) {
            $newUser = new UmgtUser();
            $newUser->setObjectId($options[$i]->getAttribute('value'));
            $newUsers[] = $newUser;
            unset($newUser);
         }

         $uM->attachUsers2Group($newUsers, $group);
         HeaderManager::forward($this->generateLink(array('mainview' => 'group', 'groupview' => '', 'groupid' => '')));

      } else {
         $form->transformOnPlace();
      }

   }

}

?>