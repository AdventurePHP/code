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
 * @class umgt_add2group_controller
 *
 * Implements the controller to add a user to a group.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.12.2008<br />
 */
class umgt_add2group_controller extends umgt_base_controller {

   public function transformContent() {

      // init the form and load the relevant groups
      $userid = RequestHandler::getValue('userid');
      $formGroup = &$this->getForm('Group');
      $group = &$formGroup->getFormElementByName('Group');
      /* @var $group form_taglib_multiselect */
      $uM = &$this->getManager();
      $user = $uM->loadUserById($userid);
      $groups = $uM->loadGroupsNotWithUser($user);
      $count = count($groups);

      // display a note, if there are no groups to add the user to
      if ($count == 0) {
         $this->getTemplate('NoMoreGroups')->transformOnPlace();
         return;
      }

      // add the groups to the option field
      for ($i = 0; $i < $count; $i++) {
         $group->addOption($groups[$i]->getDisplayName(), $groups[$i]->getObjectId());
      }

      // handle the click event
      if ($formGroup->isSent() && $formGroup->isValid()) {

         $options = &$group->getSelectedOptions();
         $count = count($options);

         $newGroups = array();
         for ($i = 0; $i < $count; $i++) {
            $newGroup = new UmgtGroup();
            $newGroup->setObjectId($options[$i]->getAttribute('value'));
            $newGroups[] = $newGroup;
            unset($newGroup);
         }

         $uM->attachUser2Groups($user, $newGroups);
         HeaderManager::forward($this->generateLink(array('mainview' => 'user', 'userview' => '', 'userid' => '')));
      } else {
         $formGroup->transformOnPlace();
      }
   }

}

?>