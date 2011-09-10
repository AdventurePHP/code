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
 * @package modules::usermanagement::pres::documentcontroller::group
 * @class umgt_userrem_controller
 *
 * Implements the controller to remove a user from a group.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.12.2008<br />
 */
class umgt_userrem_controller extends umgt_base_controller {

   public function transformContent() {

      // initialize the form
      $form = &$this->getForm('User');
      $userControl = &$form->getFormElementByName('User');
      /* @var $userControl form_taglib_multiselect */
      $groupId = RequestHandler::getValue('groupid');
      $uM = &$this->getManager();
      $group = $uM->loadGroupById($groupId);
      $users = $uM->loadUsersWithGroup($group);
      $count = count($users);

      // display hint, if no user is assigned to this group
      if ($count == 0) {
         $template = &$this->getTemplate('NoMoreUser');
         $template->setPlaceHolder('Group', $group->getProperty('DisplayName'));
         $template->setPlaceHolder('GroupViewLink', $this->generateLink(array('mainview' => 'group', 'groupview' => null, 'groupid' => null)));
         $template->transformOnPlace();
         return;
      }

      // fill the multiselect field
      for ($i = 0; $i < $count; $i++) {
         $userControl->addOption($users[$i]->getProperty('LastName') . ', ' . $users[$i]->getProperty('FirstName'), $users[$i]->getProperty('UserID'));
      }

      // remove the desired users
      if ($form->isSent() && $form->isValid()) {

         $options = &$userControl->getSelectedOptions();

         $users = array();
         for ($i = 0; $i < count($options); $i++) {
            $userControl = new GenericDomainObject('User');
            $userControl->setProperty('UserID', $options[$i]->getAttribute('value'));
            $users[] = $userControl;
            unset($userControl);
            // end for
         }

         $group = new GenericDomainObject('Group');
         $group->setProperty('GroupID', $groupId);

         $uM->detachUsersFromGroup($users, $group);
         HeaderManager::forward($this->generateLink(array('mainview' => 'group', 'groupview' => null, 'groupid' => null)));

      } else {
         $form->transformOnPlace();
      }

   }

}

?>