<?php
namespace APF\modules\usermanagement\pres\documentcontroller\group;

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
use APF\modules\usermanagement\biz\model\UmgtGroup;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;
use APF\tools\form\taglib\MultiSelectBoxTag;
use APF\tools\form\taglib\SelectBoxOptionTag;
use APF\tools\http\HeaderManager;
use APF\tools\request\RequestHandler;

/**
 * @package APF\modules\usermanagement\pres\documentcontroller\group
 * @class GroupAddUserToGroupsController
 *
 * Let's you add a user to one or more groups starting at the user main view.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.09.2011<br />
 */
class GroupAddUserToGroupsController extends UmgtBaseController {

   public function transformContent() {

      $form = &$this->getForm('Groups');

      $uM = &$this->getManager();

      $user = $uM->loadUserByID(RequestHandler::getValue('userid'));
      $groups = $uM->loadGroupsNotWithUser($user);

      if (count($groups) === 0) {
         $tmpl = &$this->getTemplate('NoMoreGroups');
         $tmpl->getLabel('message-1')->setPlaceHolder('display-name', $user->getDisplayName());
         $tmpl->getLabel('message-2')->setPlaceHolder('user-view-link', $this->generateLink(array('mainview' => 'user', 'groupview' => null, 'userid' => null)));
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

         $uM->attachUser2Groups($user, $additionalGroups);

         // back to user main view
         HeaderManager::forward($this->generateLink(array('mainview' => 'user', 'groupview' => null, 'userid' => null)));

      } else {
         $form->transformOnPlace();
      }
   }

}
